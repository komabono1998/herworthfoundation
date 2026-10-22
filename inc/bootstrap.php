<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('DATA_DIR', ROOT . '/data');
define('CONTENT_FILE', DATA_DIR . '/content.json');
define('MESSAGES_FILE', DATA_DIR . '/messages.json');

function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function read_json(string $file): array
{
    if (!is_file($file)) return [];
    $d = json_decode((string)file_get_contents($file), true);
    return is_array($d) ? $d : [];
}

function write_json(string $file, array $data): bool
{
    if (!is_dir(dirname($file))) mkdir(dirname($file), 0755, true);
    $tmp = $file . '.tmp';
    $ok = file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    return $ok !== false && rename($tmp, $file);
}

// Saved values win; lists are replaced whole so deleted items stay deleted.
function merge_content(array $defaults, array $saved): array
{
    foreach ($saved as $k => $v) {
        if (is_array($v) && isset($defaults[$k]) && is_array($defaults[$k]) && !array_is_list($defaults[$k])) {
            $defaults[$k] = merge_content($defaults[$k], $v);
        } else {
            $defaults[$k] = $v;
        }
    }
    return $defaults;
}

function content(): array
{
    static $c = null;
    if ($c === null) $c = merge_content(require __DIR__ . '/defaults.php', read_json(CONTENT_FILE));
    return $c;
}

/** Raw value from content: v('home','title') or v('site','email'). */
function v(string $section, string $key)
{
    return content()[$section][$key] ?? '';
}

/** Escaped string value. */
function t(string $section, string $key): string
{
    return e((string)v($section, $key));
}

/** Blank-line separated text to <p> tags. */
function paras(string $text): string
{
    $out = '';
    foreach (preg_split('/\R{2,}/', trim($text)) as $p) {
        if (trim($p) !== '') $out .= '<p>' . nl2br(e(trim($p))) . '</p>';
    }
    return $out;
}

function safe_upload_path(string $p): string
{
    return preg_match('#^uploads/[A-Za-z0-9._-]+$#', $p) ? $p : '';
}

/** Photo if one was uploaded, otherwise the coloured placeholder tile with a caption. */
function media(string $image, string $caption, string $class = ''): string
{
    $image = safe_upload_path($image);
    if ($image !== '') {
        return '<img class="photo" src="' . e($image) . '" alt="' . e($caption) . '" loading="lazy">';
    }
    return '<div class="art ' . e($class) . '">' . e($caption) . '</div>';
}

function digits(string $s): string
{
    return preg_replace('/\D+/', '', $s) ?? '';
}

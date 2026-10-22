<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('DATA_DIR', ROOT . '/data');
define('CONTENT_FILE', DATA_DIR . '/content.json');
define('MESSAGES_FILE', DATA_DIR . '/messages.json');
define('APPS_FILE', DATA_DIR . '/applications.json');
define('CV_DIR', DATA_DIR . '/cvs');

date_default_timezone_set('Africa/Kampala');

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
    return preg_match('#^(uploads|assets/img)/[A-Za-z0-9._-]+$#', $p) ? $p : '';
}

/** Photo if one was uploaded, otherwise the coloured placeholder tile with a caption. */
function media(string $image, string $caption, string $class = ''): string
{
    $image = safe_upload_path($image);
    if ($image !== '') {
        return '<img class="photo ' . e($class) . '" src="' . e($image) . '" alt="' . e($caption) . '" loading="lazy">';
    }
    return '<div class="art ' . e($class) . '">' . e($caption) . '</div>';
}

function digits(string $s): string
{
    return preg_replace('/\D+/', '', $s) ?? '';
}

/* ---------- openings (volunteer & career) ---------- */
function opp_is_open(array $o): bool
{
    if (($o['status'] ?? 'open') !== 'open') return false;
    $d = (string)($o['deadline'] ?? '');
    return $d === '' || $d >= date('Y-m-d');
}

function opportunities(?string $type = null, bool $openOnly = true): array
{
    $out = [];
    foreach (v('opportunities', 'items') ?: [] as $o) {
        if (empty($o['id']) || empty($o['title'])) continue;
        if ($type !== null && ($o['type'] ?? '') !== $type) continue;
        if ($openOnly && !opp_is_open($o)) continue;
        $out[] = $o;
    }
    return $out;
}

function find_opportunity(string $id): ?array
{
    foreach (opportunities(null, false) as $o) if ($o['id'] === $id) return $o;
    return null;
}

/* ---------- events ---------- */
function event_ts(array $e): int
{
    $d = (string)($e['date'] ?? '');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return 0;
    $t = preg_match('/^\d{2}:\d{2}$/', (string)($e['time'] ?? '')) ? $e['time'] : '00:00';
    return (int)strtotime("$d $t");
}

/** [upcoming soonest-first, past latest-first]. An event stays "upcoming" for its whole day. */
function events_split(): array
{
    $up = $past = [];
    foreach (v('events', 'items') ?: [] as $e) {
        $ts = event_ts($e);
        if (!$ts || empty($e['title'])) continue;
        $e['_ts'] = $ts;
        if (date('Y-m-d', $ts) < date('Y-m-d')) $past[] = $e; else $up[] = $e;
    }
    usort($up, fn($a, $b) => $a['_ts'] <=> $b['_ts']);
    usort($past, fn($a, $b) => $b['_ts'] <=> $a['_ts']);
    return [$up, $past];
}

<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/bootstrap.php';

define('ADMIN_FILE', DATA_DIR . '/admin.json');
define('LOGIN_LOG', DATA_DIR . '/login.json');

session_name('hwf_admin');
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Strict', 'use_strict_mode' => 1]);
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

/* ---------- auth ---------- */
function admin_exists(): bool { return isset(read_json(ADMIN_FILE)['hash']); }
function logged_in(): bool { return !empty($_SESSION['admin']); }
function require_login(): void
{
    if (!admin_exists() || !logged_in()) { header('Location: login.php'); exit; }
}
function csrf(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function csrf_check(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Invalid or expired form. Go back, refresh and try again.');
    }
}
function flash(string $msg, string $type = 'ok'): void { $_SESSION['flash'][] = [$type, $msg]; }
function flashes(): string
{
    $out = '';
    foreach ($_SESSION['flash'] ?? [] as [$type, $msg]) $out .= '<div class="alert ' . e($type) . '">' . e($msg) . '</div>';
    unset($_SESSION['flash']);
    return $out;
}

/* ---------- schema ---------- */
function F(string $label, string $type = 'text', array $x = []): array { return ['label' => $label, 'type' => $type] + $x; }
function schema(): array { static $s = null; return $s ??= require __DIR__ . '/schema.php'; }

/* ---------- uploads ---------- */
function upfile(array $path): ?array
{
    $f = $_FILES['up'] ?? null;
    if (!$f) return null;
    $out = [];
    foreach (['tmp_name', 'error', 'size'] as $p) {
        $x = $f[$p];
        foreach ($path as $seg) {
            if (!is_array($x) || !isset($x[$seg])) return null;
            $x = $x[$seg];
        }
        $out[$p] = $x;
    }
    return $out;
}

function store_image(?array $f): ?string
{
    if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 5 * 1024 * 1024) {
        flash('An image was skipped: it must be under 5 MB.', 'err');
        return null;
    }
    $info = @getimagesize($f['tmp_name']);
    $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp'][$info[2] ?? 0] ?? null;
    if (!$ext) {
        flash('An image was skipped: only JPG, PNG, GIF or WEBP files are allowed.', 'err');
        return null;
    }
    $dir = ROOT . '/uploads';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    return move_uploaded_file($f['tmp_name'], "$dir/$name") ? "uploads/$name" : null;
}

/* ---------- reading submitted forms ---------- */
function has_content(array $row): bool
{
    foreach ($row as $v) if (is_array($v) ? $v !== [] : trim((string)$v) !== '') return true;
    return false;
}

function rm_name(string $n): string { return substr($n, 0, -1) . '_rm]'; }

function collect(array $fields, array $post, array $path): array
{
    $out = [];
    foreach ($fields as $k => $s) {
        $type = $s['type'];
        if ($type === 'heading') continue;
        $raw = $post[$k] ?? null;
        switch ($type) {
            case 'lines':
                $lines = preg_split('/\R/', (string)$raw) ?: [];
                $out[$k] = array_values(array_filter(array_map(fn($l) => mb_substr(trim($l), 0, 300), $lines), 'strlen'));
                break;
            case 'textarea':
                $out[$k] = mb_substr(trim((string)$raw), 0, 5000);
                break;
            case 'image':
                $cur = safe_upload_path((string)$raw);
                if (!empty($post[$k . '_rm'])) $cur = '';
                $new = store_image(upfile([...$path, $k]));
                $out[$k] = $new ?? $cur;
                break;
            case 'list':
                $items = [];
                foreach ((array)$raw as $i => $item) {
                    if (!is_array($item)) continue;
                    $row = collect($s['fields'], $item, [...$path, $k, $i]);
                    if (has_content($row)) $items[] = $row;
                }
                $out[$k] = $items;
                break;
            case 'email':
                $val = trim((string)$raw);
                if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) { flash("“{$s['label']}” is not a valid email, so it was cleared.", 'err'); $val = ''; }
                $out[$k] = $val;
                break;
            case 'url':
                $val = trim((string)$raw);
                if ($val !== '' && !preg_match('#^https?://#i', $val)) { flash("“{$s['label']}” must start with http:// or https://, so it was cleared.", 'err'); $val = ''; }
                $out[$k] = mb_substr($val, 0, 500);
                break;
            default:
                $out[$k] = mb_substr(trim((string)$raw), 0, 300);
        }
    }
    return $out;
}

/* ---------- drawing form fields ---------- */
function render_field(string $n, string $u, array $s, $val): void
{
    $type = $s['type'];
    $label = e($s['label']);
    $ph = e($s['ph'] ?? '');
    $hint = isset($s['hint']) ? '<small>' . e($s['hint']) . '</small>' : '';

    if ($type === 'heading') { echo "<h3 class=\"group\">$label</h3>"; return; }

    if ($type === 'list') {
        echo "<div class=\"fld\"><span class=\"lbl\">$label</span>$hint<div class=\"list\">";
        $rows = is_array($val) ? $val : [];
        foreach ($rows as $i => $item) list_item($n, $u, $s, (string)$i, is_array($item) ? $item : []);
        echo '</div>';
        ob_start();
        list_item($n, $u, $s, '__I__', []);
        echo '<template>' . ob_get_clean() . '</template>';
        echo '<button type="button" class="btn small add">+ Add ' . e($s['item'] ?? 'item') . '</button></div>';
        return;
    }

    if ($type === 'image') {
        $cur = safe_upload_path((string)$val);
        echo "<div class=\"fld\"><span class=\"lbl\">$label</span>$hint<div class=\"imgfld\">";
        echo $cur !== '' ? '<img src="../' . e($cur) . '" alt="">' : '<div class="noimg">No image</div>';
        echo '<div><input type="hidden" name="' . e($n) . '" value="' . e($cur) . '">';
        echo '<input type="file" name="' . e($u) . '" accept="image/jpeg,image/png,image/gif,image/webp">';
        if ($cur !== '') echo '<label class="chk"><input type="checkbox" name="' . e(rm_name($n)) . '" value="1"> Remove image</label>';
        echo '</div></div></div>';
        return;
    }

    echo "<div class=\"fld\"><label>$label";
    if ($type === 'textarea' || $type === 'lines') {
        $text = $type === 'lines' ? implode("\n", (array)$val) : (string)$val;
        $rows = $s['rows'] ?? ($type === 'lines' ? 4 : 5);
        echo '<textarea name="' . e($n) . '" rows="' . $rows . '" placeholder="' . $ph . '">' . e($text) . '</textarea>';
        if ($type === 'lines') $hint = '<small>One per line.' . (isset($s['hint']) ? ' ' . e($s['hint']) : '') . '</small>';
    } else {
        $htmlType = in_array($type, ['email', 'url', 'number'], true) ? $type : 'text';
        echo '<input type="' . $htmlType . '" name="' . e($n) . '" value="' . e((string)$val) . '" placeholder="' . $ph . '">';
    }
    echo "</label>$hint</div>";
}

function list_item(string $n, string $u, array $s, string $i, array $item): void
{
    echo '<div class="item"><button type="button" class="rm" title="Remove" aria-label="Remove">×</button>';
    foreach ($s['fields'] as $k => $sub) {
        render_field("{$n}[$i][$k]", "{$u}[$i][$k]", $sub, $item[$k] ?? ($sub['type'] === 'lines' ? [] : ''));
    }
    echo '</div>';
}

function unread_count(): int
{
    return count(array_filter(read_json(MESSAGES_FILE), fn($m) => empty($m['read'])));
}

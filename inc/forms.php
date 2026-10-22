<?php
require_once __DIR__ . '/layout.php';

const CV_MAX_BYTES = 5 * 1024 * 1024;

/** Tags + summary card for an opening, linking to its apply page. */
function opening_card(array $o): string
{
    $tags = '';
    foreach ([$o['location'] ?? '', $o['commitment'] ?? ''] as $t) {
        if ($t !== '') $tags .= '<span>' . e($t) . '</span>';
    }
    if (!empty($o['deadline'])) {
        $ts = strtotime($o['deadline']);
        if ($ts) $tags .= '<span>Apply by ' . date('j M Y', $ts) . '</span>';
    }
    return '<article class="card opening">'
        . ($tags !== '' ? '<div class="tags">' . $tags . '</div>' : '')
        . '<h3>' . e($o['title']) . '</h3>'
        . (($o['summary'] ?? '') !== '' ? '<p>' . e($o['summary']) . '</p>' : '')
        . '<p><a class="btn small" href="apply.php?o=' . e($o['id']) . '">View &amp; apply</a></p></article>';
}

/**
 * Validate and store an uploaded CV.
 * @return array{0:?string,1:?string,2:?string} [stored file name, original name, error message]
 */
function save_cv(?array $f, bool $required): array
{
    if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [null, null, $required ? 'Please attach your CV (PDF or Word).' : null];
    }
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > CV_MAX_BYTES) {
        return [null, null, 'Your CV must be a file under 5 MB.'];
    }
    $ext = strtolower(pathinfo((string)$f['name'], PATHINFO_EXTENSION));
    $head = (string)file_get_contents($f['tmp_name'], false, null, 0, 8);
    $ok = match ($ext) {
        'pdf'  => str_starts_with($head, '%PDF'),
        'docx' => str_starts_with($head, "PK\x03\x04"),
        'doc'  => str_starts_with($head, "\xD0\xCF\x11\xE0"),
        default => false,
    };
    if (!$ok) return [null, null, 'Please upload your CV as a PDF, DOC or DOCX file.'];

    if (!is_dir(CV_DIR)) {
        mkdir(CV_DIR, 0755, true);
        file_put_contents(CV_DIR . '/.htaccess', "Require all denied\n");
    }
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], CV_DIR . '/' . $name)) return [null, null, 'Sorry, your CV could not be saved. Please try again.'];
    $orig = preg_replace('/[^A-Za-z0-9._ -]/', '_', basename((string)$f['name']));
    return [$name, mb_substr((string)$orig, 0, 120), null];
}

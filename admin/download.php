<?php
require __DIR__ . '/lib.php';
require_login();

$id = (string)($_GET['id'] ?? '');
foreach (read_json(APPS_FILE) as $a) {
    if (($a['id'] ?? '') !== $id || empty($a['cv_file'])) continue;
    $path = CV_DIR . '/' . basename((string)$a['cv_file']);
    if (!is_file($path)) break;
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $name = preg_replace('/[^A-Za-z0-9._-]+/', '_', 'CV_' . ($a['name'] ?? 'applicant')) . '.' . $ext;
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('Content-Length: ' . filesize($path));
    header('X-Content-Type-Options: nosniff');
    readfile($path);
    exit;
}
http_response_code(404);
echo 'CV not found.';

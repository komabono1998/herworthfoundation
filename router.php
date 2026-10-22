<?php
// Router for PHP's built-in dev server (php -S ... router.php): keeps private folders private like .htaccess does on Apache.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
if (preg_match('#^/(data|inc|tools)(/|$)#', $path) || preg_match('#/\.(?!well-known)#', $path)) {
    http_response_code(403);
    exit('Forbidden');
}
return false;

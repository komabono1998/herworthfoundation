<?php
// Create or reset the admin login from the command line:
//   php tools/set-admin.php admin@example.org "password"
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../inc/bootstrap.php';
[, $email, $password] = $argv + [null, null, null];
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || (string)$password === '') {
    fwrite(STDERR, "Usage: php tools/set-admin.php email password\n");
    exit(1);
}
write_json(DATA_DIR . '/admin.json', ['email' => $email, 'hash' => password_hash($password, PASSWORD_DEFAULT), 'created' => date('c')]);
write_json(DATA_DIR . '/login.json', []);
echo "Admin login saved for $email\n";

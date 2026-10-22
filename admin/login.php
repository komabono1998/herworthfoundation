<?php
require __DIR__ . '/lib.php';

$setup = !admin_exists();
$error = '';

if (logged_in() && !$setup) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $pw = (string)($_POST['password'] ?? '');

    if ($setup) {
        $email = trim((string)($_POST['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Enter a valid email address.';
        elseif (strlen($pw) < 10) $error = 'Use at least 10 characters.';
        elseif ($pw !== ($_POST['confirm'] ?? '')) $error = 'The passwords do not match.';
        else {
            write_json(ADMIN_FILE, ['email' => $email, 'hash' => password_hash($pw, PASSWORD_DEFAULT), 'created' => date('c')]);
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            header('Location: index.php'); exit;
        }
    } else {
        $log = array_filter(read_json(LOGIN_LOG), fn($t) => $t > time() - 900);
        if (count($log) >= 5) {
            $error = 'Too many attempts. Try again in 15 minutes.';
        } elseif (strcasecmp(trim((string)($_POST['email'] ?? '')), (string)(read_json(ADMIN_FILE)['email'] ?? '')) === 0
                  && password_verify($pw, (string)read_json(ADMIN_FILE)['hash'])) {
            write_json(LOGIN_LOG, []);
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            header('Location: index.php'); exit;
        } else {
            $log[] = time();
            write_json(LOGIN_LOG, array_values($log));
            sleep(1);
            $error = 'Incorrect email or password.';
        }
    }
}
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex">
<title><?= $setup ? 'Set up admin' : 'Admin login' ?></title>
<link rel="stylesheet" href="admin.css">
</head><body class="login">
<form class="box" method="post" autocomplete="off">
<h1><?= t('site', 'name') ?></h1>
<p><?= $setup ? 'First-time setup: enter your email and choose a password.' : 'Sign in to edit the website.' ?></p>
<?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
<?= csrf_field() ?>
<label>Email<input type="email" name="email" required autofocus autocomplete="username"></label>
<label>Password<input type="password" name="password" required autocomplete="<?= $setup ? 'new-password' : 'current-password' ?>"></label>
<?php if ($setup): ?><label>Confirm password<input type="password" name="confirm" required autocomplete="new-password"></label><?php endif; ?>
<button class="btn" type="submit"><?= $setup ? 'Create password' : 'Sign in' ?></button>
<p class="back"><a href="../index.php">← Back to website</a></p>
</form>
</body></html>

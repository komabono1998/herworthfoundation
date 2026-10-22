<?php
require __DIR__ . '/inc/layout.php';
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $subject = trim((string)($_POST['subject'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));
    $tooSoon = time() - (int)($_SESSION['last_msg'] ?? 0) < 20;

    if (($_POST['website'] ?? '') !== '') {
        // honeypot filled: pretend success
        header('Location: contact.php?sent=1'); exit;
    } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        $error = 'Please fill in your name, a valid email and a message.';
    } elseif ($tooSoon) {
        $error = 'Please wait a few seconds before sending another message.';
    } else {
        $all = read_json(MESSAGES_FILE);
        $all[] = [
            'id' => bin2hex(random_bytes(6)), 'time' => date('c'), 'read' => false,
            'name' => mb_substr($name, 0, 120), 'email' => mb_substr($email, 0, 160),
            'subject' => mb_substr($subject, 0, 160), 'message' => mb_substr($message, 0, 5000),
        ];
        write_json(MESSAGES_FILE, $all);
        $_SESSION['last_msg'] = time();
        header('Location: contact.php?sent=1'); exit;
    }
}

head('contact.php', (string)v('contact', 'eyebrow'), (string)v('contact', 'lead'));
page_hero('contact');
$s = content()['site'];
?>
<section><div class="wrap split" style="align-items:start">
<div class="card">
<h3><?= t('contact', 'form_title') ?></h3>
<?php if (isset($_GET['sent'])): ?><p class="alert ok"><?= t('contact', 'thanks') ?></p><?php endif; ?>
<?php if ($error): ?><p class="alert err"><?= e($error) ?></p><?php endif; ?>
<form method="post" action="contact.php">
<div><label for="name">Your name</label><input id="name" name="name" required autocomplete="name" value="<?= e($_POST['name'] ?? '') ?>"></div>
<div><label for="email">Your email</label><input id="email" name="email" type="email" required autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>"></div>
<div><label for="subject">I'd like to</label>
<select id="subject" name="subject"><?php foreach (v('contact', 'subjects') as $o): ?><option><?= e($o) ?></option><?php endforeach; ?></select></div>
<div><label for="message">Message</label><textarea id="message" name="message" rows="5" required><?= e($_POST['message'] ?? '') ?></textarea></div>
<div class="hp" aria-hidden="true"><label>Leave empty <input name="website" tabindex="-1" autocomplete="off"></label></div>
<button class="btn" type="submit">Send message</button>
</form>
</div>
<div>
<h2><?= t('contact', 'find_title') ?></h2>
<ul class="contact-big">
<?php if ($s['address'] !== ''): ?><li><?= icon('pin') ?><span><?= nl2br(e($s['address'])) ?></span></li><?php endif; ?>
<?php if ($s['email'] !== ''): ?><li><?= icon('mail') ?><a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a></li><?php endif; ?>
<?php if ($s['phone'] !== ''): ?><li><?= icon('phone') ?><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $s['phone'])) ?>"><?= e($s['phone']) ?></a></li><?php endif; ?>
<?php if ($s['hours'] !== ''): ?><li><?= icon('clock') ?><span><?= nl2br(e($s['hours'])) ?></span></li><?php endif; ?>
</ul>
<?php if (social_links() !== ''): ?><div class="socials dark" style="margin-top:18px"><?= social_links() ?></div><?php endif; ?>
</div>
</div></section>
<?php foot(); ?>

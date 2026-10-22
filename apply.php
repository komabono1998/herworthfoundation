<?php
require __DIR__ . '/inc/forms.php';
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);

$id = (string)($_GET['o'] ?? '');
$opp = $id !== '' ? find_opportunity($id) : null;
$general = $id === '';
$closed = !$general && (!$opp || !opp_is_open($opp));
$type = $general ? 'volunteer' : (string)($opp['type'] ?? 'volunteer');
$cvRequired = !$general && !$closed && ($opp['cv_required'] ?? 'yes') === 'yes';
$back = $type === 'career' ? 'careers.php' : 'volunteer.php';
$self = 'apply.php' . ($general ? '' : '?o=' . urlencode($id));

if (empty($_SESSION['pub_csrf'])) $_SESSION['pub_csrf'] = bin2hex(random_bytes(16));

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$closed) {
    $f = array_map(fn($k) => trim((string)($_POST[$k] ?? '')), ['name', 'email', 'phone', 'location', 'availability', 'message']);
    [$name, $email, $phone, $location, $availability, $message] = $f;

    if (!hash_equals($_SESSION['pub_csrf'], (string)($_POST['csrf'] ?? ''))) {
        $error = 'Your session expired. Please reload the page and try again.';
    } elseif (($_POST['website'] ?? '') !== '') {
        header('Location: ' . $self . ($general ? '?' : '&') . 'sent=1'); exit;   // honeypot
    } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        $error = 'Please fill in your name, a valid email and a short message.';
    } elseif (time() - (int)($_SESSION['last_app'] ?? 0) < 15) {
        $error = 'Please wait a few seconds before submitting again.';
    } else {
        [$cvFile, $cvOrig, $cvError] = save_cv($_FILES['cv'] ?? null, $cvRequired);
        if ($cvError) {
            $error = $cvError;
        } else {
            $apps = read_json(APPS_FILE);
            $apps[] = [
                'id' => bin2hex(random_bytes(6)), 'time' => date('c'), 'status' => 'new',
                'type' => $type, 'opp_id' => $general ? '' : $id,
                'opp_title' => $general ? 'General volunteer registration' : (string)$opp['title'],
                'name' => mb_substr($name, 0, 120), 'email' => mb_substr($email, 0, 160), 'phone' => mb_substr($phone, 0, 40),
                'location' => mb_substr($location, 0, 120), 'availability' => mb_substr($availability, 0, 300),
                'message' => mb_substr($message, 0, 5000), 'cv_file' => $cvFile, 'cv_orig' => $cvOrig,
            ];
            write_json(APPS_FILE, $apps);
            $_SESSION['last_app'] = time();
            header('Location: ' . $self . ($general ? '?' : '&') . 'sent=1'); exit;
        }
    }
}

$title = $general ? (string)v('involve', 'v_general_title') : (string)($opp['title'] ?? 'Opening closed');
head('apply.php', $title, (string)v('involve', 'apply_intro'));
?>
<section class="page-hero"><div class="wrap">
<span class="eyebrow"><?= $type === 'career' ? 'Career opportunity' : 'Volunteer opportunity' ?></span>
<h1><?= e($title) ?></h1>
<?php if ($opp && !$closed): ?><p class="lead"><?= e(implode(' · ', array_filter([$opp['location'] ?? '', $opp['commitment'] ?? '', !empty($opp['deadline']) ? 'Apply by ' . date('j M Y', strtotime($opp['deadline'])) : '']))) ?></p><?php endif; ?>
</div></section>
<?php involve_nav($back); ?>

<section><div class="wrap">
<?php if ($closed): ?>
  <div class="card" style="max-width:640px">
    <h3>This opening is no longer available</h3>
    <p>It may have been filled or closed. See what is open right now.</p>
    <p><a class="btn" href="<?= $back ?>">View open <?= $type === 'career' ? 'positions' : 'roles' ?></a></p>
  </div>
<?php elseif (isset($_GET['sent'])): ?>
  <div class="card" style="max-width:640px">
    <div class="ico">✅</div>
    <h3>Application received</h3>
    <p><?= t('involve', 'apply_thanks') ?></p>
    <p><a class="btn ghost" href="<?= $back ?>">Back to <?= $type === 'career' ? 'careers' : 'volunteering' ?></a></p>
  </div>
<?php else: ?>
<div class="split" style="align-items:start">
  <div>
    <?php if ($opp): ?>
      <h2>About the role</h2>
      <?= paras((string)($opp['description'] ?? $opp['summary'] ?? '')) ?>
      <?php if (!empty($opp['requirements'])): ?>
        <h3 style="margin-top:28px">Requirements</h3>
        <ul class="ticks"><?php foreach ($opp['requirements'] as $r): ?><li><?= e($r) ?></li><?php endforeach; ?></ul>
      <?php endif; ?>
    <?php else: ?>
      <h2><?= t('involve', 'v_general_title') ?></h2>
      <?= paras((string)v('involve', 'v_general_text')) ?>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3>Apply now</h3>
    <p><?= t('involve', 'apply_intro') ?></p>
    <?php if ($error): ?><p class="alert err"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="<?= e($self) ?>" enctype="multipart/form-data" style="margin-top:14px">
      <input type="hidden" name="csrf" value="<?= e($_SESSION['pub_csrf']) ?>">
      <div><label for="name">Full name *</label><input id="name" name="name" required autocomplete="name" value="<?= e($_POST['name'] ?? '') ?>"></div>
      <div><label for="email">Email *</label><input id="email" name="email" type="email" required autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>"></div>
      <div><label for="phone">Phone / WhatsApp</label><input id="phone" name="phone" type="tel" autocomplete="tel" value="<?= e($_POST['phone'] ?? '') ?>"></div>
      <div><label for="location">Where do you live?</label><input id="location" name="location" value="<?= e($_POST['location'] ?? '') ?>"></div>
      <?php if ($type === 'volunteer'): ?>
      <div><label for="availability">When are you available?</label><input id="availability" name="availability" placeholder="e.g. weekends, school holidays" value="<?= e($_POST['availability'] ?? '') ?>"></div>
      <?php endif; ?>
      <div><label for="message"><?= $type === 'career' ? 'Cover letter / why you are a good fit *' : 'Why would you like to volunteer? *' ?></label><textarea id="message" name="message" rows="5" required><?= e($_POST['message'] ?? '') ?></textarea></div>
      <div><label for="cv">Your CV <?= $cvRequired ? '*' : '(optional, recommended)' ?></label>
        <input id="cv" name="cv" type="file" accept=".pdf,.doc,.docx,application/pdf" <?= $cvRequired ? 'required' : '' ?>>
        <small class="note">PDF, DOC or DOCX, up to 5 MB.</small></div>
      <div class="hp" aria-hidden="true"><label>Leave empty <input name="website" tabindex="-1" autocomplete="off"></label></div>
      <button class="btn" type="submit">Submit application</button>
    </form>
  </div>
</div>
<?php endif; ?>
</div></section>
<?php foot(); ?>

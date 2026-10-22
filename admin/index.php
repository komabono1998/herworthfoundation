<?php
require __DIR__ . '/lib.php';
require_login();

$schema = schema();
$view = (string)($_GET['s'] ?? 'dashboard');
$valid = array_merge(array_keys($schema), ['dashboard', 'messages', 'applications', 'password']);
if (!in_array($view, $valid, true)) $view = 'dashboard';

/* ---------- actions ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'save' && isset($schema[$view])) {
        $saved = read_json(CONTENT_FILE);
        $saved[$view] = collect($schema[$view]['fields'], (array)($_POST['d'] ?? []), []);
        write_json(CONTENT_FILE, $saved) ? flash('Saved. Your changes are live.') : flash('Could not write data/content.json. Check folder permissions.', 'err');
    } elseif ($action === 'reset' && isset($schema[$view])) {
        $saved = read_json(CONTENT_FILE);
        unset($saved[$view]);
        write_json(CONTENT_FILE, $saved);
        flash('This section was reset to its original text.');
    } elseif ($action === 'msg_delete') {
        $id = (string)($_POST['id'] ?? '');
        write_json(MESSAGES_FILE, array_values(array_filter(read_json(MESSAGES_FILE), fn($m) => ($m['id'] ?? '') !== $id)));
        flash('Message deleted.');
    } elseif ($action === 'app_status') {
        $all = read_json(APPS_FILE);
        $allowed = ['new', 'reviewed', 'shortlisted', 'rejected'];
        foreach ($all as &$a) if (($a['id'] ?? '') === ($_POST['id'] ?? '') && in_array($_POST['status'] ?? '', $allowed, true)) $a['status'] = $_POST['status'];
        unset($a);
        write_json(APPS_FILE, $all);
        flash('Status updated.');
    } elseif ($action === 'app_delete') {
        $keep = [];
        foreach (read_json(APPS_FILE) as $a) {
            if (($a['id'] ?? '') === ($_POST['id'] ?? '')) {
                if (!empty($a['cv_file'])) @unlink(CV_DIR . '/' . basename((string)$a['cv_file']));
            } else $keep[] = $a;
        }
        write_json(APPS_FILE, $keep);
        flash('Application and its CV deleted.');
    } elseif ($action === 'msg_read') {
        $all = read_json(MESSAGES_FILE);
        foreach ($all as &$m) if (($m['id'] ?? '') === ($_POST['id'] ?? '')) $m['read'] = true;
        write_json(MESSAGES_FILE, $all);
    } elseif ($action === 'password') {
        $hash = (string)(read_json(ADMIN_FILE)['hash'] ?? '');
        $new = (string)($_POST['new'] ?? '');
        if (!password_verify((string)($_POST['current'] ?? ''), $hash)) flash('Current password is incorrect.', 'err');
        elseif (strlen($new) < 10) flash('New password must be at least 10 characters.', 'err');
        elseif ($new !== ($_POST['confirm'] ?? '')) flash('The new passwords do not match.', 'err');
        else { write_json(ADMIN_FILE, ['email' => read_json(ADMIN_FILE)['email'] ?? '', 'hash' => password_hash($new, PASSWORD_DEFAULT), 'changed' => date('c')]); flash('Password changed.'); }
    }
    header('Location: index.php?s=' . urlencode($view));
    exit;
}

$content = content();
$unread = unread_count();
$newApps = new_applications();
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex">
<title>Admin | <?= t('site', 'name') ?></title>
<link rel="stylesheet" href="admin.css">
</head><body>
<aside>
  <div class="logo"><?= t('site', 'name') ?><small>Admin</small></div>
  <nav>
    <a href="index.php" <?= $view === 'dashboard' ? 'class="on"' : '' ?>>▦ Dashboard</a>
    <span class="cap">Edit content</span>
    <?php foreach ($schema as $key => $sec): ?>
      <a href="?s=<?= $key ?>" <?= $view === $key ? 'class="on"' : '' ?>><?= $sec['icon'] ?> <?= e($sec['label']) ?></a>
    <?php endforeach; ?>
    <span class="cap">Inbox &amp; account</span>
    <a href="?s=applications" <?= $view === 'applications' ? 'class="on"' : '' ?>>📄 Applications<?= $newApps ? " <b class=\"pill\">$newApps</b>" : '' ?></a>
    <a href="?s=messages" <?= $view === 'messages' ? 'class="on"' : '' ?>>✉ Messages<?= $unread ? " <b class=\"pill\">$unread</b>" : '' ?></a>
    <a href="?s=password" <?= $view === 'password' ? 'class="on"' : '' ?>>🔒 Password</a>
    <a href="../index.php" target="_blank" rel="noopener">↗ View website</a>
    <a href="logout.php">⎋ Sign out</a>
  </nav>
</aside>
<main>
<?= flashes() ?>

<?php if ($view === 'dashboard'): ?>
  <h1>Welcome back</h1>
  <p class="sub">Pick a section to edit. Changes appear on the website as soon as you save.</p>
  <?php if ($newApps): ?><a class="alert ok block" href="?s=applications"><?= $newApps ?> new application<?= $newApps > 1 ? 's' : '' ?> to review →</a><?php endif; ?>
  <?php if ($unread): ?><a class="alert ok block" href="?s=messages">You have <?= $unread ?> unread message<?= $unread > 1 ? 's' : '' ?> →</a><?php endif; ?>
  <div class="cards">
    <?php foreach ($schema as $key => $sec): ?>
      <a class="tile" href="?s=<?= $key ?>"><span><?= $sec['icon'] ?></span><b><?= e($sec['label']) ?></b><small><?= count(array_filter($sec['fields'], fn($f) => $f['type'] !== 'heading')) ?> fields</small></a>
    <?php endforeach; ?>
    <a class="tile" href="?s=applications"><span>📄</span><b>Applications</b><small><?= count(read_json(APPS_FILE)) ?> total, <?= $newApps ?> new</small></a>
    <a class="tile" href="?s=messages"><span>✉</span><b>Messages</b><small><?= count(read_json(MESSAGES_FILE)) ?> total, <?= $unread ?> unread</small></a>
  </div>

<?php elseif (isset($schema[$view])): $sec = $schema[$view]; ?>
  <h1><?= e($sec['label']) ?></h1>
  <form method="post" enctype="multipart/form-data" class="editor">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <?php foreach ($sec['fields'] as $key => $spec) render_field("d[$key]", "up[$key]", $spec, $content[$view][$key] ?? ''); ?>
    <div class="savebar">
      <button class="btn" type="submit">Save changes</button>
      <button class="btn ghost" type="submit" name="action" value="reset" formnovalidate onclick="return confirm('Reset this whole section to its original text? Your edits here will be lost.')">Reset section</button>
    </div>
  </form>

<?php elseif ($view === 'applications'):
  $apps = array_reverse(read_json(APPS_FILE));
  $filter = (string)($_GET['t'] ?? '');
  if (in_array($filter, ['volunteer', 'career'], true)) $apps = array_filter($apps, fn($a) => ($a['type'] ?? '') === $filter);
  $labels = ['new' => 'New', 'reviewed' => 'Reviewed', 'shortlisted' => 'Shortlisted', 'rejected' => 'Rejected'];
?>
  <h1>Applications</h1>
  <p class="sub">Volunteer and career applications, with their CVs.</p>
  <div class="row" style="margin-bottom:16px">
    <a class="btn small <?= $filter === '' ? '' : 'ghost' ?>" href="?s=applications">All</a>
    <a class="btn small <?= $filter === 'volunteer' ? '' : 'ghost' ?>" href="?s=applications&amp;t=volunteer">Volunteers</a>
    <a class="btn small <?= $filter === 'career' ? '' : 'ghost' ?>" href="?s=applications&amp;t=career">Careers</a>
  </div>
  <?php if (!$apps): ?><p class="empty">No applications yet.</p><?php endif; ?>
  <?php foreach ($apps as $a): $st = $a['status'] ?? 'new'; ?>
    <details class="msg <?= $st === 'new' ? 'new' : '' ?>">
      <summary><b><?= e($a['name']) ?></b> · <?= e($a['opp_title']) ?>
        <small><?= ($a['type'] ?? '') === 'career' ? 'Career' : 'Volunteer' ?> · <?= e($labels[$st] ?? $st) ?> · <?= e(date('j M Y, H:i', strtotime($a['time']))) ?></small></summary>
      <p class="meta"><a href="mailto:<?= e($a['email']) ?>"><?= e($a['email']) ?></a><?= $a['phone'] !== '' ? ' · ' . e($a['phone']) : '' ?><?= $a['location'] !== '' ? ' · ' . e($a['location']) : '' ?></p>
      <?php if (!empty($a['availability'])): ?><p><b>Available:</b> <?= e($a['availability']) ?></p><?php endif; ?>
      <p><?= nl2br(e($a['message'])) ?></p>
      <p><?php if (!empty($a['cv_file'])): ?><a class="btn small" href="download.php?id=<?= e($a['id']) ?>">⬇ Download CV (<?= e($a['cv_orig'] ?? 'file') ?>)</a><?php else: ?><span class="empty">No CV attached</span><?php endif; ?></p>
      <form method="post" class="row">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($a['id']) ?>">
        <select name="status" style="width:auto;margin:0"><?php foreach ($labels as $k => $l): ?><option value="<?= $k ?>"<?= $st === $k ? ' selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select>
        <button class="btn small" name="action" value="app_status">Update status</button>
        <button class="btn small danger" name="action" value="app_delete" onclick="return confirm('Delete this application and its CV permanently?')">Delete</button>
      </form>
    </details>
  <?php endforeach; ?>

<?php elseif ($view === 'messages'): $msgs = array_reverse(read_json(MESSAGES_FILE)); ?>
  <h1>Messages</h1>
  <p class="sub">Sent from the Get Involved form.</p>
  <?php if (!$msgs): ?><p class="empty">No messages yet.</p><?php endif; ?>
  <?php foreach ($msgs as $m): ?>
    <details class="msg <?= empty($m['read']) ? 'new' : '' ?>">
      <summary><b><?= e($m['name']) ?></b> · <?= e($m['subject']) ?> <small><?= e(date('j M Y, H:i', strtotime($m['time']))) ?></small></summary>
      <p><?= nl2br(e($m['message'])) ?></p>
      <p class="meta"><a href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . $m['subject']) ?>"><?= e($m['email']) ?></a></p>
      <form method="post" class="row">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($m['id']) ?>">
        <?php if (empty($m['read'])): ?><button class="btn small" name="action" value="msg_read">Mark as read</button><?php endif; ?>
        <button class="btn small danger" name="action" value="msg_delete" onclick="return confirm('Delete this message permanently?')">Delete</button>
      </form>
    </details>
  <?php endforeach; ?>

<?php else: ?>
  <h1>Change password</h1>
  <form method="post" class="editor narrow">
    <?= csrf_field() ?><input type="hidden" name="action" value="password">
    <div class="fld"><label>Current password<input type="password" name="current" required autocomplete="current-password"></label></div>
    <div class="fld"><label>New password<input type="password" name="new" required minlength="10" autocomplete="new-password"></label><small>At least 10 characters.</small></div>
    <div class="fld"><label>Confirm new password<input type="password" name="confirm" required autocomplete="new-password"></label></div>
    <div class="savebar"><button class="btn" type="submit">Change password</button></div>
  </form>
<?php endif; ?>
</main>
<script src="admin.js"></script>
</body></html>

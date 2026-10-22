<?php
require __DIR__ . '/lib.php';
require_login();

$schema = schema();
$meta = section_meta();
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
        write_json(CONTENT_FILE, $saved)
            ? flash('Saved. Your changes are now live on the website.', 'ok', '../' . ($meta[$view][0] ?? 'index.php'))
            : flash('Could not write data/content.json. Check folder permissions.', 'err');
    } elseif ($action === 'reset' && isset($schema[$view])) {
        $saved = read_json(CONTENT_FILE);
        unset($saved[$view]);
        write_json(CONTENT_FILE, $saved);
        flash('This section was reset to its original content.');
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
    } elseif ($action === 'msg_delete') {
        $id = (string)($_POST['id'] ?? '');
        write_json(MESSAGES_FILE, array_values(array_filter(read_json(MESSAGES_FILE), fn($m) => ($m['id'] ?? '') !== $id)));
        flash('Message deleted.');
    } elseif ($action === 'msg_read') {
        $all = read_json(MESSAGES_FILE);
        foreach ($all as &$m) if (($m['id'] ?? '') === ($_POST['id'] ?? '')) $m['read'] = true;
        write_json(MESSAGES_FILE, $all);
        flash('Marked as read.');
    } elseif ($action === 'password') {
        $hash = (string)(read_json(ADMIN_FILE)['hash'] ?? '');
        $new = (string)($_POST['new'] ?? '');
        if (!password_verify((string)($_POST['current'] ?? ''), $hash)) flash('Current password is incorrect.', 'err');
        elseif (strlen($new) < 10) flash('New password must be at least 10 characters.', 'err');
        elseif ($new !== ($_POST['confirm'] ?? '')) flash('The new passwords do not match.', 'err');
        else { write_json(ADMIN_FILE, ['email' => read_json(ADMIN_FILE)['email'] ?? '', 'hash' => password_hash($new, PASSWORD_DEFAULT), 'changed' => date('c')]); flash('Password changed.'); }
    }
    header('Location: index.php?s=' . urlencode($view) . (isset($_GET['t']) ? '&t=' . urlencode((string)$_GET['t']) : ''));
    exit;
}

/* ---------- numbers used across views ---------- */
$content = content();
$apps = read_json(APPS_FILE);
$msgs = read_json(MESSAGES_FILE);
$newApps = new_applications();
$unread = unread_count();
$badges = ['applications' => $newApps, 'messages' => $unread];
[$upcoming] = events_split();
$openRoles = count(opportunities(null, true));
$adminEmail = (string)(read_json(ADMIN_FILE)['email'] ?? '');

/** "Applications" and "Messages" list helpers */
function when_short(string $iso): string { $t = strtotime($iso); return $t ? date('j M Y, H:i', $t) : ''; }

/* =========================================================== DASHBOARD */
if ($view === 'dashboard') {
    $h = (int)date('G');
    $greet = $h < 12 ? 'Good morning' : ($h < 17 ? 'Good afternoon' : 'Good evening');
    admin_open('dashboard', $badges, 'Dashboard',
        '<a class="btn small ghost" href="?s=events&new=items">' . aic('plus') . ' New event</a>'
        . '<a class="btn small" href="?s=opportunities&new=items">' . aic('plus') . ' New opening</a>');

    $s = $content['site'];
    $checks = [
        ['Add a contact email', $s['email'] !== '', 'site'],
        ['Add a phone or WhatsApp number', $s['phone'] !== '' || $s['whatsapp'] !== '', 'site'],
        ['Link your Facebook and Instagram', $s['facebook'] !== '' && $s['instagram'] !== '', 'site'],
        ['Set up donations (link or procedures)', preg_match('#^https?://#i', (string)$content['donate']['payment_url']) || !empty($content['donate']['methods']), 'donate'],
        ['Schedule an upcoming event', (bool)$upcoming, 'events'],
        ['Open a volunteer or career role', $openRoles > 0, 'opportunities'],
        ['Publish a news story', !empty($content['news']['posts']), 'news'],
    ];
    $done = count(array_filter($checks, fn($c) => $c[1]));
    $pct = (int)round($done / count($checks) * 100);
    ?>
    <div class="hello">
      <div><h1><?= $greet ?>.</h1><p>Here is what is happening on <?= t('site', 'name') ?> today.</p></div>
      <span class="date"><?= date('l, j F Y') ?></span>
    </div>

    <div class="kpis">
      <a class="kpi" href="?s=applications"><span class="kic"><?= aic('file') ?></span><b><?= $newApps ?></b><span>New applications</span><small><?= count($apps) ?> in total</small></a>
      <a class="kpi" href="?s=messages"><span class="kic"><?= aic('inbox') ?></span><b><?= $unread ?></b><span>Unread messages</span><small><?= count($msgs) ?> in total</small></a>
      <a class="kpi" href="?s=opportunities"><span class="kic"><?= aic('briefcase') ?></span><b><?= $openRoles ?></b><span>Open roles</span><small>Volunteer &amp; careers</small></a>
      <a class="kpi" href="?s=events"><span class="kic"><?= aic('calendar') ?></span><b><?= count($upcoming) ?></b><span>Upcoming events</span><small>With live countdowns</small></a>
    </div>

    <div class="cols">
      <div class="col">
        <section class="panel">
          <h2>Quick actions</h2>
          <div class="qa">
            <a href="?s=events&amp;new=items"><span><?= aic('calendar') ?></span><b>Add an event</b><small>Date, venue, host</small></a>
            <a href="?s=opportunities&amp;new=items"><span><?= aic('briefcase') ?></span><b>Open a role</b><small>Volunteer or job</small></a>
            <a href="?s=news&amp;new=posts"><span><?= aic('news') ?></span><b>Post a story</b><small>News &amp; updates</small></a>
            <a href="?s=donate"><span><?= aic('heart') ?></span><b>Donation details</b><small>Link &amp; procedures</small></a>
            <a href="?s=home"><span><?= aic('home') ?></span><b>Edit home page</b><small>Hero, numbers, photos</small></a>
            <a href="?s=site"><span><?= aic('settings') ?></span><b>Contact &amp; social</b><small>Footer details</small></a>
          </div>
        </section>

        <section class="panel">
          <div class="phead"><h2>Recent applications</h2><a href="?s=applications">View all <?= aic('arrow') ?></a></div>
          <?php $recent = array_slice(array_reverse($apps), 0, 5); if (!$recent): ?>
            <p class="empty">No applications yet. Open a role to start receiving them.</p>
          <?php else: ?>
            <ul class="feed"><?php foreach ($recent as $a): ?>
              <li><span class="av"><?= e(mb_strtoupper(mb_substr($a['name'], 0, 1))) ?></span>
                <div><b><?= e($a['name']) ?></b><small><?= e($a['opp_title']) ?> · <?= e(when_short($a['time'])) ?></small></div>
                <span class="badge b-<?= e($a['status'] ?? 'new') ?>"><?= e(ucfirst($a['status'] ?? 'new')) ?></span></li>
            <?php endforeach; ?></ul>
          <?php endif; ?>
        </section>

        <section class="panel">
          <div class="phead"><h2>Recent messages</h2><a href="?s=messages">View all <?= aic('arrow') ?></a></div>
          <?php $rm = array_slice(array_reverse($msgs), 0, 4); if (!$rm): ?>
            <p class="empty">No messages yet.</p>
          <?php else: ?>
            <ul class="feed"><?php foreach ($rm as $m): ?>
              <li><span class="av alt"><?= e(mb_strtoupper(mb_substr($m['name'], 0, 1))) ?></span>
                <div><b><?= e($m['name']) ?></b><small><?= e(mb_strimwidth($m['message'], 0, 70, '…')) ?></small></div>
                <?= empty($m['read']) ? '<span class="badge b-new">New</span>' : '' ?></li>
            <?php endforeach; ?></ul>
          <?php endif; ?>
        </section>
      </div>

      <div class="col side">
        <section class="panel">
          <h2>Next event</h2>
          <?php if ($upcoming): $n = $upcoming[0]; ?>
            <b class="evt"><?= e($n['title']) ?></b>
            <p class="muted"><?= e(when_label($n)) ?><?= !empty($n['venue']) ? ' · ' . e($n['venue']) : '' ?></p>
            <?= countdown($n['_ts'], 'adm') ?>
            <a class="btn small ghost" href="?s=events">Manage events</a>
          <?php else: ?>
            <p class="empty">Nothing scheduled.</p>
            <a class="btn small" href="?s=events&amp;new=items"><?= aic('plus') ?> Add an event</a>
          <?php endif; ?>
        </section>

        <section class="panel">
          <div class="phead"><h2>Setup checklist</h2><span class="pct"><?= $pct ?>%</span></div>
          <div class="progress"><i style="width:<?= $pct ?>%"></i></div>
          <ul class="checks">
            <?php foreach ($checks as [$label, $ok, $target]): ?>
              <li class="<?= $ok ? 'ok' : '' ?>"><?= aic($ok ? 'check' : 'circle') ?>
                <?= $ok ? '<span>' . e($label) . '</span>' : '<a href="?s=' . $target . '">' . e($label) . '</a>' ?></li>
            <?php endforeach; ?>
          </ul>
        </section>

        <section class="panel">
          <h2>Jump to a live page</h2>
          <div class="links">
            <?php foreach ([['index.php', 'Home'], ['about.php', 'About'], ['programs.php', 'What We Do'], ['event.php', 'Menstrual Hygiene Run'], ['events.php', 'Events'], ['news.php', 'News'], ['partners.php', 'Partners'], ['volunteer.php', 'Volunteer'], ['careers.php', 'Careers'], ['donate.php', 'Donate'], ['contact.php', 'Contact']] as [$u, $l]): ?>
              <a href="../<?= $u ?>" target="_blank" rel="noopener"><?= e($l) ?> <?= aic('ext') ?></a>
            <?php endforeach; ?>
          </div>
        </section>
      </div>
    </div>
    <?php
    admin_close();
    exit;
}

/* =========================================================== CONTENT EDITORS */
if (isset($schema[$view])) {
    $sec = $schema[$view];
    $live = '../' . ($meta[$view][0] ?? 'index.php');
    admin_open($view, $badges, '',
        '<a class="btn small ghost" href="' . e($live) . '" target="_blank" rel="noopener">View live page ' . aic('ext') . '</a>'
        . '<button class="btn small" type="submit" form="editor">' . aic('save') . ' Save changes</button>');

    $groups = [];
    $cur = 'Main content';
    foreach ($sec['fields'] as $key => $spec) {
        if ($spec['type'] === 'heading') { $cur = $spec['label']; continue; }
        $groups[$cur][$key] = $spec;
    }
    ?>
    <div class="pagehead"><h1><?= e(admin_title($view)) ?></h1><p><?= e($meta[$view][1] ?? '') ?></p></div>
    <form id="editor" method="post" enctype="multipart/form-data" class="editor">
      <?= csrf_field() ?><input type="hidden" name="action" value="save">
      <?php if (count($groups) > 1): ?>
        <nav class="chips" aria-label="Sections on this page"><?php $gi = 0; foreach (array_keys($groups) as $g): ?><a href="#g<?= ++$gi ?>"><?= e($g) ?></a><?php endforeach; ?></nav>
      <?php endif; ?>
      <?php $gi = 0; foreach ($groups as $title => $fields): $gi++; ?>
        <details class="grp" id="g<?= $gi ?>" open>
          <summary><h2><?= e($title) ?></h2><span class="chev"></span></summary>
          <div class="gbody"><?php foreach ($fields as $key => $spec) render_field("d[$key]", "up[$key]", $spec, $content[$view][$key] ?? ''); ?></div>
        </details>
      <?php endforeach; ?>
      <div class="savebar">
        <button class="btn" type="submit"><?= aic('save') ?> Save changes</button>
        <a class="btn ghost" href="<?= e($live) ?>" target="_blank" rel="noopener">Preview live page</a>
        <button class="btn ghost danger" type="submit" name="action" value="reset" formnovalidate onclick="return confirm('Reset this whole section to its original content? Your edits here will be lost.')">Reset section</button>
      </div>
    </form>
    <?php
    admin_close();
    exit;
}

/* =========================================================== APPLICATIONS */
if ($view === 'applications') {
    admin_open('applications', $badges);
    $filter = (string)($_GET['t'] ?? '');
    $list = array_reverse($apps);
    if (in_array($filter, ['volunteer', 'career'], true)) $list = array_filter($list, fn($a) => ($a['type'] ?? '') === $filter);
    $labels = ['new' => 'New', 'reviewed' => 'Reviewed', 'shortlisted' => 'Shortlisted', 'rejected' => 'Rejected'];
    ?>
    <div class="pagehead"><h1>Applications</h1><p>Volunteer and career applications, with their CVs.</p></div>
    <div class="tabs">
      <a href="?s=applications" class="<?= $filter === '' ? 'on' : '' ?>">All <small><?= count($apps) ?></small></a>
      <a href="?s=applications&amp;t=volunteer" class="<?= $filter === 'volunteer' ? 'on' : '' ?>">Volunteers</a>
      <a href="?s=applications&amp;t=career" class="<?= $filter === 'career' ? 'on' : '' ?>">Careers</a>
    </div>
    <?php if (!$list): ?><p class="panel empty">No applications yet. They appear here when someone applies through a Volunteer or Careers page.</p><?php endif; ?>
    <?php foreach ($list as $a): $st = $a['status'] ?? 'new'; ?>
      <details class="rowcard <?= $st === 'new' ? 'new' : '' ?>">
        <summary>
          <span class="av"><?= e(mb_strtoupper(mb_substr($a['name'], 0, 1))) ?></span>
          <div class="who"><b><?= e($a['name']) ?></b><small><?= e($a['opp_title']) ?></small></div>
          <span class="kind"><?= ($a['type'] ?? '') === 'career' ? 'Career' : 'Volunteer' ?></span>
          <span class="badge b-<?= e($st) ?>"><?= e($labels[$st] ?? $st) ?></span>
          <small class="when"><?= e(when_short($a['time'])) ?></small>
        </summary>
        <div class="rbody">
          <p class="meta"><a href="mailto:<?= e($a['email']) ?>"><?= e($a['email']) ?></a><?= $a['phone'] !== '' ? ' · ' . e($a['phone']) : '' ?><?= $a['location'] !== '' ? ' · ' . e($a['location']) : '' ?></p>
          <?php if (!empty($a['availability'])): ?><p><b>Available:</b> <?= e($a['availability']) ?></p><?php endif; ?>
          <p><?= nl2br(e($a['message'])) ?></p>
          <p><?php if (!empty($a['cv_file'])): ?><a class="btn small" href="download.php?id=<?= e($a['id']) ?>"><?= aic('file') ?> Download CV (<?= e($a['cv_orig'] ?? 'file') ?>)</a><?php else: ?><span class="empty">No CV attached</span><?php endif; ?></p>
          <form method="post" class="row">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($a['id']) ?>">
            <select name="status"><?php foreach ($labels as $k => $l): ?><option value="<?= $k ?>"<?= $st === $k ? ' selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select>
            <button class="btn small" name="action" value="app_status">Update status</button>
            <button class="btn small danger" name="action" value="app_delete" onclick="return confirm('Delete this application and its CV permanently?')">Delete</button>
          </form>
        </div>
      </details>
    <?php endforeach;
    admin_close();
    exit;
}

/* =========================================================== MESSAGES */
if ($view === 'messages') {
    admin_open('messages', $badges);
    $list = array_reverse($msgs);
    ?>
    <div class="pagehead"><h1>Messages</h1><p>Sent from the contact form.</p></div>
    <?php if (!$list): ?><p class="panel empty">No messages yet.</p><?php endif; ?>
    <?php foreach ($list as $m): ?>
      <details class="rowcard <?= empty($m['read']) ? 'new' : '' ?>">
        <summary>
          <span class="av alt"><?= e(mb_strtoupper(mb_substr($m['name'], 0, 1))) ?></span>
          <div class="who"><b><?= e($m['name']) ?></b><small><?= e($m['subject']) ?></small></div>
          <?= empty($m['read']) ? '<span class="badge b-new">New</span>' : '' ?>
          <small class="when"><?= e(when_short($m['time'])) ?></small>
        </summary>
        <div class="rbody">
          <p><?= nl2br(e($m['message'])) ?></p>
          <p class="meta"><a href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . $m['subject']) ?>"><?= e($m['email']) ?></a></p>
          <form method="post" class="row">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= e($m['id']) ?>">
            <a class="btn small" href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . $m['subject']) ?>"><?= aic('mail') ?> Reply</a>
            <?php if (empty($m['read'])): ?><button class="btn small ghost" name="action" value="msg_read">Mark as read</button><?php endif; ?>
            <button class="btn small danger" name="action" value="msg_delete" onclick="return confirm('Delete this message permanently?')">Delete</button>
          </form>
        </div>
      </details>
    <?php endforeach;
    admin_close();
    exit;
}

/* =========================================================== PASSWORD */
admin_open('password', $badges);
?>
<div class="pagehead"><h1>Account &amp; password</h1><p>Signed in as <b><?= e($adminEmail) ?></b>.</p></div>
<form method="post" class="panel narrow editor">
  <?= csrf_field() ?><input type="hidden" name="action" value="password">
  <div class="fld"><label>Current password<input type="password" name="current" required autocomplete="current-password"></label></div>
  <div class="fld"><label>New password<input type="password" name="new" required minlength="10" autocomplete="new-password"></label><small>At least 10 characters. Use a long phrase that only you know.</small></div>
  <div class="fld"><label>Confirm new password<input type="password" name="confirm" required autocomplete="new-password"></label></div>
  <div class="savebar"><button class="btn" type="submit">Change password</button></div>
</form>
<?php admin_close();

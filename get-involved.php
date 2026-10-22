<?php
require __DIR__ . '/inc/forms.php';
head('get-involved.php', (string)v('involve', 'title'), (string)v('involve', 'lead'));
page_hero('involve');
involve_nav('get-involved.php');
$ways = [
    ['volunteer.php', '🙋', 'Volunteer', 'volunteer_text', count(opportunities('volunteer'))],
    ['careers.php',   '💼', 'Careers',   'careers_text',   count(opportunities('career'))],
    ['donate.php',    '❤️', 'Donate',    'donate_text',    0],
    ['contact.php',   '✉️', 'Contact us', 'contact_text',  0],
];
[$upcoming] = events_split();
?>
<section><div class="wrap"><div class="grid g4">
<?php foreach ($ways as [$href, $ico, $label, $key, $open]): ?>
<a class="card" href="<?= $href ?>" style="text-decoration:none;color:inherit">
  <div class="ico"><?= $ico ?></div><h3><?= e($label) ?></h3><p><?= t('involve', $key) ?></p>
  <?php if ($open): ?><p style="margin-top:10px"><span class="pill-open"><?= $open ?> open now</span></p><?php endif; ?>
</a>
<?php endforeach; ?>
</div></div></section>

<?php if ($upcoming): $n = $upcoming[0]; ?>
<section class="alt"><div class="wrap split">
<div>
<span class="eyebrow">Next event</span>
<h2><?= e($n['title']) ?></h2>
<p><?= e(when_label($n)) ?><?= !empty($n['venue']) ? ' · ' . e($n['venue']) : '' ?></p>
<div class="actions"><a class="btn" href="events.php">See all events</a></div>
</div>
<?= countdown($n['_ts'], 'on-light') ?>
</div></section>
<?php endif; ?>
<?php cta(); foot(); ?>

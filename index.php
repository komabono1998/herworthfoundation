<?php
require __DIR__ . '/inc/layout.php';
head('index.php', (string)v('home', 'title'), (string)v('site', 'tagline'));

$impact = v('about', 'impact');
$gallery = v('programs', 'gallery');
$latest = v('news', 'posts');
usort($latest, fn($a, $b) => strcmp((string)($b['date'] ?? ''), (string)($a['date'] ?? '')));
$latest = array_slice($latest, 0, 3);
[$upcoming] = events_split();
$next = $upcoming[0] ?? null;
$eventImg = safe_upload_path((string)v('home', 'event_image'));
?>
<section class="hero"><div class="wrap">
  <div>
    <span class="eyebrow"><?= t('home', 'eyebrow') ?></span>
    <h1><?= t('home', 'title') ?></h1>
    <p class="lead"><?= t('home', 'lead') ?></p>
    <?php if (v('site', 'motto') !== ''): ?><p class="motto"><?= t('site', 'motto') ?></p><?php endif; ?>
    <div class="actions">
      <a class="btn" href="get-involved.php"><?= t('site', 'cta_button') ?></a>
      <a class="btn outline" href="about.php">Our story</a>
    </div>
  </div>
  <div class="hero-art">
    <?= media((string)v('home', 'hero_image'), (string)v('home', 'who_art'), 'p') ?>
    <?php if (isset($impact[0])): ?><div class="badge"><b><?= e($impact[0]['value'] ?? '') ?></b><span><?= e($impact[0]['label'] ?? '') ?></span></div><?php endif; ?>
    <?php if (isset($impact[1])): ?><div class="badge two"><b><?= e($impact[1]['value'] ?? '') ?></b><span><?= e($impact[1]['label'] ?? '') ?></span></div><?php endif; ?>
  </div>
</div></section>

<?php if (v('home', 'stats')): ?>
<section class="band"><div class="wrap"><div class="box">
<?php foreach (v('home', 'stats') as $st): ?>
<div class="stat"><b><?= e($st['value'] ?? '') ?></b><span><?= e($st['label'] ?? '') ?></span></div>
<?php endforeach; ?>
</div></div></section>
<?php endif; ?>

<?php if ($next): ?>
<section style="padding:48px 0 0"><div class="wrap"><div class="nextbar">
  <div><span class="eyebrow">Next event</span><h3><?= e($next['title']) ?></h3>
  <p><?= e(when_label($next)) ?><?= !empty($next['venue']) ? ' · ' . e($next['venue']) : '' ?></p></div>
  <?= countdown($next['_ts'], 'on-light') ?>
  <div class="actions" style="margin:0"><a class="btn small" href="events.php">Event details</a><a class="btn small ghost" href="donate.php">❤ Donate</a></div>
</div></div></section>
<?php endif; ?>

<section><div class="wrap split">
<div class="frame"><?= media((string)v('home', 'who_image'), (string)v('home', 'who_art'), 'tall') ?></div>
<div>
<span class="eyebrow">Who we are</span>
<h2><?= t('home', 'who_title') ?></h2>
<?= paras((string)v('home', 'who_text')) ?>
<div class="actions"><a class="btn ghost" href="about.php">About us</a></div>
</div>
</div></section>

<section class="alt"><div class="wrap">
<div class="sec-head"><span class="eyebrow">What we do</span><h2><?= t('home', 'areas_title') ?></h2></div>
<div class="gcenter">
<?php foreach (v('programs', 'areas') as $a): ?>
<a class="card" href="programs.php" style="text-decoration:none;color:inherit"><div class="ico"><?= e($a['icon'] ?? '') ?></div><h3><?= e($a['title'] ?? '') ?></h3><p><?= e($a['short'] ?? '') ?></p></a>
<?php endforeach; ?>
</div>
<div class="actions"><a class="btn ghost" href="programs.php">See all programmes</a></div>
</div></section>

<section class="dark"><div class="wrap split flip">
<div class="frame"><?= media((string)v('home', 'feature_image'), (string)v('home', 'feature_title')) ?></div>
<div>
<span class="eyebrow"><?= t('home', 'feature_eyebrow') ?></span>
<h2><?= t('home', 'feature_title') ?></h2>
<?= paras((string)v('home', 'feature_text')) ?>
<div class="actions"><a class="btn" href="about.php#story"><?= t('home', 'feature_button') ?></a></div>
</div>
</div></section>

<section><div class="wrap">
<div class="banner"<?= $eventImg === '' ? ' style="background:linear-gradient(135deg,var(--black),var(--blue-d))"' : '' ?>>
<?php if ($eventImg !== ''): ?><img src="<?= e($eventImg) ?>" alt="<?= t('home', 'event_art') ?>" loading="lazy"><?php endif; ?>
<div class="in">
<span class="eyebrow"><?= t('event', 'eyebrow') ?></span>
<h2><?= t('home', 'event_title') ?></h2>
<?= paras((string)v('home', 'event_text')) ?>
<div class="actions"><a class="btn" href="event.php">About the run</a></div>
</div>
</div>
</div></section>

<?php if (($m = mosaic($gallery)) !== ''): ?>
<section class="alt"><div class="wrap">
<div class="sec-head"><span class="eyebrow">In pictures</span><h2><?= t('programs', 'gallery_title') ?></h2></div>
<?= $m ?>
</div></section>
<?php endif; ?>

<?php if ($latest): ?>
<section><div class="wrap">
<div class="sec-head"><span class="eyebrow"><?= t('news', 'eyebrow') ?></span><h2><?= t('news', 'title') ?></h2></div>
<div class="grid g3">
<?php foreach ($latest as $p): $ts = strtotime((string)($p['date'] ?? '')); ?>
<a class="card post" href="news.php" style="text-decoration:none;color:inherit">
  <?php if (safe_upload_path((string)($p['image'] ?? '')) !== ''): ?><?= media((string)$p['image'], (string)($p['title'] ?? '')) ?><?php endif; ?>
  <?php if ($ts): ?><time datetime="<?= date('Y-m-d', $ts) ?>"><?= date('j F Y', $ts) ?></time><?php endif; ?>
  <h3><?= e($p['title'] ?? '') ?></h3>
  <p><?= e(mb_strimwidth((string)($p['summary'] ?? ''), 0, 130, '…')) ?></p>
</a>
<?php endforeach; ?>
</div>
<div class="actions"><a class="btn ghost" href="news.php">All news &amp; stories</a></div>
</div></section>
<?php endif; ?>
<?php cta(); foot(); ?>

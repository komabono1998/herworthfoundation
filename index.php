<?php
require __DIR__ . '/inc/layout.php';
head('index.php', (string)v('home', 'title'));
?>
<section class="hero"><div class="wrap">
<span class="eyebrow"><?= t('home', 'eyebrow') ?></span>
<h1><?= t('home', 'title') ?></h1>
<p class="lead"><?= t('home', 'lead') ?></p>
<div class="actions"><a class="btn" href="contact.php"><?= t('site', 'cta_button') ?></a><a class="btn outline" href="about.php">Our story</a></div>
</div></section>

<?php if (v('home', 'stats')): ?>
<section class="alt"><div class="wrap"><div class="grid g3">
<?php foreach (v('home', 'stats') as $st): ?>
<div class="stat"><b><?= e($st['value'] ?? '') ?></b><span><?= e($st['label'] ?? '') ?></span></div>
<?php endforeach; ?>
</div></div></section>
<?php endif; ?>

<section><div class="wrap split">
<div>
<span class="eyebrow">Who we are</span>
<h2><?= t('home', 'who_title') ?></h2>
<?= paras((string)v('home', 'who_text')) ?>
<div class="actions"><a class="btn ghost" href="about.php">About us</a></div>
</div>
<?= media((string)v('home', 'who_image'), (string)v('home', 'who_art'), 'p') ?>
</div></section>

<section class="alt"><div class="wrap">
<span class="eyebrow">What we do</span><h2><?= t('home', 'areas_title') ?></h2>
<div class="grid g4">
<?php foreach (v('programs', 'areas') as $a): ?>
<div class="card"><div class="ico"><?= e($a['icon'] ?? '') ?></div><h3><?= e($a['title'] ?? '') ?></h3><p><?= e($a['short'] ?? '') ?></p></div>
<?php endforeach; ?>
</div>
<div class="actions"><a class="btn ghost" href="programs.php">See all programmes</a></div>
</div></section>

<section><div class="wrap split">
<?= media((string)v('home', 'event_image'), (string)v('home', 'event_art')) ?>
<div>
<span class="eyebrow"><?= t('event', 'eyebrow') ?></span>
<h2><?= t('home', 'event_title') ?></h2>
<?= paras((string)v('home', 'event_text')) ?>
<div class="actions"><a class="btn" href="event.php">About the run</a></div>
</div>
</div></section>
<?php cta(); foot(); ?>

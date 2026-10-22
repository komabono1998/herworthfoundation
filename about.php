<?php
require __DIR__ . '/inc/layout.php';
head('about.php', (string)v('about', 'eyebrow'), (string)v('about', 'lead'));
page_hero('about');
$pct = max(0, min(100, (int)v('about', 'female_pct')));
?>
<section><div class="wrap grid g2">
<div class="card"><div class="ico">🎯</div><h3>Our mission</h3><p><?= t('about', 'mission') ?></p></div>
<div class="card"><div class="ico">🔭</div><h3>Our vision</h3><p><?= t('about', 'vision') ?></p></div>
</div></section>

<section class="alt"><div class="wrap split">
<div>
<span class="eyebrow">Our reach</span><h2><?= t('about', 'reach_title') ?></h2>
<?= paras((string)v('about', 'reach_text')) ?>
<div class="bar-split" role="img" aria-label="<?= $pct ?>% female, <?= 100 - $pct ?>% male"><i style="width:<?= $pct ?>%"></i></div>
<p class="note"><?= $pct ?>% female · <?= 100 - $pct ?>% male</p>
</div>
<div class="frame"><?= media((string)v('about', 'reach_image'), (string)v('about', 'reach_art'), 'p') ?></div>
</div></section>

<section id="story"><div class="wrap split" style="align-items:start">
<div>
<span class="eyebrow">Our story</span><h2><?= t('about', 'story_title') ?></h2>
<?= paras((string)v('about', 'story_text')) ?>
</div>
<div>
<?php if (safe_upload_path((string)v('about', 'story_image')) !== ''): ?><div class="frame" style="margin-bottom:36px"><?= media((string)v('about', 'story_image'), (string)v('about', 'story_title')) ?></div><?php endif; ?>
<div class="card">
<h3><?= t('about', 'focus_title') ?></h3>
<ul class="ticks"><?php foreach (v('about', 'focus') as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul>
</div>
</div>
</div></section>

<?php if (v('about', 'impact')): ?>
<section class="alt"><div class="wrap">
<h2 style="text-align:center"><?= t('about', 'impact_title') ?></h2>
<div class="grid gauto band-lite" style="margin-top:28px">
<?php foreach (v('about', 'impact') as $st): ?>
<div class="stat"><b><?= e($st['value'] ?? '') ?></b><span><?= e($st['label'] ?? '') ?></span></div>
<?php endforeach; ?>
</div>
</div></section>
<?php endif; ?>
<?php cta(); foot(); ?>

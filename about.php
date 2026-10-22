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
<?= media((string)v('about', 'reach_image'), (string)v('about', 'reach_art'), 'p') ?>
</div></section>
<?php cta(); foot(); ?>

<?php
require __DIR__ . '/inc/layout.php';
head('event.php', (string)v('event', 'title'), (string)v('event', 'lead'));
page_hero('event');
?>
<section><div class="wrap split">
<div>
<h2><?= t('event', 'heading') ?></h2>
<?= paras((string)v('event', 'body')) ?>
<?php if (v('event', 'bullets')): ?><ul class="ticks" style="margin-top:16px"><?php foreach (v('event', 'bullets') as $b): ?><li><?= e($b) ?></li><?php endforeach; ?></ul><?php endif; ?>
<div class="actions"><a class="btn" href="contact.php"><?= t('event', 'button') ?></a></div>
</div>
<?= media((string)v('event', 'image'), (string)v('event', 'art')) ?>
</div></section>

<section class="alt"><div class="wrap split" style="align-items:start">
<div>
<span class="eyebrow">Latest edition</span>
<h2><?= t('event', 'latest_title') ?></h2>
<?= paras((string)v('event', 'latest_text')) ?>
</div>
<div class="card">
<h3>At a glance</h3>
<ul class="ticks"><?php foreach (v('event', 'latest_facts') as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul>
</div>
</div></section>
<?php cta(); foot(); ?>

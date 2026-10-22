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
<?php cta(); foot(); ?>

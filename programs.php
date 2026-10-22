<?php
require __DIR__ . '/inc/layout.php';
head('programs.php', (string)v('programs', 'eyebrow'), (string)v('programs', 'lead'));
page_hero('programs');
?>
<section><div class="wrap"><div class="grid g2">
<?php foreach (v('programs', 'areas') as $a): ?>
<div class="card"><div class="ico"><?= e($a['icon'] ?? '') ?></div><h3><?= e($a['title'] ?? '') ?></h3>
<p><?= e($a['text'] ?? '') ?></p>
<?php if (!empty($a['items'])): ?><ul class="ticks" style="margin-top:12px"><?php foreach ($a['items'] as $i): ?><li><?= e($i) ?></li><?php endforeach; ?></ul><?php endif; ?>
</div>
<?php endforeach; ?>
</div></div></section>

<?php if (v('programs', 'gallery')): ?>
<section class="alt"><div class="wrap">
<span class="eyebrow">In pictures</span><h2><?= t('programs', 'gallery_title') ?></h2>
<div class="grid g3">
<?php foreach (v('programs', 'gallery') as $n => $g): ?>
<figure class="shot"><?= media((string)($g['image'] ?? ''), (string)($g['caption'] ?? ''), $n % 2 ? '' : 'p') ?>
<?php if (!empty($g['image']) && !empty($g['caption'])): ?><figcaption><?= e($g['caption']) ?></figcaption><?php endif; ?></figure>
<?php endforeach; ?>
</div>
</div></section>
<?php endif; ?>
<?php cta(); foot(); ?>

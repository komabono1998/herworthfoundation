<?php
require __DIR__ . '/inc/layout.php';
head('partners.php', (string)v('partners', 'eyebrow'), (string)v('partners', 'lead'));
page_hero('partners');
?>
<section><div class="wrap"><div class="grid g4">
<?php foreach (v('partners', 'list') as $p):
    $logo = safe_upload_path((string)($p['logo'] ?? ''));
    $url = preg_match('#^https?://#i', (string)($p['url'] ?? '')) ? $p['url'] : '';
    $inner = ($logo !== '' ? '<img class="plogo" src="' . e($logo) . '" alt="" loading="lazy">' : '') . '<h3>' . e($p['name'] ?? '') . '</h3>';
?>
<div class="card partner"><?= $url ? '<a href="' . e($url) . '" target="_blank" rel="noopener">' . $inner . '</a>' : $inner ?></div>
<?php endforeach; ?>
</div></div></section>

<section class="alt"><div class="wrap" style="text-align:center">
<h2><?= t('partners', 'become_title') ?></h2>
<p class="lead" style="margin:0 auto"><?= t('partners', 'become_text') ?></p>
<div class="actions" style="justify-content:center"><a class="btn" href="contact.php">Partner with us</a></div>
</div></section>
<?php foot(); ?>

<?php
require __DIR__ . '/inc/layout.php';
head('news.php', (string)v('news', 'eyebrow'), (string)v('news', 'lead'));
page_hero('news');

$posts = v('news', 'posts');
usort($posts, fn($a, $b) => strcmp((string)($b['date'] ?? ''), (string)($a['date'] ?? '')));
?>
<section><div class="wrap">
<?php if (!$posts): ?><p class="lead">No stories yet. Please check back soon.</p><?php endif; ?>
<div class="grid g2">
<?php foreach ($posts as $p):
    $ts = strtotime((string)($p['date'] ?? ''));
    $url = preg_match('#^https?://#i', (string)($p['url'] ?? '')) ? $p['url'] : '';
?>
<article class="card post">
  <?php if (safe_upload_path((string)($p['image'] ?? '')) !== ''): ?><?= media((string)$p['image'], (string)($p['title'] ?? '')) ?><?php endif; ?>
  <?php if ($ts): ?><time datetime="<?= date('Y-m-d', $ts) ?>"><?= date('j F Y', $ts) ?></time><?php endif; ?>
  <h3><?= e($p['title'] ?? '') ?></h3>
  <p><?= e($p['summary'] ?? '') ?></p>
  <?php if ($url): ?><p><a href="<?= e($url) ?>" target="_blank" rel="noopener"><?= t('news', 'readmore') ?> →</a></p><?php endif; ?>
</article>
<?php endforeach; ?>
</div>
</div></section>
<?php cta(); foot(); ?>

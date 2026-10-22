<?php
require __DIR__ . '/inc/forms.php';
head('donate.php', (string)v('donate', 'title'), (string)v('donate', 'lead'));
page_hero('donate');
involve_nav('donate.php');
$url = (string)v('donate', 'payment_url');
$hasLink = preg_match('#^https?://#i', $url) === 1;
$methods = array_filter(v('donate', 'methods') ?: [], fn($m) => !empty($m['name']));
$email = (string)v('site', 'email');
?>
<section><div class="wrap split" style="align-items:start">
<div>
  <?php if ($hasLink): ?>
  <div class="card give">
    <h3>Give online</h3>
    <p><?= t('donate', 'payment_note') ?></p>
    <p><a class="btn big" href="<?= e($url) ?>" target="_blank" rel="noopener">❤ <?= t('donate', 'button_label') ?></a></p>
  </div>
  <?php endif; ?>

  <?php if ($methods): ?>
  <h2 style="margin-top:<?= $hasLink ? '40px' : '0' ?>"><?= t('donate', 'methods_title') ?></h2>
  <div class="grid" style="gap:16px">
    <?php foreach ($methods as $m): ?>
    <div class="card"><h3><?= e($m['name']) ?></h3><p class="pre"><?= nl2br(e($m['details'] ?? '')) ?></p></div>
    <?php endforeach; ?>
  </div>
  <?php elseif (!$hasLink): ?>
  <div class="card"><div class="ico">❤️</div><p class="lead" style="font-size:1.05rem"><?= t('donate', 'fallback') ?></p>
  <?php if ($email !== ''): ?><p style="margin-top:14px"><a class="btn" href="mailto:<?= e($email) ?>?subject=Donation">Email <?= e($email) ?></a></p><?php endif; ?></div>
  <?php endif; ?>
</div>

<div>
  <?php if (v('donate', 'steps')): ?>
  <div class="card">
    <h3><?= t('donate', 'steps_title') ?></h3>
    <ol class="steps"><?php foreach (v('donate', 'steps') as $s): ?><li><?= e($s) ?></li><?php endforeach; ?></ol>
  </div>
  <?php endif; ?>
  <?php if (v('donate', 'impact')): ?>
  <div class="card" style="margin-top:20px">
    <h3><?= t('donate', 'impact_title') ?></h3>
    <ul class="ticks"><?php foreach (v('donate', 'impact') as $i): ?><li><?= e($i) ?></li><?php endforeach; ?></ul>
  </div>
  <?php endif; ?>
</div>
</div></section>

<section class="alt"><div class="wrap" style="text-align:center">
<p class="lead" style="margin:0 auto"><?= t('donate', 'receipt_note') ?></p>
<div class="actions" style="justify-content:center"><a class="btn ghost" href="contact.php">Contact us</a><a class="btn" href="events.php">See our events</a></div>
</div></section>
<?php foot(); ?>

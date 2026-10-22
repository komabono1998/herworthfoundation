<?php
require __DIR__ . '/inc/forms.php';
head('careers.php', (string)v('involve', 'c_title'), (string)v('involve', 'c_lead'));
page_hero('involve', 'c_');
involve_nav('careers.php');
$jobs = opportunities('career');
?>
<section><div class="wrap">
<div class="sec-head"><h2><?= t('involve', 'c_list_title') ?></h2></div>
<?php if ($jobs): ?>
<div class="grid g2"><?php foreach ($jobs as $o) echo opening_card($o); ?></div>
<?php else: ?>
<div class="card" style="max-width:640px"><div class="ico">💼</div><p class="lead" style="font-size:1.05rem"><?= t('involve', 'c_empty') ?></p>
<p style="margin-top:16px"><a class="btn ghost" href="news.php">Follow our news</a></p></div>
<?php endif; ?>
</div></section>
<?php cta(); foot(); ?>

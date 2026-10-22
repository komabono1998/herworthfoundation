<?php
require __DIR__ . '/inc/forms.php';
head('volunteer.php', (string)v('involve', 'v_title'), (string)v('involve', 'v_lead'));
page_hero('involve', 'v_');
involve_nav('volunteer.php');
$roles = opportunities('volunteer');
?>
<section><div class="wrap">
<div class="sec-head"><h2><?= t('involve', 'v_list_title') ?></h2></div>
<?php if ($roles): ?>
<div class="grid g2"><?php foreach ($roles as $o) echo opening_card($o); ?></div>
<?php else: ?>
<p class="lead"><?= t('involve', 'v_empty') ?></p>
<?php endif; ?>
</div></section>

<section class="alt"><div class="wrap split">
<div>
<span class="eyebrow">Open to everyone</span>
<h2><?= t('involve', 'v_general_title') ?></h2>
<?= paras((string)v('involve', 'v_general_text')) ?>
<div class="actions"><a class="btn" href="apply.php">Register your interest</a></div>
</div>
<div class="frame"><?= media((string)v('about', 'reach_image'), 'Volunteer with us') ?></div>
</div></section>
<?php cta(); foot(); ?>

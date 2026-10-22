<?php
require __DIR__ . '/inc/forms.php';
head('events.php', (string)v('events', 'title'), (string)v('events', 'lead'));
page_hero('events');
[$upcoming, $past] = events_split();
$next = $upcoming[0] ?? null;
$rest = array_slice($upcoming, 1);

function event_card(array $e, bool $isPast = false): string
{
    $ts = $e['_ts'];
    $img = safe_upload_path((string)($e['image'] ?? ''));
    $link = preg_match('#^https?://#i', (string)($e['link'] ?? '')) ? $e['link'] : '';
    $h = '<article class="card event' . ($isPast ? ' past' : '') . '">';
    if ($img !== '') $h .= '<img class="photo" src="' . e($img) . '" alt="' . e($e['title']) . '" loading="lazy">';
    $h .= '<div class="evbody"><div class="date-chip"><b>' . date('j', $ts) . '</b><span>' . date('M', $ts) . '</span></div><div>';
    $h .= '<h3>' . e($e['title']) . '</h3>';
    $h .= '<ul class="meta">';
    $h .= '<li>' . icon('calendar') . e(when_label($e)) . '</li>';
    if (!empty($e['venue'])) $h .= '<li>' . icon('pin') . e($e['venue']) . '</li>';
    if (!empty($e['host']))  $h .= '<li>' . icon('user') . 'Hosted by ' . e($e['host']) . '</li>';
    $h .= '</ul>';
    if (!empty($e['summary'])) $h .= '<p>' . e($e['summary']) . '</p>';
    if (!$isPast) {
        $h .= countdown($ts, 'small');
        $h .= '<div class="actions">' . ($link ? '<a class="btn small" href="' . e($link) . '" target="_blank" rel="noopener">Register / learn more</a>' : '')
            . '<a class="btn small ghost" href="donate.php">❤ Donate</a></div>';
    }
    return $h . '</div></div></article>';
}
?>
<?php if ($next): ?>
<section class="spotlight"><div class="wrap">
  <span class="eyebrow">Next event</span>
  <h2><?= e($next['title']) ?></h2>
  <ul class="meta light">
    <li><?= icon('calendar') ?><?= e(when_label($next)) ?></li>
    <?php if (!empty($next['venue'])): ?><li><?= icon('pin') ?><?= e($next['venue']) ?></li><?php endif; ?>
    <?php if (!empty($next['host'])): ?><li><?= icon('user') ?>Hosted by <?= e($next['host']) ?></li><?php endif; ?>
  </ul>
  <?php if (!empty($next['summary'])): ?><p class="lead" style="color:#cdd8ee"><?= e($next['summary']) ?></p><?php endif; ?>
  <?= countdown($next['_ts']) ?>
  <div class="actions">
    <?php if (preg_match('#^https?://#i', (string)($next['link'] ?? ''))): ?><a class="btn light" href="<?= e($next['link']) ?>" target="_blank" rel="noopener">Register / learn more</a><?php endif; ?>
    <a class="btn" href="donate.php">❤ <?= t('donate', 'button_label') ?></a>
  </div>
</div></section>
<?php endif; ?>

<section><div class="wrap">
<?php if (!$upcoming): ?>
  <div class="card" style="max-width:640px"><div class="ico">📅</div><p class="lead" style="font-size:1.05rem"><?= t('events', 'empty_text') ?></p></div>
<?php elseif ($rest): ?>
  <div class="sec-head"><h2>Also coming up</h2></div>
  <div class="grid g2"><?php foreach ($rest as $e) echo event_card($e); ?></div>
<?php endif; ?>
<?php if ($past): ?>
  <div class="sec-head" style="margin-top:64px"><h2>Past events</h2></div>
  <div class="grid g2"><?php foreach ($past as $e) echo event_card($e, true); ?></div>
<?php endif; ?>
</div></section>

<section class="alt"><div class="wrap split">
<div>
<h2><?= t('events', 'support_title') ?></h2>
<?= paras((string)v('events', 'support_text')) ?>
<div class="actions"><a class="btn" href="donate.php">❤ <?= t('donate', 'button_label') ?></a><a class="btn ghost" href="event.php">The Menstrual Hygiene Run</a></div>
</div>
<div class="frame"><?= media((string)v('event', 'image'), (string)v('event', 'art')) ?></div>
</div></section>
<?php foot(); ?>

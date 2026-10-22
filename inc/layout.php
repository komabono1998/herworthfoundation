<?php
require_once __DIR__ . '/bootstrap.php';

const NAV = [
    'index.php'    => 'Home',
    'about.php'    => 'About',
    'programs.php' => 'What We Do',
    'event.php'    => 'Menstrual Hygiene Run',
    'news.php'     => 'News',
    'partners.php' => 'Partners',
    'contact.php'  => 'Get Involved',
];

const SOCIALS = [
    'facebook'  => 'Facebook',
    'instagram' => 'Instagram',
    'x'         => 'X',
    'blog'      => 'Blog',
    'tiktok'    => 'TikTok',
    'youtube'   => 'YouTube',
    'linkedin'  => 'LinkedIn',
];

function icon(string $name): string
{
    static $p = [
        'pin'       => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
        'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'facebook'  => '<path d="M14 8.5h2V4.5h-2.5A3.5 3.5 0 0 0 10 8v2H8v3.5h2V20h3.5v-6.5H16l.5-3.5h-3V8.5"/>',
        'instagram' => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".6"/>',
        'x'         => '<path d="M5 4l14 16M19 4L5 20"/>',
        'tiktok'    => '<path d="M14 4v10.5a3.5 3.5 0 1 1-3.5-3.5M14 4c.3 2.4 1.9 4 4.5 4.2"/>',
        'youtube'   => '<rect x="3" y="6" width="18" height="12" rx="4"/><path d="M10.5 9.5v5l4.2-2.5z"/>',
        'linkedin'  => '<path d="M7 10v7M7 7v.1M11 17v-7M11 13a3 3 0 0 1 6 0v4"/>',
        'whatsapp'  => '<path d="M4 20l1.2-3.8A8 8 0 1 1 8 19z"/><path d="M9 9c0 3 3 6 6 6l1-1.5-2-1-1 .8c-1-.4-2-1.4-2.4-2.4l.8-1-1-2z"/>',
        'blog'      => '<path d="M5 4h10l4 4v12H5z"/><path d="M9 12h6M9 16h6M9 8h3"/>',
    ];
    return '<svg class="ic" viewBox="0 0 24 24" aria-hidden="true">' . ($p[$name] ?? '') . '</svg>';
}

function social_links(): string
{
    $out = '';
    foreach (SOCIALS as $key => $label) {
        $url = (string)v('site', $key);
        if (preg_match('#^https?://#i', $url)) {
            $out .= '<a href="' . e($url) . '" target="_blank" rel="noopener" aria-label="' . e($label) . '" title="' . e($label) . '">' . icon($key) . '</a>';
        }
    }
    $wa = digits((string)v('site', 'whatsapp'));
    if ($wa !== '') $out .= '<a href="https://wa.me/' . $wa . '" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp">' . icon('whatsapp') . '</a>';
    return $out;
}

/** Two-line wordmark: "Her Worth" over "Foundation" when the name ends in Foundation. */
function brand_html(): string
{
    $name = trim((string)v('site', 'name'));
    $main = $name;
    $sub = '';
    if (preg_match('/^(.*\S)\s+(Foundation)$/i', $name, $m)) { $main = $m[1]; $sub = $m[2]; }
    $initials = '';
    foreach (preg_split('/\s+/', $main) as $w) $initials .= mb_strtoupper(mb_substr($w, 0, 1));
    return '<a class="brand" href="index.php"><i>' . e(mb_substr($initials, 0, 2)) . '</i><span><b>' . e($main) . '</b>'
        . ($sub !== '' ? '<small>' . e($sub) . '</small>' : '') . '</span></a>';
}

/** Photo mosaic from a list of items with 'image' and 'caption'. */
function mosaic(array $items): string
{
    $out = '';
    foreach ($items as $g) {
        $img = safe_upload_path((string)($g['image'] ?? ''));
        if ($img === '') continue;
        $cap = (string)($g['caption'] ?? '');
        $out .= '<figure><img src="' . e($img) . '" alt="' . e($cap) . '" loading="lazy">'
            . ($cap !== '' ? '<figcaption>' . e($cap) . '</figcaption>' : '') . '</figure>';
    }
    return $out === '' ? '' : '<div class="mosaic">' . $out . '</div>';
}

function head(string $file, string $title, string $desc = ''): void
{
    $name = t('site', 'name');
    $desc = $desc !== '' ? $desc : (string)v('site', 'tagline');
    ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> | <?= $name ?></title>
<meta name="description" content="<?= e($desc) ?>">
<meta name="theme-color" content="#1c4fb8">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body id="top">
<header><div class="wrap bar">
<?= brand_html() ?>
<nav aria-label="Main"><ul>
<?php foreach (NAV as $href => $label): ?>
<li><a href="<?= $href ?>"<?= $href === $file ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
<?php endforeach; ?>
</ul></nav>
</div></header>
<main>
<?php
}

function page_hero(string $section): void
{
    ?>
<section class="page-hero"><div class="wrap"><span class="eyebrow"><?= t($section, 'eyebrow') ?></span><h1><?= t($section, 'title') ?></h1>
<p class="lead"><?= t($section, 'lead') ?></p></div></section>
<?php
}

function cta(): void
{
    ?>
<section class="cta"><div class="wrap">
<h2><?= t('site', 'cta_title') ?></h2>
<p><?= t('site', 'cta_text') ?></p>
<div class="actions"><a class="btn light" href="contact.php"><?= t('site', 'cta_button') ?></a></div>
</div></section>
<?php
}

function foot(): void
{
    $s = content()['site'];
    $phone = (string)$s['phone'];
    $socials = social_links();
    ?>
</main>
<footer>
<div class="wrap fmain"><div class="fgrid">
  <div class="fbrand">
    <?= brand_html() ?>
    <p><?= e($s['blurb']) ?></p>
    <?php if ($s['motto'] !== ''): ?><p class="motto">“<?= e($s['motto']) ?>”</p><?php endif; ?>
    <?php if ($socials !== ''): ?><div class="socials" aria-label="Social media"><?= $socials ?></div><?php endif; ?>
  </div>
  <div>
    <h3>Explore</h3>
    <ul class="flinks"><?php foreach (NAV as $href => $label): ?><li><a href="<?= $href ?>"><?= e($label) ?></a></li><?php endforeach; ?></ul>
  </div>
  <div>
    <h3>What we do</h3>
    <ul class="flinks"><?php foreach (v('programs', 'areas') as $a): ?><li><a href="programs.php"><?= e($a['title'] ?? '') ?></a></li><?php endforeach; ?></ul>
  </div>
  <div>
    <h3>Contact us</h3>
    <ul class="contact-list">
      <?php if ($s['address'] !== ''): ?><li><?= icon('pin') ?><span><?= nl2br(e($s['address'])) ?></span></li><?php endif; ?>
      <?php if ($phone !== ''): ?><li><?= icon('phone') ?><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $phone)) ?>"><?= e($phone) ?></a></li><?php endif; ?>
      <?php if ($s['email'] !== ''): ?><li><?= icon('mail') ?><a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a></li><?php endif; ?>
      <?php if ($s['hours'] !== ''): ?><li><?= icon('clock') ?><span><?= nl2br(e($s['hours'])) ?></span></li><?php endif; ?>
    </ul>
    <div class="actions" style="margin-top:20px"><a class="btn small" style="color:#fff" href="contact.php"><?= e($s['cta_button']) ?></a></div>
  </div>
</div></div>
<div class="fbar"><div class="wrap">
  <span>© <?= date('Y') ?> <?= e($s['name']) ?>. All rights reserved.</span>
  <a href="#top">Back to top ↑</a>
</div></div>
</footer>
</body>
</html>
<?php
}

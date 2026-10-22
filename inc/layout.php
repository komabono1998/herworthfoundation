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

function social_links(): string
{
    $out = '';
    foreach (SOCIALS as $key => $label) {
        $url = (string)v('site', $key);
        if (preg_match('#^https?://#i', $url)) {
            $out .= '<a href="' . e($url) . '" target="_blank" rel="noopener">' . e($label) . '</a>';
        }
    }
    $wa = digits((string)v('site', 'whatsapp'));
    if ($wa !== '') $out .= '<a href="https://wa.me/' . $wa . '" target="_blank" rel="noopener">WhatsApp</a>';
    return $out;
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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header><div class="wrap bar">
<a class="brand" href="index.php"><i>♀</i><?= $name ?></a>
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
<footer><div class="wrap">
<div class="fgrid">
  <div class="fbrand">
    <a class="brand" href="index.php"><i>♀</i><?= e($s['name']) ?></a>
    <p><?= e($s['blurb']) ?></p>
    <?php if ($socials !== ''): ?><div class="socials" aria-label="Social media"><?= $socials ?></div><?php endif; ?>
  </div>
  <div>
    <h3>Explore</h3>
    <ul><?php foreach (NAV as $href => $label): ?><li><a href="<?= $href ?>"><?= e($label) ?></a></li><?php endforeach; ?></ul>
  </div>
  <div>
    <h3>What we do</h3>
    <ul><?php foreach (v('programs', 'areas') as $a): ?><li><a href="programs.php"><?= e($a['title'] ?? '') ?></a></li><?php endforeach; ?></ul>
  </div>
  <div>
    <h3>Contact</h3>
    <ul class="contact-list">
      <?php if ($s['address'] !== ''): ?><li><span>Address</span><?= nl2br(e($s['address'])) ?></li><?php endif; ?>
      <?php if ($phone !== ''): ?><li><span>Phone</span><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $phone)) ?>"><?= e($phone) ?></a></li><?php endif; ?>
      <?php if ($s['email'] !== ''): ?><li><span>Email</span><a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a></li><?php endif; ?>
      <?php if ($s['hours'] !== ''): ?><li><span>Hours</span><?= nl2br(e($s['hours'])) ?></li><?php endif; ?>
    </ul>
  </div>
</div>
<p class="copy">© <?= date('Y') ?> <?= e($s['name']) ?>. <?= e($s['motto'] !== '' ? $s['motto'] : $s['tagline'] . '.') ?></p>
</div></footer>
</body>
</html>
<?php
}

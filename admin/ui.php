<?php
// Admin page shell: sidebar, top bar, icons. Included by admin/index.php.

function aic(string $name, string $class = 'ai'): string
{
    static $p = [
        'grid'      => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'home'      => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
        'info'      => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8v.1"/>',
        'layers'    => '<path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/>',
        'flag'      => '<path d="M5 21V4"/><path d="M5 4h12l-2 4 2 4H5"/>',
        'news'      => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/>',
        'users'     => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><circle cx="17" cy="9" r="2.5"/><path d="M17 14a5 5 0 0 1 4.5 6"/>',
        'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/>',
        'file'      => '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 12h6M9 16h6"/>',
        'calendar'  => '<rect x="3.5" y="5" width="17" height="15" rx="2.5"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
        'heart'     => '<path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"/>',
        'inbox'     => '<path d="M3 13l3-8h12l3 8v6H3z"/><path d="M3 13h5l1 3h6l1-3h5"/>',
        'settings'  => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/>',
        'lock'      => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
        'ext'       => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
        'plus'      => '<path d="M12 5v14M5 12h14"/>',
        'logout'    => '<path d="M10 4H5v16h5M15 8l4 4-4 4M19 12H9"/>',
        'check'     => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'circle'    => '<circle cx="12" cy="12" r="8"/>',
        'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'pin'       => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'save'      => '<path d="M5 4h11l3 3v13H5z"/><path d="M8 4v5h7V4M8 20v-6h8v6"/>',
    ];
    return '<svg class="' . $class . '" viewBox="0 0 24 24" aria-hidden="true">' . ($p[$name] ?? '') . '</svg>';
}

/** Sidebar structure: group => [view key, label, icon]. Live URLs and blurbs per editable section. */
function admin_nav(): array
{
    return [
        'Overview' => [['dashboard', 'Dashboard', 'grid']],
        'Website pages' => [
            ['home', 'Home page', 'home'], ['about', 'About', 'info'], ['programs', 'What We Do', 'layers'],
            ['event', 'Menstrual Hygiene Run', 'flag'], ['news', 'News & stories', 'news'],
            ['partners', 'Partners', 'users'], ['contact', 'Contact page', 'mail'],
        ],
        'Engage' => [
            ['opportunities', 'Openings', 'briefcase'], ['applications', 'Applications', 'file'],
            ['events', 'Events', 'calendar'], ['involve', 'Get Involved pages', 'users'], ['donate', 'Donate', 'heart'],
        ],
        'Inbox' => [['messages', 'Messages', 'inbox']],
        'Settings' => [['site', 'Contact, social & footer', 'settings'], ['password', 'Account & password', 'lock']],
    ];
}

function section_meta(): array
{
    return [
        'home'          => ['index.php',        'Landing page: hero, key numbers, programmes, run banner and news teasers.'],
        'about'         => ['about.php',        'Mission, vision, our story, impact numbers and focus areas.'],
        'programs'      => ['programs.php',     'Areas of work and the photo gallery.'],
        'event'         => ['event.php',        'The annual Gulu Menstrual Hygiene Run page.'],
        'news'          => ['news.php',         'Stories and updates shown on the News page and home page.'],
        'partners'      => ['partners.php',     'Partner organisations and the “become a partner” box.'],
        'contact'       => ['contact.php',      'Text on the contact form page.'],
        'opportunities' => ['careers.php',      'Create, open and close volunteer roles and jobs. Open roles appear on the Volunteer and Careers pages.'],
        'events'        => ['events.php',       'Add events with a date, venue and host. The site shows a live countdown to the next one.'],
        'involve'       => ['get-involved.php', 'Text for the Get Involved, Volunteer and Careers pages and the application form.'],
        'donate'        => ['donate.php',       'Payment link and donation procedures shown on the Donate page.'],
        'site'          => ['contact.php',      'Email, phone, address, social links, motto and the footer. Used across the whole site.'],
    ];
}

function admin_title(string $view): string
{
    foreach (admin_nav() as $items) foreach ($items as [$k, $label]) if ($k === $view) return $label;
    return 'Admin';
}

function admin_open(string $view, array $badges = [], string $pageTitle = '', string $actions = ''): void
{
    $title = $pageTitle !== '' ? $pageTitle : admin_title($view);
    $site = t('site', 'name');
    ?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex">
<title><?= e($title) ?> · Admin · <?= $site ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head><body>
<aside id="side">
  <a class="logo" href="index.php"><span class="mark">HW</span><span><b><?= $site ?></b><small>Admin console</small></span></a>
  <nav>
  <?php foreach (admin_nav() as $group => $items): ?>
    <span class="cap"><?= e($group) ?></span>
    <?php foreach ($items as [$key, $label, $icon]): $n = $badges[$key] ?? 0; ?>
      <a href="<?= $key === 'dashboard' ? 'index.php' : '?s=' . $key ?>" <?= $view === $key ? 'class="on" aria-current="page"' : '' ?>>
        <?= aic($icon) ?><span><?= e($label) ?></span><?= $n ? '<b class="pill">' . $n . '</b>' : '' ?>
      </a>
    <?php endforeach; ?>
  <?php endforeach; ?>
  </nav>
  <div class="sidefoot">
    <a href="../index.php" target="_blank" rel="noopener"><?= aic('ext') ?><span>View website</span></a>
    <a href="logout.php"><?= aic('logout') ?><span>Sign out</span></a>
  </div>
</aside>
<div id="scrim"></div>
<div class="main">
  <header class="top">
    <button class="burger" id="burger" aria-label="Menu"><?= aic('menu') ?></button>
    <div class="crumbs"><span>Admin</span><span>/</span><b><?= e($title) ?></b></div>
    <div class="top-actions"><?= $actions ?></div>
  </header>
  <main>
<?= flashes() ?>
<?php
}

function admin_close(): void
{
    ?>
  </main>
</div>
<script src="admin.js"></script>
</body></html>
<?php
}

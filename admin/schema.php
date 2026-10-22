<?php
// Describes every editable field. Keys match inc/defaults.php.
$hero = fn(string $eyebrow = 'Small label above the title') => [
    'eyebrow' => F('Small label', 'text', ['hint' => $eyebrow]),
    'title'   => F('Page title'),
    'lead'    => F('Intro sentence', 'textarea', ['rows' => 3]),
];

return [
    'site' => ['label' => 'Contact, social & footer', 'icon' => '⚙', 'fields' => [
        '_a' => F('Organisation', 'heading'),
        'name'    => F('Organisation name'),
        'tagline' => F('Tagline'),
        'motto'   => F('Motto', 'text', ['hint' => 'Shown on the home page and in the footer.']),
        'blurb'   => F('Short description (shown in the footer)', 'textarea', ['rows' => 3]),
        '_b' => F('Contact & address', 'heading'),
        'address'  => F('Address', 'textarea', ['rows' => 3, 'ph' => 'Plot 1, Example Road, Gulu City']),
        'email'    => F('Email', 'email', ['ph' => 'info@yourdomain.org']),
        'phone'    => F('Phone', 'text', ['ph' => '+256 700 000000']),
        'whatsapp' => F('WhatsApp number', 'text', ['ph' => '+256700000000', 'hint' => 'Numbers only with country code. Creates a WhatsApp chat link.']),
        'hours'    => F('Opening hours', 'textarea', ['rows' => 2, 'ph' => 'Mon–Fri, 9am–5pm']),
        '_c' => F('Social media links', 'heading'),
        'facebook'  => F('Facebook', 'url', ['ph' => 'https://facebook.com/yourpage']),
        'instagram' => F('Instagram', 'url', ['ph' => 'https://instagram.com/yourhandle']),
        'x'         => F('X (Twitter)', 'url', ['ph' => 'https://x.com/yourhandle']),
        'blog'      => F('Blog', 'url', ['ph' => 'https://yourblog.blogspot.com']),
        'tiktok'    => F('TikTok', 'url', ['ph' => 'https://tiktok.com/@yourhandle']),
        'youtube'   => F('YouTube', 'url', ['ph' => 'https://youtube.com/@yourchannel']),
        'linkedin'  => F('LinkedIn', 'url', ['ph' => 'https://linkedin.com/company/yourpage']),
        '_d' => F('Call-to-action banner (bottom of most pages)', 'heading'),
        'cta_title'  => F('Heading'),
        'cta_text'   => F('Text', 'textarea', ['rows' => 2]),
        'cta_button' => F('Button label'),
    ]],

    'home' => ['label' => 'Home page', 'icon' => '⌂', 'fields' => [
        '_a' => F('Top banner', 'heading'),
        'eyebrow' => F('Small label'),
        'title'   => F('Main heading'),
        'lead'    => F('Intro text', 'textarea', ['rows' => 3]),
        '_b' => F('Key numbers', 'heading'),
        'stats' => F('Numbers', 'list', ['item' => 'number', 'fields' => [
            'value' => F('Number or value', 'text', ['ph' => '10,500+']),
            'label' => F('What it means'),
        ]]),
        '_c' => F('Who we are', 'heading'),
        'who_title' => F('Heading'),
        'who_text'  => F('Text', 'textarea'),
        'who_image' => F('Photo', 'image'),
        'who_art'   => F('Caption shown when there is no photo'),
        '_d' => F('What we do section', 'heading'),
        'areas_title' => F('Heading', 'text', ['hint' => 'The four cards come from What We Do → Areas of work.']),
        '_e' => F('Annual event teaser', 'heading'),
        'event_title' => F('Heading'),
        'event_text'  => F('Text', 'textarea', ['rows' => 4]),
        'event_image' => F('Photo', 'image'),
        'event_art'   => F('Caption shown when there is no photo'),
    ]],

    'about' => ['label' => 'About page', 'icon' => 'ℹ', 'fields' => $hero() + [
        '_a' => F('Mission & vision', 'heading'),
        'mission' => F('Mission', 'textarea', ['rows' => 3]),
        'vision'  => F('Vision', 'textarea', ['rows' => 3]),
        '_b' => F('Our reach', 'heading'),
        'reach_title' => F('Heading'),
        'reach_text'  => F('Text', 'textarea'),
        'female_pct'  => F('Percentage female', 'number', ['hint' => '0–100. The rest is shown as male.']),
        'reach_image' => F('Photo', 'image'),
        'reach_art'   => F('Caption shown when there is no photo'),
        '_c' => F('Our story', 'heading'),
        'story_title' => F('Heading'),
        'story_text'  => F('Story', 'textarea', ['rows' => 9, 'hint' => 'Leave a blank line between paragraphs.']),
        '_d' => F('Impact numbers', 'heading'),
        'impact_title' => F('Heading'),
        'impact' => F('Numbers', 'list', ['item' => 'number', 'fields' => [
            'value' => F('Number', 'text', ['ph' => '5,000+']),
            'label' => F('What it means'),
        ]]),
        '_e' => F('What we work on', 'heading'),
        'focus_title' => F('Heading'),
        'focus' => F('Focus areas', 'lines'),
    ]],

    'programs' => ['label' => 'What We Do page', 'icon' => '✿', 'fields' => $hero() + [
        'areas' => F('Areas of work', 'list', ['item' => 'area', 'fields' => [
            'icon'  => F('Emoji icon', 'text', ['ph' => '🌍']),
            'title' => F('Title'),
            'short' => F('Short description (home page card)', 'textarea', ['rows' => 2]),
            'text'  => F('Full description (this page)', 'textarea', ['rows' => 3]),
            'items' => F('Bullet points (optional)', 'lines', ['rows' => 3]),
        ]]),
        '_a' => F('Photo gallery', 'heading'),
        'gallery_title' => F('Heading'),
        'gallery' => F('Photos', 'list', ['item' => 'photo', 'fields' => [
            'caption' => F('Caption'),
            'image'   => F('Photo', 'image'),
        ]]),
    ]],

    'event' => ['label' => 'Menstrual Hygiene Run', 'icon' => '🏃', 'fields' => $hero() + [
        'heading' => F('Section heading'),
        'body'    => F('Description', 'textarea', ['rows' => 8, 'hint' => 'Leave a blank line between paragraphs.']),
        'bullets' => F('Highlights', 'lines'),
        'button'  => F('Button label'),
        'image'   => F('Photo', 'image'),
        'art'     => F('Caption shown when there is no photo'),
        '_a' => F('Latest edition', 'heading'),
        'latest_title' => F('Heading'),
        'latest_text'  => F('Description', 'textarea', ['rows' => 7, 'hint' => 'Leave a blank line between paragraphs.']),
        'latest_facts' => F('Key facts', 'lines'),
    ]],

    'news' => ['label' => 'News & Stories', 'icon' => '📰', 'fields' => $hero() + [
        'readmore' => F('“Read more” link label'),
        'posts' => F('Posts', 'list', ['item' => 'post', 'fields' => [
            'title'   => F('Title'),
            'date'    => F('Date', 'text', ['ph' => '2026-09-21', 'hint' => 'Format YYYY-MM-DD.']),
            'summary' => F('Summary', 'textarea', ['rows' => 4]),
            'image'   => F('Photo (optional)', 'image'),
            'url'     => F('Link to full story (optional)', 'url', ['ph' => 'https://']),
        ]]),
    ]],

    'partners' => ['label' => 'Partners page', 'icon' => '🤝', 'fields' => $hero() + [
        'list' => F('Partners', 'list', ['item' => 'partner', 'fields' => [
            'name' => F('Name'),
            'logo' => F('Logo (optional)', 'image'),
            'url'  => F('Website (optional)', 'url', ['ph' => 'https://']),
        ]]),
        '_a' => F('Become a partner box', 'heading'),
        'become_title' => F('Heading'),
        'become_text'  => F('Text', 'textarea', ['rows' => 2]),
    ]],

    'contact' => ['label' => 'Get Involved page', 'icon' => '✉', 'fields' => $hero() + [
        'form_title' => F('Form heading'),
        'subjects'   => F('“I’d like to…” choices', 'lines'),
        'thanks'     => F('Message shown after sending', 'textarea', ['rows' => 2]),
        'find_title' => F('Contact details heading', 'text', ['hint' => 'Details themselves come from Contact, social & footer.']),
    ]],
];

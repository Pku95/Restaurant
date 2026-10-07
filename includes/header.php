<?php
/**
 * Shared page header.
 * Before including this file, a page may set:
 *   $pageTitle       — the browser tab title (without the site name)
 *   $pageDescription — the meta description
 *   $active          — one of: home, menu, gallery, about, contact
 *   $bodyClass       — optional extra class on <body>
 */

if (!defined('SITE_NAME')) {
    http_response_code(403);
    exit('This file cannot be loaded directly.');
}

$pageTitle = isset($pageTitle) ? $pageTitle : '';
$pageDescription = isset($pageDescription) ? $pageDescription
    : SITE_NAME . ' — ' . SITE_TAGLINE . ' in Banani, Dhaka. Charcoal kebabs, dum-cooked kacchi biryani and a private dining room.';
$active = isset($active) ? $active : '';
$bodyClass = isset($bodyClass) ? $bodyClass : '';

$navItems = [
    'home'    => ['href' => 'index.php', 'label' => 'Home'],
    'menu'    => ['href' => 'menu.php', 'label' => 'Menu'],
    'gallery' => ['href' => 'gallery.php', 'label' => 'Gallery'],
    'about'   => ['href' => 'about.php', 'label' => 'About'],
    'contact' => ['href' => 'contact.php', 'label' => 'Contact'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME . ' — ' . SITE_TAGLINE) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="theme-color" content="#14100d">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='22' fill='%2314100d'/%3E%3Ctext x='50' y='72' font-size='62' text-anchor='middle' fill='%23d69a2d'%3E%E2%9C%A6%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= e($bodyClass) ?>">

<header class="site-header">
    <div class="topbar">
        <div class="wrap">
            <span><?= e(SITE_ADDRESS) ?></span>
            <div class="topbar__right">
                <span><?= e(site_hours()[0]['days'] . ': ' . site_hours()[0]['time']) ?></span>
                <a href="tel:<?= e(SITE_PHONE_HREF) ?>"><?= e(SITE_PHONE) ?></a>
            </div>
        </div>
    </div>

    <div class="header-bar">
        <div class="wrap">
            <a class="brand" href="index.php">
                <span class="brand__mark">&#10022;</span>
                <span>
                    <span class="brand__name"><?= e(SITE_NAME) ?></span>
                    <span class="brand__tag"><?= e(SITE_TAGLINE) ?></span>
                </span>
            </a>

            <nav class="nav">
                <?php foreach ($navItems as $key => $item): ?>
                    <a href="<?= e($item['href']) ?>" class="<?= $active === $key ? 'is-active' : '' ?>"><?= e($item['label']) ?></a>
                <?php endforeach; ?>
            </nav>

            <div class="header-actions">
                <a class="btn btn--primary" href="reservations.php">Book a table</a>
                <button class="burger" type="button" aria-label="Toggle navigation" aria-expanded="false">
                    <span></span><span></span>
                </button>
            </div>
        </div>
    </div>

    <nav class="mobile-nav" id="mobile-nav">
        <?php foreach ($navItems as $key => $item): ?>
            <a href="<?= e($item['href']) ?>"><?= e($item['label']) ?></a>
        <?php endforeach; ?>
        <a class="btn btn--primary" href="reservations.php">Book a table</a>
        <span class="mobile-nav__call">or call <?= e(SITE_PHONE) ?></span>
    </nav>
</header>

<?php $flash = flash_get(); ?>
<?php if ($flash): ?>
    <div class="flash">
        <div class="alert <?= $flash['type'] === 'error' ? 'alert--error' : 'alert--success' ?>">
            <?= e($flash['message']) ?>
        </div>
    </div>
<?php endif; ?>

<main>

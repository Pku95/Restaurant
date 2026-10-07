<?php
/**
 * Shared admin chrome.
 * Set $adminTitle and $adminActive before including this file.
 */

if (!defined('SITE_NAME')) {
    http_response_code(403);
    exit('This file cannot be loaded directly.');
}

$adminTitle = isset($adminTitle) ? $adminTitle : '';
$adminActive = isset($adminActive) ? $adminActive : '';

$unreadRow = db_one('SELECT COUNT(*) AS total FROM messages WHERE is_read = 0');
$unreadMessages = $unreadRow ? (int) $unreadRow['total'] : 0;

$adminNav = [
    'dashboard'    => ['href' => 'index.php', 'label' => 'Dashboard'],
    'reservations' => ['href' => 'reservations.php', 'label' => 'Reservations'],
    'messages'     => ['href' => 'messages.php', 'label' => 'Messages'],
    'menu'         => ['href' => 'menu.php', 'label' => 'Menu'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($adminTitle ? $adminTitle . ' | Staff' : 'Staff area') ?> — <?= e(SITE_NAME) ?></title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='22' fill='%2314100d'/%3E%3Ctext x='50' y='72' font-size='62' text-anchor='middle' fill='%23d69a2d'%3E%E2%9C%A6%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-topbar">
    <div class="wrap">
        <p>Staff area</p>
        <h1><?= e($adminTitle) ?></h1>
    </div>
</div>

<div class="admin-nav">
    <div class="admin-nav__links">
        <?php foreach ($adminNav as $key => $item): ?>
            <a href="<?= e($item['href']) ?>" class="<?= $adminActive === $key ? 'is-active' : '' ?>">
                <?= e($item['label']) ?><?php if ($key === 'messages' && $unreadMessages > 0): ?><span class="admin-nav__count"><?= $unreadMessages ?></span><?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="admin-nav__right">
        <a href="../index.php">View site &rarr;</a>
        <form method="post" action="logout.php" style="display:inline">
            <?= csrf_field() ?>
            <button type="submit">Log out</button>
        </form>
    </div>
</div>

<div class="admin-body">
    <div class="wrap">
        <?php $flash = flash_get(); ?>
        <?php if ($flash): ?>
            <div class="alert <?= $flash['type'] === 'error' ? 'alert--error' : 'alert--success' ?>" style="margin-bottom:24px">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

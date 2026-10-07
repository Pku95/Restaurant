<?php
/**
 * One-time installer.
 *
 * Open this file in your browser after filling in config.php:
 *     https://your-domain.com/install.php
 *
 * It creates the database tables and loads the demo menu and gallery.
 * DELETE THIS FILE when you are done.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$steps = [];
$fatal = null;

/* ---------------------------- requirements ---------------------------- */
$phpOk = version_compare(PHP_VERSION, '7.4.0', '>=');
$steps[] = ['PHP ' . PHP_VERSION . ' (7.4 or newer required)', $phpOk];
$steps[] = ['PDO MySQL driver available', extension_loaded('pdo_mysql')];
$steps[] = ['JSON support available', function_exists('json_decode')];
$steps[] = ['PHP sessions working', session_status() === PHP_SESSION_ACTIVE];

if (!$phpOk || !extension_loaded('pdo_mysql')) {
    $fatal = 'Your server does not meet the requirements above. Ask your host to enable the missing pieces.';
}

/* ------------------------------ tables -------------------------------- */
$tables = [
    'menu_categories' => "CREATE TABLE IF NOT EXISTS menu_categories (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        slug VARCHAR(60) NOT NULL,
        name VARCHAR(120) NOT NULL,
        tagline VARCHAR(200) NOT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_category_slug (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'menu_items' => "CREATE TABLE IF NOT EXISTS menu_items (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        category_id INT UNSIGNED NOT NULL,
        name VARCHAR(160) NOT NULL,
        description TEXT NOT NULL,
        price INT UNSIGNED NOT NULL,
        tags VARCHAR(200) NOT NULL DEFAULT '',
        image_url VARCHAR(200) DEFAULT NULL,
        is_vegetarian TINYINT(1) NOT NULL DEFAULT 0,
        is_featured TINYINT(1) NOT NULL DEFAULT 0,
        is_available TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT NOT NULL DEFAULT 0,
        PRIMARY KEY (id),
        KEY idx_item_category (category_id),
        CONSTRAINT fk_item_category FOREIGN KEY (category_id)
            REFERENCES menu_categories (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'reservations' => "CREATE TABLE IF NOT EXISTS reservations (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        reference VARCHAR(24) NOT NULL,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(180) NOT NULL,
        phone VARCHAR(40) NOT NULL,
        guests INT UNSIGNED NOT NULL,
        reservation_date DATE NOT NULL,
        reservation_time VARCHAR(5) NOT NULL,
        occasion VARCHAR(60) NOT NULL DEFAULT '',
        notes TEXT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_reservation_reference (reference),
        KEY idx_reservation_date (reservation_date),
        KEY idx_reservation_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'messages' => "CREATE TABLE IF NOT EXISTS messages (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(180) NOT NULL,
        phone VARCHAR(40) NOT NULL DEFAULT '',
        subject VARCHAR(160) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_message_read (is_read)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'gallery_images' => "CREATE TABLE IF NOT EXISTS gallery_images (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        image_url VARCHAR(200) NOT NULL,
        caption VARCHAR(200) NOT NULL,
        category VARCHAR(60) NOT NULL DEFAULT 'Food',
        sort_order INT NOT NULL DEFAULT 0,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
];

if ($fatal === null) {
    foreach ($tables as $name => $sql) {
        try {
            db()->exec($sql);
            $steps[] = ['Table <code>' . $name . '</code> ready', true];
        } catch (PDOException $exception) {
            $steps[] = ['Table <code>' . $name . '</code>: ' . $exception->getMessage(), false];
            $fatal = 'Could not create the tables. Make sure the database user has CREATE and ALTER rights.';
            break;
        }
    }
}

/* ------------------------------- seeding ------------------------------- */
$seeded = false;
$seedNote = 'Already contains data — nothing was overwritten.';

if ($fatal === null) {
    $existing = (int) db_one('SELECT COUNT(*) AS total FROM menu_categories')['total'];

    if ($existing === 0) {
        $path = __DIR__ . '/data/menu.json';
        if (!is_readable($path)) {
            $fatal = 'Could not read data/menu.json. Please upload the whole folder and try again.';
        } else {
            $content = json_decode(file_get_contents($path), true);
            if (!is_array($content) || empty($content['categories'])) {
                $fatal = 'data/menu.json is not valid JSON. Please re-upload it.';
            } else {
                db()->beginTransaction();
                try {
                    $categoryIds = [];
                    foreach ($content['categories'] as $category) {
                        db_run(
                            'INSERT INTO menu_categories (slug, name, tagline, sort_order) VALUES (?, ?, ?, ?)',
                            [$category['slug'], $category['name'], $category['tagline'], (int) $category['sortOrder']]
                        );
                        $categoryIds[$category['slug']] = (int) db()->lastInsertId();
                    }

                    $order = 0;
                    foreach ($content['items'] as $item) {
                        db_run(
                            'INSERT INTO menu_items
                                (category_id, name, description, price, tags, image_url,
                                 is_vegetarian, is_featured, is_available, sort_order)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)',
                            [
                                $categoryIds[$item['category']],
                                $item['name'],
                                $item['description'],
                                (int) $item['price'],
                                implode(',', (array) $item['tags']),
                                !empty($item['image']) ? $item['image'] : null,
                                !empty($item['vegetarian']) ? 1 : 0,
                                !empty($item['featured']) ? 1 : 0,
                                $order++,
                            ]
                        );
                    }

                    $order = 0;
                    foreach ($content['gallery'] as $image) {
                        db_run(
                            'INSERT INTO gallery_images (image_url, caption, category, sort_order) VALUES (?, ?, ?, ?)',
                            [$image['image'], $image['caption'], $image['category'], $order++]
                        );
                    }

                    db()->commit();
                    $seeded = true;
                    $seedNote = 'Loaded ' . count($content['items']) . ' dishes and '
                        . count($content['gallery']) . ' gallery images.';
                } catch (Exception $exception) {
                    db()->rollBack();
                    $fatal = 'Seeding failed: ' . $exception->getMessage();
                }
            }
        }
    }
}

$totalItems = 0;
if ($fatal === null) {
    $row = db_one('SELECT COUNT(*) AS total FROM menu_items');
    $totalItems = $row ? (int) $row['total'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installer | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="install">
    <p class="eyebrow eyebrow--saffron">Setup</p>
    <h1 class="display">Installer</h1>
    <p class="section-desc">This page creates your database tables and loads the starting content.</p>

    <div class="install__box">
        <?php if ($fatal !== null): ?>
            <div class="alert alert--error"><?= e($fatal) ?></div>
            <h2>What to do next</h2>
            <ol>
                <li>Open <code>config.php</code> and check the four <code>DB_</code> values.</li>
                <li>In cPanel, make sure the database user was added to the database with
                    <strong>ALL PRIVILEGES</strong>.</li>
                <li>Reload this page.</li>
            </ol>
        <?php else: ?>
            <div class="alert alert--success">Everything is set up and ready.</div>
            <h2>Checks</h2>
            <ul>
                <?php foreach ($steps as $step): ?>
                    <li>
                        <?= $step[1] ? '&#10003;' : '&#10007;' ?>
                        <?= $step[1] ? $step[0] : e(strip_tags($step[0])) /* keep the message readable */ ?>
                    </li>
                <?php endforeach; ?>
                <?php if (!$phpOk || !extension_loaded('pdo_mysql')): ?>
                    <li class="alert alert--error">One or more checks failed. Ask your host to enable them.</li>
                <?php endif; ?>
            </ul>

            <h2>Content</h2>
            <p><?= e($seedNote) ?> There are now <strong><?= (int) $totalItems ?></strong> dishes on the menu.</p>

            <h2>Almost done — two more things</h2>
            <ol>
                <li>Open <code>config.php</code> and change <code>ADMIN_PASSWORD</code> from
                    <code>ember123</code> to your own.</li>
                <li><strong>Delete this file (<code>install.php</code>)</strong> from the server.</li>
            </ol>

            <p style="margin-top:24px">
                <a class="btn btn--primary" href="index.php">View the website</a>
                <a class="btn btn--ghost" href="admin/login.php">Log in to the staff area</a>
            </p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

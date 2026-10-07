<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Menu';
$pageDescription = 'Charcoal kebabs, dum-cooked kacchi biryani, curries, tandoori breads and desserts at ' . SITE_NAME . ', Banani.';
$active = 'menu';

$categories = db_all('SELECT * FROM menu_categories ORDER BY sort_order');
$items = db_all('SELECT * FROM menu_items ORDER BY sort_order');

$itemsByCategory = [];
foreach ($items as $item) {
    $itemsByCategory[(int) $item['category_id']][] = $item;
}

require __DIR__ . '/includes/header.php';
?>

<section class="banner">
    <img class="banner__bg" src="assets/images/kebab.jpg" alt="" >
    <div class="banner__scrim"></div>
    <div class="wrap banner__inner">
        <p class="eyebrow eyebrow--saffron">The menu</p>
        <h1>Cooked over fire, finished by hand</h1>
        <p>Prices are in Bangladeshi Taka and exclude VAT and service. Ask our team about allergens or
            spice levels.</p>
        <div class="banner__actions">
            <?php foreach ($categories as $category): ?>
                <a class="chip" href="#<?= e($category['slug']) ?>"><?= e($category['name']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap wrap--narrow">
        <?php foreach ($categories as $category): ?>
            <?php $categoryItems = isset($itemsByCategory[(int) $category['id']]) ? $itemsByCategory[(int) $category['id']] : []; ?>
            <div class="menu-block" id="<?= e($category['slug']) ?>">
                <div class="menu-block__head">
                    <div>
                        <p class="menu-block__index"><?= e(str_pad((string) $category['sort_order'], 2, '0', STR_PAD_LEFT)) ?></p>
                        <h2 class="menu-block__title"><?= e($category['name']) ?></h2>
                        <p class="menu-block__tagline"><?= e($category['tagline']) ?></p>
                    </div>
                    <span class="menu-block__count"><?= count($categoryItems) ?></span>
                </div>

                <div class="menu-list">
                    <?php foreach ($categoryItems as $item): ?>
                        <?php $tags = split_tags($item['tags']); ?>
                        <div class="menu-row">
                            <div class="menu-row__line">
                                <h3 class="menu-row__name">
                                    <?= e($item['name']) ?><?php if ((int) $item['is_vegetarian'] === 1): ?><span class="veg-flag">veg</span><?php endif; ?>
                                </h3>
                                <span class="menu-row__leader"></span>
                                <span class="menu-row__price"><?= e(format_bdt($item['price'])) ?></span>
                            </div>
                            <div class="menu-row__meta">
                                <p class="menu-row__desc"><?= e($item['description']) ?></p>
                                <?php foreach ($tags as $tag): ?>
                                    <span class="tag tag--<?= e(preg_replace('/[^a-z]+/', '', strtolower($tag))) ?>"><?= e($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php if ((int) $item['is_available'] !== 1): ?>
                                <p class="menu-row__unavailable">Currently unavailable</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="panel" style="margin-top:56px;text-align:center">
            <h3 class="panel__title">Eating with us tonight?</h3>
            <p>Tables for eight or more are planned with the kitchen in advance. Call
                <a href="tel:<?= e(SITE_PHONE_HREF) ?>" style="color:var(--ember)"><?= e(SITE_PHONE) ?></a>
                and we'll build a set menu for your group.</p>
            <p><a class="btn btn--primary" href="reservations.php">Book a table</a></p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Gallery';
$pageDescription = 'Inside ' . SITE_NAME . ' — the dining room, the grill and the food.';
$active = 'gallery';

$all = db_all('SELECT * FROM gallery_images ORDER BY sort_order');

$categories = [];
foreach ($all as $image) {
    if (!in_array($image['category'], $categories, true)) {
        $categories[] = $image['category'];
    }
}

$activeCategory = isset($_GET['category']) && is_string($_GET['category']) ? $_GET['category'] : '';
if ($activeCategory !== '' && !in_array($activeCategory, $categories, true)) {
    $activeCategory = '';
}

$images = $activeCategory === ''
    ? $all
    : array_values(array_filter($all, function ($image) use ($activeCategory) {
        return $image['category'] === $activeCategory;
    }));

require __DIR__ . '/includes/header.php';
?>

<section class="banner">
    <img class="banner__bg" src="assets/images/curry.jpg" alt="">
    <div class="banner__scrim"></div>
    <div class="wrap banner__inner">
        <p class="eyebrow eyebrow--saffron">Gallery</p>
        <h1>The room, the fire, the food</h1>
        <p>A look inside service at <?= e(SITE_NAME) ?>.</p>
        <div class="banner__actions">
            <a class="chip <?= $activeCategory === '' ? 'chip--active' : '' ?>" href="gallery.php">All</a>
            <?php foreach ($categories as $category): ?>
                <a class="chip <?= $activeCategory === $category ? 'chip--active' : '' ?>"
                   href="gallery.php?category=<?= e(urlencode($category)) ?>"><?= e($category) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="gallery-grid">
            <?php foreach ($images as $index => $image): ?>
                <figure class="<?= $index % 5 === 0 ? 'figure--tall' : '' ?>">
                    <img src="<?= e(image_url($image['image_url'])) ?>" alt="<?= e($image['caption']) ?>" loading="lazy">
                    <figcaption>
                        <span><?= e($image['category']) ?></span>
                        <strong><?= e($image['caption']) ?></strong>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

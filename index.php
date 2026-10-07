<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = SITE_NAME . ' — ' . SITE_TAGLINE;
$active = 'home';

$featured = db_all(
    'SELECT * FROM menu_items WHERE is_featured = 1 AND is_available = 1 ORDER BY sort_order LIMIT 6'
);
$categories = db_all(
    'SELECT c.id, c.slug, c.name, c.tagline, COUNT(m.id) AS item_count
       FROM menu_categories c
       LEFT JOIN menu_items m ON m.category_id = c.id
      GROUP BY c.id
      ORDER BY c.sort_order'
);
$gallery = db_all('SELECT * FROM gallery_images ORDER BY sort_order LIMIT 7');

$content = json_decode(file_get_contents(__DIR__ . '/data/menu.json'), true);
$testimonials = isset($content['testimonials']) && is_array($content['testimonials']) ? $content['testimonials'] : [];
$dishCount = isset($content['items']) && is_array($content['items']) ? count($content['items']) : 0;

$yearsOpen = max(1, (int) date('Y') - (int) SITE_ESTABLISHED);

require __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="hero">
    <img class="hero__bg" src="assets/images/hero-grill.jpg" alt="Charcoal grilled skewers">
    <div class="hero__scrim"></div>
    <div class="wrap hero__inner">
        <p class="eyebrow eyebrow--saffron">Banani, Dhaka &middot; Est. <?= (int) SITE_ESTABLISHED ?></p>
        <h1>Fire, smoke and slow-cooked saffron.</h1>
        <p>A modern Mughlai grill room. Charcoal kebabs, dum-cooked kacchi biryani and desserts worth
            saving room for.</p>
        <div class="hero__actions">
            <a class="btn btn--primary" href="reservations.php">Book a table</a>
            <a class="btn btn--outline" href="menu.php">View the menu</a>
        </div>
        <p class="hero__note">
            Open today <?= e(site_hours()[0]['time']) ?> &middot;
            <a href="tel:<?= e(SITE_PHONE_HREF) ?>"><?= e(SITE_PHONE) ?></a>
        </p>
    </div>
</section>

<!-- Pillars -->
<div class="section--bordered">
    <div class="wrap section section--tight">
        <div class="pillars">
            <div class="pillar">
                <span class="pillar__icon">&#10022;</span>
                <div>
                    <h3>Live charcoal</h3>
                    <p>Kebabs cooked over the sigri, never a flat-top.</p>
                </div>
            </div>
            <div class="pillar">
                <span class="pillar__icon">&#10022;</span>
                <div>
                    <h3>Slow &amp; sealed</h3>
                    <p>Kacchi dum-cooked in sealed handis, rice aged a year.</p>
                </div>
            </div>
            <div class="pillar">
                <span class="pillar__icon">&#10022;</span>
                <div>
                    <h3>Private room</h3>
                    <p>24 seats for family lunches and business dinners.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured dishes -->
<section class="section">
    <div class="wrap">
        <div class="section-head-row">
            <div class="section-head">
                <p class="eyebrow">What we're known for</p>
                <h2 class="section-title">Dishes worth the drive</h2>
                <p class="section-desc">A short list of the plates our regulars order without looking
                    at the menu.</p>
            </div>
            <a class="btn btn--ghost" href="menu.php">Full menu &rarr;</a>
        </div>

        <div class="grid grid--3">
            <?php foreach ($featured as $item): ?>
                <article class="card card--hover dish">
                    <div class="dish__media">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?= e(image_url($item['image_url'])) ?>" alt="<?= e($item['name']) ?>" loading="lazy">
                        <?php endif; ?>
                        <?php if ((int) $item['is_vegetarian'] === 1): ?>
                            <span class="dish__veg">Veg</span>
                        <?php endif; ?>
                    </div>
                    <div class="card__body">
                        <div class="dish__head">
                            <h3 class="card__title"><?= e($item['name']) ?></h3>
                            <span class="dish__price"><?= e(format_bdt($item['price'])) ?></span>
                        </div>
                        <p class="dish__desc"><?= e($item['description']) ?></p>
                        <?php $tags = split_tags($item['tags']); ?>
                        <?php if ($tags): ?>
                            <p style="margin-top:14px;display:flex;flex-wrap:wrap;gap:6px">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="tag tag--<?= e(preg_replace('/[^a-z]+/', '', strtolower($tag))) ?>"><?= e($tag) ?></span>
                                <?php endforeach; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Story -->
<section class="section section--sand">
    <div class="wrap split">
        <div class="media-frame">
            <img src="assets/images/chef.jpg" alt="Chef working the live flame" loading="lazy">
        </div>
        <div>
            <div class="section-head">
                <p class="eyebrow">The kitchen</p>
                <h2 class="section-title">One fire, three generations of technique</h2>
                <p class="section-desc">Our grill team learned over coals in Old Dhaka and Lucknow.
                    The kacchi is sealed and left alone for hours; the kebabs are turned by hand until
                    the fat renders. Nothing here is rushed.</p>
            </div>
            <dl class="stat-grid" style="margin-top:40px">
                <div>
                    <dd class="stat-grid__value"><?= $yearsOpen ?>+</dd>
                    <dt class="stat-grid__label">Years in Banani</dt>
                </div>
                <div>
                    <dd class="stat-grid__value"><?= (int) $dishCount ?></dd>
                    <dt class="stat-grid__label">Dishes on the menu</dt>
                </div>
                <div>
                    <dd class="stat-grid__value">86</dd>
                    <dt class="stat-grid__label">Seats</dt>
                </div>
                <div>
                    <dd class="stat-grid__value">24</dd>
                    <dt class="stat-grid__label">Private room</dt>
                </div>
            </dl>
        </div>
    </div>
</section>

<!-- Menu preview -->
<section class="section">
    <div class="wrap">
        <div class="section-head section-head--center">
            <p class="eyebrow">The menu</p>
            <h2 class="section-title">Six sections, no filler</h2>
            <p class="section-desc">Everything is cooked to order. Ask the team about allergens or
                spice levels — nothing is off-limits.</p>
        </div>

        <div class="grid grid--3" style="margin-top:44px">
            <?php foreach ($categories as $category): ?>
                <a class="card card--hover" href="menu.php#<?= e($category['slug']) ?>" style="padding:24px;display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <div>
                        <h3 class="card__title"><?= e($category['name']) ?></h3>
                        <p class="dish__desc"><?= e($category['tagline']) ?></p>
                    </div>
                    <span style="font-family:var(--display);font-size:28px;font-weight:600;color:var(--saffron)"><?= (int) $category['item_count'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Gallery strip -->
<section class="section section--ink">
    <div class="wrap section-head-row" style="margin-bottom:0">
        <div class="section-head">
            <p class="eyebrow eyebrow--saffron">Inside</p>
            <h2 class="section-title" style="color:var(--cream)">The room and the food</h2>
        </div>
        <a class="btn btn--outline" href="gallery.php">See the gallery</a>
    </div>
    <div class="gallery-strip">
        <?php foreach ($gallery as $image): ?>
            <figure>
                <img src="<?= e(image_url($image['image_url'])) ?>" alt="<?= e($image['caption']) ?>" loading="lazy">
                <figcaption><?= e($image['caption']) ?></figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
</section>

<!-- Testimonials -->
<section class="section">
    <div class="wrap">
        <div class="section-head section-head--center">
            <p class="eyebrow">Word of mouth</p>
            <h2 class="section-title">What guests say</h2>
        </div>
        <div class="grid grid--3" style="margin-top:44px">
            <?php foreach ($testimonials as $testimonial): ?>
                <figure class="quote">
                    <div class="quote__stars">
                        <?= e(str_repeat('★', max(0, min(5, (int) $testimonial['rating'])))) ?>
                    </div>
                    <blockquote>&ldquo;<?= e($testimonial['quote']) ?>&rdquo;</blockquote>
                    <figcaption>
                        <strong><?= e($testimonial['name']) ?></strong>
                        <span><?= e($testimonial['role']) ?></span>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Hours and map -->
<div class="section--bordered">
    <section class="section">
        <div class="wrap split">
            <div>
                <div class="section-head">
                    <p class="eyebrow">Plan a visit</p>
                    <h2 class="section-title">Hours &amp; location</h2>
                </div>
                <ul class="hours-list" style="margin-top:28px">
                    <?php foreach (site_hours() as $entry): ?>
                        <li>
                            <span><?= e($entry['days']) ?></span>
                            <span><?= e($entry['time']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="section-desc"><?= e(SITE_PRIVATE_DINING) ?></p>
            </div>
            <div class="map-frame">
                <iframe title="Map"
                        src="https://www.google.com/maps?q=<?= e(urlencode(SITE_MAP_QUERY)) ?>&amp;output=embed"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
</div>

<!-- CTA -->
<section class="cta-band">
    <div class="wrap">
        <h2>Table for two, or twenty-four.</h2>
        <p>Bookings are confirmed the same day. For groups over eight, call us and we'll plan the menu
            with you.</p>
        <div class="cta-band__actions">
            <a class="btn btn--primary" href="reservations.php">Book a table</a>
            <a class="btn btn--outline" href="menu.php">Browse the menu</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'About';
$pageDescription = 'The story behind ' . SITE_NAME . ', a modern Mughlai grill room in Banani, Dhaka.';
$active = 'about';

$content = json_decode(file_get_contents(__DIR__ . '/data/menu.json'), true);
$dishCount = isset($content['items']) && is_array($content['items']) ? count($content['items']) : 0;
$yearsOpen = max(1, (int) date('Y') - (int) SITE_ESTABLISHED);

$method = [
    ['01', 'The charcoal', 'The sigri is lit at 10am every day and left until the coals turn white-hot. Nothing touches a gas flame.'],
    ['02', 'The marinade', 'Meat rests overnight in hung yoghurt, green papaya and whole spice. It is the slowest part of the process and the reason the kebabs are soft.'],
    ['03', 'The seal', 'Kacchi handis are sealed with dough and left on low heat for hours. We do not open them to check — we listen.'],
    ['04', 'The finish', 'Saffron, kewra and burnt butter go on at the very end, at the pass, not in the kitchen.'],
];

require __DIR__ . '/includes/header.php';
?>

<section class="banner">
    <img class="banner__bg" src="assets/images/chef.jpg" alt="">
    <div class="banner__scrim"></div>
    <div class="wrap banner__inner">
        <p class="eyebrow eyebrow--saffron">Our story</p>
        <h1>A grill room built around one fire</h1>
        <p>We opened in <?= (int) SITE_ESTABLISHED ?> with a small menu, a charcoal sigri and a dining
            room we wanted to sit in ourselves. Not much has changed.</p>
    </div>
</section>

<section class="section">
    <div class="wrap split">
        <div class="media-frame media-frame--tall">
            <img src="assets/images/bread.jpg" alt="Breads baking in the tandoor" loading="lazy">
        </div>
        <div>
            <div class="section-head">
                <p class="eyebrow">How we cook</p>
                <h2 class="section-title">Four steps, none of them fast</h2>
                <p class="section-desc">Mughlai cooking rewards patience. These are the four things our
                    kitchen will not compromise on.</p>
            </div>
            <ol style="list-style:none;margin:40px 0 0;padding:0;display:grid;gap:30px">
                <?php foreach ($method as $step): ?>
                    <li style="display:flex;gap:20px">
                        <span style="font-family:var(--display);font-size:28px;font-weight:600;color:var(--saffron)"><?= e($step[0]) ?></span>
                        <div>
                            <h3 style="font-family:var(--display);font-size:24px;font-weight:600;margin:0"><?= e($step[1]) ?></h3>
                            <p style="margin:6px 0 0;font-size:14px;color:var(--mute)"><?= e($step[2]) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</section>

<section class="section section--sand">
    <div class="wrap">
        <div class="section-head section-head--center">
            <p class="eyebrow">By the numbers</p>
            <h2 class="section-title">A few facts about the room</h2>
        </div>
        <dl class="stat-grid" style="margin-top:44px">
            <div>
                <dd class="stat-grid__value"><?= (int) $dishCount ?></dd>
                <dt class="stat-grid__label">Dishes on the menu</dt>
            </div>
            <div>
                <dd class="stat-grid__value">86</dd>
                <dt class="stat-grid__label">Seats in the dining room</dt>
            </div>
            <div>
                <dd class="stat-grid__value">24</dd>
                <dt class="stat-grid__label">Private dining seats</dt>
            </div>
            <div>
                <dd class="stat-grid__value"><?= $yearsOpen ?></dd>
                <dt class="stat-grid__label">Years in Banani</dt>
            </div>
        </dl>
    </div>
</section>

<section class="section">
    <div class="wrap grid grid--2">
        <div class="card" style="padding:40px">
            <p class="panel__label">Private dining</p>
            <h3 class="panel__title">A room of your own</h3>
            <p class="section-desc"><?= e(SITE_PRIVATE_DINING) ?> We'll write a set menu around your
                group, handle dietary needs in advance and keep the room to yourselves for the evening.</p>
            <p><a class="btn btn--ghost" href="contact.php">Enquire about the room</a></p>
        </div>
        <div class="panel panel--ink">
            <p class="panel__label">Large groups</p>
            <h3 class="panel__title">Eight guests or more</h3>
            <p>For parties of eight and above we plan the food with you in advance so everything lands
                at the table together. Call us and we'll put a menu together.</p>
            <p><a class="btn btn--primary" href="tel:<?= e(SITE_PHONE_HREF) ?>">Call <?= e(SITE_PHONE) ?></a></p>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="wrap">
        <h2>Come and eat with us.</h2>
        <p>The kacchi is sealed by four o'clock. Book a table and we'll keep a seat warm.</p>
        <div class="cta-band__actions">
            <a class="btn btn--primary" href="reservations.php">Book a table</a>
            <a class="btn btn--outline" href="menu.php">See the menu</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

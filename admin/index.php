<?php
require_once __DIR__ . '/includes/auth.php';
admin_require_login();

$adminTitle = 'Good service starts here';
$adminActive = 'dashboard';

$today = date('Y-m-d');

$coversToday = (int) db_one(
    'SELECT COALESCE(SUM(guests), 0) AS total FROM reservations WHERE reservation_date = ? AND status <> ?',
    [$today, 'cancelled']
)['total'];

$pendingCount = (int) db_one('SELECT COUNT(*) AS total FROM reservations WHERE status = ?', ['pending'])['total'];
$upcomingCount = (int) db_one(
    'SELECT COUNT(*) AS total FROM reservations WHERE reservation_date >= ? AND status <> ?',
    [$today, 'cancelled']
)['total'];
$totalReservations = (int) db_one('SELECT COUNT(*) AS total FROM reservations')['total'];
$unreadMessages = (int) db_one('SELECT COUNT(*) AS total FROM messages WHERE is_read = 0')['total'];
$totalItems = (int) db_one('SELECT COUNT(*) AS total FROM menu_items')['total'];
$unavailableItems = (int) db_one('SELECT COUNT(*) AS total FROM menu_items WHERE is_available = 0')['total'];

$upcoming = db_all(
    'SELECT * FROM reservations WHERE reservation_date >= ? AND status <> ? ORDER BY reservation_date, reservation_time LIMIT 6',
    [$today, 'cancelled']
);

$cards = [
    ['Covers today', $coversToday, 'reservations.php'],
    ['Pending requests', $pendingCount, 'reservations.php?status=pending'],
    ['Upcoming bookings', $upcomingCount, 'reservations.php'],
    ['Unread messages', $unreadMessages, 'messages.php'],
    ['Menu items', $totalItems, 'menu.php'],
    ['Unavailable items', $unavailableItems, 'menu.php'],
];

require __DIR__ . '/includes/header.php';
?>

<div class="admin-intro">
    <p>A quick view of today's covers, pending requests and anything that needs your attention.</p>
</div>

<div class="stat-cards">
    <?php foreach ($cards as $card): ?>
        <a class="stat-card" href="<?= e($card[2]) ?>">
            <p class="stat-card__label"><?= e($card[0]) ?></p>
            <p class="stat-card__value"><?= (int) $card[1] ?></p>
        </a>
    <?php endforeach; ?>
</div>

<section style="margin-top:48px">
    <div class="admin-intro">
        <h2 class="section-title" style="font-size:32px">Next seatings</h2>
        <a class="link-arrow" href="reservations.php">All reservations &rarr;</a>
    </div>

    <?php if (!$upcoming): ?>
        <div class="empty-state" style="margin-top:20px">
            No upcoming reservations yet. New bookings will appear here as guests request them.
        </div>
    <?php else: ?>
        <div class="stack">
            <?php foreach ($upcoming as $reservation): ?>
                <article class="record">
                    <div class="record__head">
                        <div>
                            <div class="record__meta">
                                <span class="badge badge--<?= e($reservation['status']) ?>"><?= e($reservation['status']) ?></span>
                                <span class="reference"><?= e($reservation['reference']) ?></span>
                            </div>
                            <h3 class="record__title"><?= e($reservation['name']) ?></h3>
                            <p class="record__meta" style="margin:4px 0 0">
                                <?= e(format_date($reservation['reservation_date'])) ?> at
                                <?= e(format_time($reservation['reservation_time'])) ?> &middot;
                                <?= (int) $reservation['guests'] ?> guests
                                <?php if ($reservation['occasion'] !== ''): ?> &middot; <?= e($reservation['occasion']) ?><?php endif; ?>
                            </p>
                        </div>
                        <div style="text-align:right;font-size:14px">
                            <a href="tel:<?= e($reservation['phone']) ?>" style="color:var(--ember)"><?= e($reservation['phone']) ?></a><br>
                            <a href="mailto:<?= e($reservation['email']) ?>" style="color:var(--mute)"><?= e($reservation['email']) ?></a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

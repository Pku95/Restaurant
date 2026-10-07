<?php
require_once __DIR__ . '/includes/auth.php';
admin_require_login();

$adminTitle = 'Reservations';
$adminActive = 'reservations';

$statuses = ['pending', 'confirmed', 'cancelled'];

/* -------------------------------- actions ------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        flash_set('error', 'Your session expired. Please try that again.');
        redirect('reservations.php');
    }

    $action = (string) ($_POST['action'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0 && $action === 'status') {
        $status = (string) ($_POST['status'] ?? '');
        if (in_array($status, $statuses, true)) {
            db_run('UPDATE reservations SET status = ? WHERE id = ?', [$status, $id]);
            flash_set('success', 'Reservation marked as ' . $status . '.');
        }
    } elseif ($id > 0 && $action === 'delete') {
        db_run('DELETE FROM reservations WHERE id = ?', [$id]);
        flash_set('success', 'Reservation deleted.');
    }

    redirect('reservations.php');
}

/* -------------------------------- filtering ----------------------------- */
$status = isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : '';
if (!in_array($status, $statuses, true)) {
    $status = '';
}

if ($status === '') {
    $reservations = db_all('SELECT * FROM reservations ORDER BY reservation_date DESC, reservation_time');
    $count = (int) db_one('SELECT COUNT(*) AS total FROM reservations')['total'];
} else {
    $reservations = db_all(
        'SELECT * FROM reservations WHERE status = ? ORDER BY reservation_date DESC, reservation_time',
        [$status]
    );
    $count = count($reservations);
}

require __DIR__ . '/includes/header.php';
?>

<div class="admin-intro">
    <p>Confirm, cancel or remove table requests. Guests are told their booking is pending until you
        confirm it.</p>
</div>

<div class="filter-row">
    <a class="<?= $status === '' ? 'is-active' : '' ?>" href="reservations.php">All</a>
    <?php foreach ($statuses as $value): ?>
        <a class="<?= $status === $value ? 'is-active' : '' ?>"
           href="reservations.php?status=<?= e($value) ?>"><?= e(ucfirst($value)) ?></a>
    <?php endforeach; ?>
</div>

<p style="margin-top:20px;font-size:14px;color:var(--mute)">
    <?= (int) $count ?> reservation<?= $count === 1 ? '' : 's' ?>
</p>

<?php if (!$reservations): ?>
    <div class="empty-state" style="margin-top:20px">Nothing here with that filter.</div>
<?php else: ?>
    <div class="stack">
        <?php foreach ($reservations as $reservation): ?>
            <article class="record">
                <div class="record__head">
                    <div>
                        <div class="record__meta">
                            <span class="badge badge--<?= e($reservation['status']) ?>"><?= e($reservation['status']) ?></span>
                            <span class="reference"><?= e($reservation['reference']) ?></span>
                            <span>requested <?= e(format_datetime($reservation['created_at'])) ?></span>
                        </div>
                        <h3 class="record__title"><?= e($reservation['name']) ?></h3>
                    </div>
                    <p class="record__time">
                        <?= e(format_time($reservation['reservation_time'])) ?>
                        <span><?= e(format_date($reservation['reservation_date'])) ?></span>
                    </p>
                </div>

                <dl class="record__grid">
                    <div>
                        <dt>Guests</dt>
                        <dd><?= (int) $reservation['guests'] ?></dd>
                    </div>
                    <div>
                        <dt>Occasion</dt>
                        <dd><?= $reservation['occasion'] !== '' ? e($reservation['occasion']) : '—' ?></dd>
                    </div>
                    <div>
                        <dt>Phone</dt>
                        <dd><a href="tel:<?= e($reservation['phone']) ?>"><?= e($reservation['phone']) ?></a></dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd><a href="mailto:<?= e($reservation['email']) ?>"><?= e($reservation['email']) ?></a></dd>
                    </div>
                </dl>

                <?php if ((string) $reservation['notes'] !== ''): ?>
                    <p class="record__notes"><strong>Notes:</strong> <?= e($reservation['notes']) ?></p>
                <?php endif; ?>

                <div class="record__actions">
                    <form method="post" action="reservations.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="status">
                        <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                        <input type="hidden" name="status" value="confirmed">
                        <button class="btn btn--sm btn--confirm" type="submit"
                            <?= $reservation['status'] === 'confirmed' ? 'disabled' : '' ?>>Confirm</button>
                    </form>
                    <form method="post" action="reservations.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="status">
                        <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                        <input type="hidden" name="status" value="cancelled">
                        <button class="btn btn--sm btn--cancel" type="submit"
                            <?= $reservation['status'] === 'cancelled' ? 'disabled' : '' ?>>Cancel</button>
                    </form>
                    <form method="post" action="reservations.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                        <button class="btn btn--sm btn--quiet btn--danger" type="submit">Delete</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>

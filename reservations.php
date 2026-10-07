<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Book a Table';
$pageDescription = 'Reserve a table at ' . SITE_NAME . ', Banani. Bookings are confirmed the same day by phone or email.';
$active = 'reservations';

$occasions = ['Dinner', 'Lunch', 'Birthday', 'Anniversary', 'Business meal', 'Family gathering', 'Other'];

/* ------------------------------ handle submit ----------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $date = trim((string) ($_POST['reservation_date'] ?? ''));
    $time = trim((string) ($_POST['reservation_time'] ?? ''));
    $occasion = trim((string) ($_POST['occasion'] ?? ''));
    $notes = trim((string) ($_POST['notes'] ?? ''));
    $guests = (int) ($_POST['guests'] ?? 0);

    $validTimes = array_map(function ($slot) {
        return $slot['value'];
    }, time_slots());

    $error = null;
    if (!csrf_check()) {
        $error = 'Your session expired. Please submit the form again.';
    } elseif (mb_strlen($name) < 2) {
        $error = 'Please tell us the name for the booking.';
    } elseif (!is_email($email)) {
        $error = 'Please enter a valid email address.';
    } elseif (digit_count($phone) < 7) {
        $error = 'Please enter a phone number we can reach you on.';
    } elseif ($guests < 1 || $guests > (int) SITE_MAX_GUESTS) {
        $error = 'Party size must be between 1 and ' . (int) SITE_MAX_GUESTS . '.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !strtotime($date)) {
        $error = 'Please choose a valid date.';
    } elseif ($date < today_iso()) {
        $error = 'Please choose today or a later date.';
    } elseif (!in_array($time, $validTimes, true)) {
        $error = 'Please choose a valid seating time.';
    } elseif ($occasion !== '' && !in_array($occasion, $occasions, true)) {
        $error = 'Please choose a valid occasion.';
    }

    if ($error !== null) {
        $_SESSION['old'] = [
            'name' => $name, 'email' => $email, 'phone' => $phone, 'guests' => $guests,
            'reservation_date' => $date, 'reservation_time' => $time,
            'occasion' => $occasion, 'notes' => $notes,
        ];
        flash_set('error', $error);
        redirect('reservations.php');
    }

    $reference = make_reference();
    db_insert(
        'INSERT INTO reservations
            (reference, name, email, phone, guests, reservation_date, reservation_time, occasion, notes, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [$reference, $name, $email, $phone, $guests, $date, $time, $occasion, $notes, 'pending']
    );

    $_SESSION['confirmation'] = [
        'reference' => $reference,
        'date' => $date,
        'time' => $time,
        'guests' => $guests,
    ];
    redirect('reservations.php');
}

$confirmation = isset($_SESSION['confirmation']) && is_array($_SESSION['confirmation']) ? $_SESSION['confirmation'] : null;
unset($_SESSION['confirmation']);

$old = isset($_SESSION['old']) && is_array($_SESSION['old']) ? $_SESSION['old'] : [];
unset($_SESSION['old']);

require __DIR__ . '/includes/header.php';
?>

<section class="banner">
    <img class="banner__bg" src="assets/images/interior.jpg" alt="">
    <div class="banner__scrim"></div>
    <div class="wrap banner__inner">
        <p class="eyebrow eyebrow--saffron">Reservations</p>
        <h1>Book a table</h1>
        <p>Choose your date, time and party size. We confirm every booking personally.</p>
    </div>
</section>

<section class="section">
    <div class="wrap split" style="align-items:start">
        <div>
            <?php if ($confirmation): ?>
                <div class="confirm">
                    <p class="confirm__label">Request received</p>
                    <h3>Thank you — we'll confirm shortly.</h3>
                    <p>Our team confirms every booking by phone or email, usually within a couple of
                        hours during opening times.</p>
                    <dl class="confirm__grid">
                        <div>
                            <dt>Reference</dt>
                            <dd class="dd--ref"><?= e($confirmation['reference']) ?></dd>
                        </div>
                        <div>
                            <dt>Date</dt>
                            <dd><?= e(format_date($confirmation['date'])) ?></dd>
                        </div>
                        <div>
                            <dt>Seating</dt>
                            <dd><?= e(format_time($confirmation['time'])) ?></dd>
                        </div>
                        <div>
                            <dt>Guests</dt>
                            <dd><?= (int) $confirmation['guests'] ?></dd>
                        </div>
                    </dl>
                    <a class="btn btn--outline" href="reservations.php">Make another booking</a>
                </div>
            <?php else: ?>
                <form method="post" action="reservations.php" class="form-grid" novalidate>
                    <?= csrf_field() ?>

                    <label class="field">
                        <span>Name for the booking</span>
                        <input type="text" name="name" required maxlength="120"
                               value="<?= e(isset($old['name']) ? $old['name'] : '') ?>">
                    </label>
                    <label class="field">
                        <span>Phone</span>
                        <input type="tel" name="phone" required maxlength="40" placeholder="+880 1XXX-XXXXXX"
                               value="<?= e(isset($old['phone']) ? $old['phone'] : '') ?>">
                    </label>
                    <label class="field field--full">
                        <span>Email</span>
                        <input type="email" name="email" required maxlength="180"
                               value="<?= e(isset($old['email']) ? $old['email'] : '') ?>">
                    </label>
                    <label class="field">
                        <span>Date</span>
                        <input type="date" name="reservation_date" required min="<?= e(today_iso()) ?>"
                               value="<?= e(isset($old['reservation_date']) ? $old['reservation_date'] : '') ?>">
                    </label>
                    <label class="field">
                        <span>Seating time</span>
                        <select name="reservation_time" required>
                            <?php foreach (time_slots() as $slot): ?>
                                <option value="<?= e($slot['value']) ?>"
                                    <?= (isset($old['reservation_time']) ? $old['reservation_time'] : '19:30') === $slot['value'] ? 'selected' : '' ?>>
                                    <?= e($slot['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="field">
                        <span>Guests</span>
                        <input type="number" name="guests" required min="1" max="<?= (int) SITE_MAX_GUESTS ?>"
                               value="<?= e(isset($old['guests']) ? $old['guests'] : 2) ?>">
                    </label>
                    <label class="field">
                        <span>Occasion (optional)</span>
                        <select name="occasion">
                            <option value="">None</option>
                            <?php foreach ($occasions as $occasion): ?>
                                <option value="<?= e($occasion) ?>"
                                    <?= (isset($old['occasion']) ? $old['occasion'] : '') === $occasion ? 'selected' : '' ?>>
                                    <?= e($occasion) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="field field--full">
                        <span>Anything we should know? (optional)</span>
                        <textarea name="notes" rows="4" maxlength="1000"
                                  placeholder="Allergies, high chairs, a quiet corner, wheelchair access…"><?= e(isset($old['notes']) ? $old['notes'] : '') ?></textarea>
                    </label>

                    <div class="field--full">
                        <button type="submit" class="btn btn--primary btn--block">Request this table</button>
                        <p class="form-note">For parties over <?= (int) SITE_MAX_GUESTS ?> guests, please call us so
                            we can plan the menu with you.</p>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <aside class="stack" style="gap:22px">
            <div class="media-frame">
                <img src="assets/images/interior.jpg" alt="The dining room at <?= e(SITE_NAME) ?>" loading="lazy">
            </div>

            <div class="panel">
                <h3 class="panel__title" style="font-size:24px">Good to know</h3>
                <ul class="check-list" style="margin-top:16px">
                    <li>Tables are held for 15 minutes past the booking time.</li>
                    <li>Peak seatings on Friday and Saturday fill up 3–4 days ahead.</li>
                    <li>We keep a few tables for walk-ins at the counter.</li>
                    <li><?= e(SITE_PRIVATE_DINING) ?></li>
                </ul>
            </div>

            <div class="panel panel--ink">
                <h3 class="panel__title" style="font-size:24px">Prefer to call?</h3>
                <p style="font-family:var(--display);font-size:30px;font-weight:600;color:var(--saffron);margin:10px 0 0">
                    <a href="tel:<?= e(SITE_PHONE_HREF) ?>"><?= e(SITE_PHONE) ?></a>
                </p>
                <p>Or email <a href="mailto:<?= e(SITE_EMAIL) ?>" style="text-decoration:underline"><?= e(SITE_EMAIL) ?></a>.
                    We answer during opening hours.</p>
            </div>
        </aside>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

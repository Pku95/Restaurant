<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact';
$pageDescription = 'Get in touch with ' . SITE_NAME . ' — private dining, group bookings, feedback and press.';
$active = 'contact';

$subjects = ['General enquiry', 'Private dining', 'Large group booking', 'Feedback', 'Careers', 'Press'];

/* ------------------------------ handle submit ----------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));

    $error = null;
    if (!csrf_check()) {
        $error = 'Your session expired. Please submit the form again.';
    } elseif (mb_strlen($name) < 2) {
        $error = 'Please tell us your name.';
    } elseif (!is_email($email)) {
        $error = 'Please enter a valid email address.';
    } elseif ($phone !== '' && digit_count($phone) < 7) {
        $error = 'Please enter a valid phone number, or leave it blank.';
    } elseif (!in_array($subject, $subjects, true)) {
        $error = 'Please choose a subject.';
    } elseif (mb_strlen($message) < 10) {
        $error = 'Please write a little more detail (10 characters or more).';
    }

    if ($error !== null) {
        $_SESSION['old'] = [
            'name' => $name, 'email' => $email, 'phone' => $phone,
            'subject' => $subject, 'message' => $message,
        ];
        flash_set('error', $error);
        redirect('contact.php');
    }

    db_insert(
        'INSERT INTO messages (name, email, phone, subject, message, is_read)
         VALUES (?, ?, ?, ?, ?, 0)',
        [$name, $email, $phone, $subject, $message]
    );

    flash_set('success', 'Thank you — your message has been sent. We reply within one working day.');
    redirect('contact.php');
}

$old = isset($_SESSION['old']) && is_array($_SESSION['old']) ? $_SESSION['old'] : [];
unset($_SESSION['old']);

require __DIR__ . '/includes/header.php';
?>

<section class="banner">
    <img class="banner__bg" src="assets/images/plated.jpg" alt="">
    <div class="banner__scrim"></div>
    <div class="wrap banner__inner">
        <p class="eyebrow eyebrow--saffron">Contact</p>
        <h1>Talk to us</h1>
        <p>Private dining, group bookings, feedback or press — send a note and we'll reply within a
            working day.</p>
    </div>
</section>

<section class="section">
    <div class="wrap split" style="align-items:start">
        <div>
            <form method="post" action="contact.php" class="form-grid" novalidate>
                <?= csrf_field() ?>

                <label class="field">
                    <span>Name</span>
                    <input type="text" name="name" required maxlength="120"
                           value="<?= e(isset($old['name']) ? $old['name'] : '') ?>">
                </label>
                <label class="field">
                    <span>Phone (optional)</span>
                    <input type="tel" name="phone" maxlength="40"
                           value="<?= e(isset($old['phone']) ? $old['phone'] : '') ?>">
                </label>
                <label class="field">
                    <span>Email</span>
                    <input type="email" name="email" required maxlength="180"
                           value="<?= e(isset($old['email']) ? $old['email'] : '') ?>">
                </label>
                <label class="field">
                    <span>Subject</span>
                    <select name="subject" required>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= e($subject) ?>"
                                <?= (isset($old['subject']) ? $old['subject'] : 'General enquiry') === $subject ? 'selected' : '' ?>>
                                <?= e($subject) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field field--full">
                    <span>Message</span>
                    <textarea name="message" rows="5" required minlength="10" maxlength="4000"
                              placeholder="Tell us what you need — dates, guest numbers, dietary requirements…"><?= e(isset($old['message']) ? $old['message'] : '') ?></textarea>
                </label>

                <div class="field--full">
                    <button type="submit" class="btn btn--primary btn--block">Send message</button>
                </div>
            </form>
        </div>

        <aside class="stack" style="gap:22px">
            <div class="panel">
                <p class="panel__label">Visit</p>
                <p style="margin-top:14px"><?= e(SITE_ADDRESS) ?></p>
                <p style="margin-top:12px">
                    <a class="link-arrow" href="https://maps.google.com/?q=<?= e(urlencode(SITE_MAP_QUERY)) ?>"
                       target="_blank" rel="noopener">Open in Google Maps &rarr;</a>
                </p>
            </div>

            <div class="panel">
                <p class="panel__label">Call or write</p>
                <p style="margin-top:14px;font-family:var(--display);font-size:24px;font-weight:600">
                    <a href="tel:<?= e(SITE_PHONE_HREF) ?>"><?= e(SITE_PHONE) ?></a>
                </p>
                <p style="margin:4px 0 0">
                    <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a>
                </p>
                <p style="margin-top:14px">
                    <a class="link-arrow" href="https://wa.me/<?= e(preg_replace('/\D/', '', SITE_WHATSAPP)) ?>"
                       target="_blank" rel="noopener">WhatsApp us &rarr;</a>
                </p>
            </div>

            <div class="panel panel--ink">
                <p class="panel__label">Opening hours</p>
                <ul class="hours-list" style="margin-top:14px;border-color:rgba(250,246,239,0.15)">
                    <?php foreach (site_hours() as $entry): ?>
                        <li style="border-color:rgba(250,246,239,0.15)">
                            <span><?= e($entry['days']) ?></span>
                            <span style="color:rgba(250,246,239,0.6)"><?= e($entry['time']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

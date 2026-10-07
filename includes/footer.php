<?php if (!defined('SITE_NAME')) {
    http_response_code(403);
    exit('This file cannot be loaded directly.');
} ?>
</main>

<footer class="site-footer">
    <div class="wrap">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <span class="brand__mark">&#10022;</span>
                    <span class="footer-brand__name"><?= e(SITE_NAME) ?></span>
                </div>
                <p>A modern Mughlai grill room in Banani. Charcoal kebabs, slow-cooked biryani and a
                    serious list of lassis — since <?= (int) SITE_ESTABLISHED ?>.</p>
                <p class="footer-est">Est. <?= (int) SITE_ESTABLISHED ?> &middot; Dhaka</p>
            </div>

            <div>
                <h4>Explore</h4>
                <ul>
                    <li><a href="reservations.php">Book a table</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4>Hours</h4>
                <ul>
                    <?php foreach (site_hours() as $entry): ?>
                        <li><?= e($entry['days']) ?><br><?= e($entry['time']) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><?= e(SITE_PRIVATE_DINING) ?></p>
            </div>

            <div>
                <h4>Find us</h4>
                <ul>
                    <li><?= e(SITE_ADDRESS) ?></li>
                    <li><a href="tel:<?= e(SITE_PHONE_HREF) ?>"><?= e(SITE_PHONE) ?></a></li>
                    <li><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
                </ul>
                <p><a class="btn btn--outline btn--sm" href="https://wa.me/<?= e(preg_replace('/\D/', '', SITE_WHATSAPP)) ?>">Message us on WhatsApp</a></p>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="wrap">
            <span>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.</span>
            <span><a href="admin/login.php">Staff login</a></span>
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>

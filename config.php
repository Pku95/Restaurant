<?php
/**
 * Ember & Saffron — site configuration
 * ------------------------------------------------------------------
 * This is the ONLY file you need to edit after uploading.
 *
 * 1. Create a MySQL database and user in cPanel (see README.txt).
 * 2. Fill in the four DB_ values below.
 * 3. Visit /install.php once to create the tables and demo content.
 * 4. Change ADMIN_PASSWORD below.
 * 5. Delete install.php.
 */

// ---- Database ---------------------------------------------------------
// cPanel usually uses 'localhost' for the host.
// The database name and username normally share your cPanel prefix,
// for example cpuser_embersaffron / cpuser_embers.
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// ---- Staff area (/admin) ---------------------------------------------
// Default password is "ember123" — CHANGE THIS BEFORE GOING LIVE.
//
// For stronger security, replace ADMIN_PASSWORD_HASH with a bcrypt string
// instead of keeping a plain password. Generate one with:
//     php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"
// then paste it between the quotes and leave ADMIN_PASSWORD empty.
define('ADMIN_PASSWORD', 'ember123');
define('ADMIN_PASSWORD_HASH', '');

// ---- Restaurant details ----------------------------------------------
define('SITE_NAME', 'Ember & Saffron');
define('SITE_TAGLINE', 'Modern Mughlai grill & kitchen');
define('SITE_PHONE', '+880 1711-234567');
define('SITE_PHONE_HREF', '+8801711234567');
define('SITE_WHATSAPP', '+8801711234567');
define('SITE_EMAIL', 'reserve@embersaffron.com');
define('SITE_ADDRESS', 'House 12, Road 11, Banani, Dhaka 1213');
define('SITE_MAP_QUERY', 'Banani, Dhaka, Bangladesh');
define('SITE_ESTABLISHED', 2018);
define('SITE_PRIVATE_DINING', 'A 24-seat private room is available for lunches, dinners and tastings.');
define('SITE_MAX_GUESTS', 20);

// Opening hours, shown in the header, footer, home and contact pages.
define('SITE_HOURS', serialize([
    ['days' => 'Saturday – Thursday', 'time' => '12:00 PM – 11:00 PM'],
    ['days' => 'Friday', 'time' => '1:30 PM – 11:00 PM'],
]));

// ---- Environment ------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', '0'); // set to '1' only while debugging
date_default_timezone_set('Asia/Dhaka');

// mbstring is present on virtually every host, but the site works without it.
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

if (session_status() === PHP_SESSION_ACTIVE) {
    // already running (for example when config.php is included twice)
} else {
    session_start();
}

================================================================================
  EMBER & SAFFRON — Restaurant Website (PHP + MySQL, for cPanel)
================================================================================

A complete restaurant website: public pages, an online table-booking form, a
contact form and a private staff area where you can manage reservations,
messages and the menu.

No Composer, no Node.js, no build step. Upload it and it runs.


--------------------------------------------------------------------------------
1. WHAT'S IN THE FOLDER
--------------------------------------------------------------------------------

  index.php            Home page
  menu.php             Full menu
  reservations.php     Table booking form
  gallery.php          Photo gallery
  about.php            About / story page
  contact.php          Contact details + contact form
  install.php          One-time installer (DELETE AFTER USE)
  config.php           The only file you need to edit

  includes/            Header, footer, database and helper code
  admin/               Private staff area
  assets/css/style.css All styling (one hand-written file)
  assets/js/main.js    Mobile menu + small niceties
  assets/images/       All photographs
  data/menu.json       Starting menu / gallery / testimonials used by the installer

  .htaccess            Apache settings (compression, caching, security)


--------------------------------------------------------------------------------
2. REQUIREMENTS
--------------------------------------------------------------------------------

  * PHP 7.4 or newer (8.x is fine)
  * MySQL 5.7+ or MariaDB 10.3+
  * The pdo_mysql PHP extension (standard on every cPanel host)

Nothing else.


--------------------------------------------------------------------------------
3. HOW TO INSTALL ON cPANEL (about 10 minutes)
--------------------------------------------------------------------------------

STEP 1 — Create the database
  Log in to cPanel and open "MySQL Databases" (or "MySQL Database Wizard").
  a) Create a new database, e.g.  yoursite_ember
  b) Create a new database user, e.g.  yoursite_ember  (give it a strong password)
  c) Add that user to the database and tick "ALL PRIVILEGES".
  Write down the database name, the username and the password.

STEP 2 — Upload the files
  In cPanel, open "File Manager".
  a) Go to public_html (or the folder for the domain you want to use).
  b) Click "Upload" and upload the ZIP file you received.
  c) Back in File Manager, right-click the ZIP and choose "Extract".
  d) If you extracted into a subfolder, move all the files so that
     index.php sits directly inside public_html.
  e) Delete the ZIP file afterwards.

STEP 3 — Edit config.php
  In File Manager, right-click config.php and choose "Edit".
  Replace these four values with the ones from Step 1:

      define('DB_HOST', 'localhost');            // leave as localhost
      define('DB_NAME', 'your_database_name');
      define('DB_USER', 'your_database_user');
      define('DB_PASS', 'your_database_password');

  Save the file.

STEP 4 — Run the installer
  Open your browser and go to:

      https://your-domain.com/install.php

  You should see "Everything is set up and ready." It has created the five
  database tables and loaded the starting menu and gallery.

STEP 5 — Change the admin password
  In config.php find this line:

      define('ADMIN_PASSWORD', 'ember123');

  Change ember123 to your own password and save.

  For stronger security you can store a bcrypt hash instead. Generate one by
  running this in a terminal (or ask your host):

      php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"

  Paste the result into ADMIN_PASSWORD_HASH and clear ADMIN_PASSWORD:

      define('ADMIN_PASSWORD', '');
      define('ADMIN_PASSWORD_HASH', '$2y$10$...');

STEP 6 — Delete the installer
  In File Manager, delete install.php. This is important.

STEP 7 — Check the site
  Visit https://your-domain.com — you should see the restaurant home page.
  Try the booking form once, then log in at:

      https://your-domain.com/admin/

  and your test booking will be waiting under "Reservations".


--------------------------------------------------------------------------------
4. USING THE STAFF AREA (/admin)
--------------------------------------------------------------------------------

  Dashboard     Today's covers, pending requests, unread messages and a quick
                look at the next seatings.

  Reservations  Every booking request. Filter by status. "Confirm" or "Cancel"
                a booking, or delete it. Guests are told their request is
                pending until you confirm it here.

  Messages      Everything sent through the contact form. Mark as read once you
                have replied (the "Reply to" link opens their email address).

  Menu          Mark dishes off when they run out (they stay on the menu but
                show "Currently unavailable"), feature dishes on the home page,
                add new dishes, or delete them.


--------------------------------------------------------------------------------
5. CHANGING THE RESTAURANT DETAILS
--------------------------------------------------------------------------------

Everything editable lives in config.php:

  SITE_NAME           Restaurant name (shown in the header, footer and browser tab)
  SITE_TAGLINE        Small line under the name
  SITE_PHONE          Phone number as people should read it
  SITE_PHONE_HREF     Phone number for the dial link, digits only
  SITE_WHATSAPP       WhatsApp number, digits only, with country code
  SITE_EMAIL          Email address
  SITE_ADDRESS        Street address
  SITE_MAP_QUERY      What to search for on the Google map
  SITE_ESTABLISHED    Opening year (used for "Est. 2018")
  SITE_PRIVATE_DINING Private dining sentence
  SITE_MAX_GUESTS     Highest party size accepted by the booking form
  SITE_HOURS          Opening hours, shown on the home page and contact page

The colours and fonts are defined at the very top of assets/css/style.css under
":root". Change --saffron or --ink there and the whole site follows.


--------------------------------------------------------------------------------
6. REPLACING THE PHOTOGRAPHS
--------------------------------------------------------------------------------

All photos live in assets/images/. To swap one, upload your own file with the
same name and it will appear everywhere. The names used are:

  hero-grill.jpg    Home page background
  interior.jpg      Reservations page + gallery
  kebab.jpg         Menu page background
  biryani.jpg       Featured dish
  curry.jpg         Featured dish
  paneer.jpg        Featured dish
  bread.jpg         About page
  dessert.jpg       Featured dish
  lassi.jpg         Featured dish
  chef.jpg          Home page + About page
  plated.jpg        Contact page background

Use JPGs around 1200px wide so the site stays fast.

To change which photo belongs to which dish, edit the "image_url" value for that
dish in the admin area (or directly in the menu_items table).

To add gallery photos: upload the file to assets/images/, then add a row to the
gallery_images table in phpMyAdmin with the filename, a caption and a category
(Food, Interior, Kitchen or Drinks).


--------------------------------------------------------------------------------
7. TROUBLESHOOTING
--------------------------------------------------------------------------------

  "Could not connect to the database"
      The DB_ values in config.php do not match the database you created, or the
      user was not added to the database with ALL PRIVILEGES. Re-check Step 1.

  A blank white page
      A PHP error is being hidden. Open config.php and change
          ini_set('display_errors', '0');
      to
          ini_set('display_errors', '1');
      Reload the page, read the message, then set it back to '0'.

  The booking form says "Your session expired"
      Sessions are not working, which usually means the folder is not writable
      or cookies are blocked. Try another browser first; if it persists, ask
      your host to check the PHP session save path.

  The Google map is blank
      The map needs an internet connection in the browser. If you would rather
      not load Google Maps at all, delete the <iframe> block in index.php and
      put your own embed code in its place.

  Fonts look plain
      The site loads Cormorant Garamond and Inter from Google Fonts. If your
      visitors are in a region where that is blocked, download the fonts,
      upload them to assets/fonts/ and change the two --font variables at the
      top of assets/css/style.css.


--------------------------------------------------------------------------------
8. SECURITY NOTES
--------------------------------------------------------------------------------

  * Every database query uses prepared statements — user input is never glued
    into SQL.
  * All output is escaped with htmlspecialchars(), so nothing can inject HTML
    or scripts into your pages.
  * Every form (public and admin) is protected against cross-site request
    forgery with a per-session token.
  * Passwords are compared in constant time; the staff area uses
    session_regenerate_id() on login to prevent session fixation.
  * The includes/ and data/ folders are blocked from web access, and config.php
    is refused by .htaccess.
  * The staff area is set to noindex so it stays out of search engines.

Before going live: change ADMIN_PASSWORD, delete install.php, and make sure
display_errors is set back to '0'.


--------------------------------------------------------------------------------
9. CREDITS
--------------------------------------------------------------------------------

  Photography  Pexels (free to use) — see assets/images for the files
  Fonts        Cormorant Garamond & Inter, Google Fonts (SIL Open Font License)
  Map          Google Maps embed

================================================================================

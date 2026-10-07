EMBER & SAFFRON — STATIC HTML/CSS/JS VERSION
================================================

Converted from the supplied PHP/cPanel project.

Included public pages:
- index.html
- menu.html
- gallery.html
- about.html
- contact.html
- reservations.html

Assets:
- assets/css/style.css
- assets/js/main.js
- assets/js/site.js
- assets/images/*.svg

IMPORTANT STATIC-HOSTING CHANGE
--------------------------------
GitHub Pages cannot execute PHP or MySQL. Therefore:
1. PHP includes were converted into normal HTML.
2. Menu/gallery/testimonial data is embedded into the HTML.
3. The PHP/MySQL admin dashboard was not carried over because it cannot run on GitHub Pages.
4. Contact form uses the visitor's email application.
5. Reservation form prepares a WhatsApp/email request and generates a browser-side reference. It does NOT save bookings to a database.
6. The original project referenced JPG images that were not present in the supplied ZIP. Local SVG artwork has been added so there are no broken image icons. Replace these SVGs with the client's real photos later if available.

UPLOAD TO CPANEL
----------------
Upload the CONTENTS of this folder into public_html.
Do not upload the outer folder itself if you want the site at the main domain.

UPLOAD TO GITHUB PAGES
----------------------
Upload the contents to the repository root, then enable GitHub Pages from the repository's Pages settings.
The .nojekyll file is already included.

FORMS
-----
For real database-backed reservations/messages, connect the forms to a service such as Formspree/Getform or a custom PHP/API endpoint. This static version intentionally avoids PHP/MySQL.

ORIGINAL CLIENT DETAILS
-----------------------
Site: Ember & Saffron
Address: House 12, Road 11, Banani, Dhaka 1213
Phone: +880 1711-234567
Email: reserve@embersaffron.com
WhatsApp: +8801711234567

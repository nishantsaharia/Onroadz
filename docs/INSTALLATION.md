# SAAFAAR Aether — cPanel deployment guide
Stack: PHP 8.2+, PDO MySQL/MariaDB, Apache mod_rewrite, HTTPS.

1. Back up your existing saafaar.in files and database. Open the domain's actual cPanel document root (usually public_html, or an addon-domain folder).
2. Upload SAAFAAR-AETHER-FULL-WEBSITE.zip. Extract the **contents directly into that document root**, not an extra parent directory. Ensure index.php, .htaccess, cars/, travel-guides/, app/, admin/, assets/ are siblings. Enable "Show hidden files" to verify .htaccess.
3. Set PHP 8.2 or later via MultiPHP Manager; enable PDO_MySQL, mbstring and fileinfo. Enable SSL for saafaar.in. The provided .htaccess enforces https://saafaar.in.
4. In cPanel MySQL Databases create a NEW dedicated SAAFAAR database and user (do not reuse Onroadz). Grant privileges. Import database/schema.sql through phpMyAdmin.
5. Copy .env.example to .env and enter real DB_HOST, DB_NAME, DB_USER and DB_PASS. Keep the password secret; .htaccess blocks hidden files and all non-public directories.
6. In cPanel Terminal/SSH, from the document root, run: php bin/create-admin.php YOUR_PRIVATE_USERNAME. Choose a unique password of 14+ characters. Never publish credentials.
7. Open /admin/ and enter each real SAAFAAR vehicle, its pricing and actual photograph. Keep it in DRAFT until verified, then set Active and "Approved". No vehicles are published by default.
8. Browse the exact requested pages: /, /cars/, /travel-guides/, /travel-guides/tawang-mountain-drive/, /search/, /request-booking/, /booking-confirmation/, /manage-booking/, /enquire/, /offline/, /404/, /contact/, /admin/. Only confirmed, approved cars appear as results.
9. Check https://saafaar.in/sitemap.xml and /robots.txt. Submit the sitemap in Google Search Console after confirming all pages respond correctly.
10. Leave PAYMENTS_ENABLED=0 until merchant/payee verification and approval. Customer requests remain pending until staff review. Do not assume a submitted UTR means money was received.
11. If clean URLs fail: confirm mod_rewrite is enabled, .htaccess was not omitted, and the document root is correct. Physical route entrypoints are also present for primary pages. Inspect cPanel Metrics > Errors and Apache logs; never enable production display_errors.
12. Test HTTPS, mobile layouts, private booking lookup, admin authentication and a real request against a test approved inventory record. Roll back from backup if any critical error persists.

The bundled photos come from user-controlled public saafaar.in images. Source vehicle ZIP images from earlier conversations must still be mapped and approved to each vehicle; they are NOT silently substituted.

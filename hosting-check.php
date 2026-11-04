<?php
// TEMPORARY, read-only deployment diagnostic. Delete after setup.
// Does not load config, connect to the database, start a session or print server paths.
ini_set('display_errors', '0');
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
header('X-Content-Type-Options: nosniff');
$root = dirname(__DIR__);
$checks = array('PHP 8.2 or newer' => PHP_VERSION_ID >= 80200);
foreach (array('pdo_mysql', 'mbstring', 'gd', 'fileinfo', 'session') as $ext) {
    $checks['Extension: ' . $ext] = extension_loaded($ext);
}
$checks['WebP encoding support'] = function_exists('imagewebp');
foreach (array('app/bootstrap.php', 'app/layout.php', 'app/booking.php', 'app/content.php', 'app/policies.php', 'config/config.example.php') as $file) {
    $checks['Readable private file: ' . $file] = is_file($root . '/' . $file) && is_readable($root . '/' . $file);
}
$checks['Private configuration exists (values are not displayed)'] = is_file($root . '/config/config.php');
$checks['Private storage exists and is writable by PHP'] = is_dir($root . '/storage/private') && is_writable($root . '/storage/private');
$checks['Existing session directory is writable, or can be created'] = is_dir($root . '/storage/private/sessions') ? is_writable($root . '/storage/private/sessions') : $checks['Private storage exists and is writable by PHP'];
$checks['Uploads directory exists and is writable by PHP'] = is_dir(__DIR__ . '/uploads') && is_writable(__DIR__ . '/uploads');
echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>SAAFAAR hosting checks</title></head><body><h1>SAAFAAR hosting checks</h1><p>PHP version: ' . htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') . '</p><ul>';
foreach ($checks as $label => $ok) {
    echo '<li><strong>' . ($ok ? 'PASS' : 'FAIL') . '</strong> ' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</li>';
}
echo '</ul><p>This page cannot inspect Apache rewrite policy or verify database credentials. If this page also returns HTTP 500, check the Apache/PHP error log before changing application files.</p><p>Delete hosting-check.php after diagnosis. Never share passwords, tokens, identity documents or database exports.</p></body></html>';

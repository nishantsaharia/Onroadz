# Diagnose a hosting HTTP 500

An HTTP status alone does not identify a root cause. The latest PHP/Apache log entry, the selected PHP version and actual document root are required to identify a live hosting failure. Do not share credentials or full configuration files.

## Check first
1. cPanel → Metrics → Errors (or the domain's error_log). Copy the latest entry at the time of the failed request, with credentials redacted.
2. In MultiPHP Manager or Select PHP Version, set the domain to PHP 8.2 or newer and enable the required extensions.
3. Verify the domain document root is the supplied `public` directory. The private `app`, `config` and `storage` directories must remain its siblings. Copying only `public/*` into an unrelated `public_html` breaks the ../app path.
4. Temporarily visit `/hosting-check.php` after installing the updated public file and private files in the documented layout. It reports flags and PHP version only; it never loads credentials, connects to the DB, or prints server paths. Delete it after setup.
5. If hosting-check.php itself returns 500, examine the server log for a PHP-handler or .htaccess problem. Do not remove private-directory deny rules or make all files 0777.

## Typical log messages and appropriate correction
| Evidence | Correction |
|---|---|
| Failed opening required ... app/bootstrap.php or app/front-controller.php | Restore the complete project layout and correct domain document root. Do not move private source into the public web directory. |
| Unexpected token / unsupported type / PHP version below 8.2 | Select supported PHP for this specific domain and retry. CLI and web PHP versions can differ. |
| Permission denied / cannot create private session directory | Give the account's PHP worker ownership/read-write access to private storage; keep it outside public. Use per-user PHP-FPM/suPHP where appropriate. |
| could not find driver | Enable pdo_mysql for the selected web PHP version. |
| SQLSTATE Access denied / unknown database / table not found | Create the independent DB, import schema once into the new empty DB, and configure private config/config.php. Never reimport schema over live bookings. |
| Options not allowed here / Require not allowed here / Invalid command RewriteEngine | Ask the host to confirm Apache modules and AllowOverride settings; adapt only the directive named by the log. Do not disable protections speculatively. |

## Updated package changes
A small public entry point checks PHP, required private files and storage before loading the application. Setup failures now return a clear 503 message rather than an unhandled require error. The exception handler is installed before configuration/session initialization, and rollback no longer opens a new database connection while handling unrelated errors. Public archive files/directories have portable 0644/0755 modes. Private storage remains restricted to the PHP account. These changes do not establish the root cause or fix a server we cannot access.

Back up your existing installation before applying updates. Replace only reviewed application files; preserve config/config.php, actual uploaded vehicle images, private originals, sessions and database records. Do not overwrite live configuration with the example or use a full-database reset to troubleshoot.

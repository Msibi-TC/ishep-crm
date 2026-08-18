# DirectAdmin staging deployment

Deploy only to a password-protected preview subdomain. Do not upload the local database or request/commit hosting credentials.

## Server layout

```text
/home/DIRECTADMIN_USER/domains/DOMAIN/ishep-preview/
  public/  src/  templates/  config/  database/  bin/  storage/  vendor/  .env
```

Set the subdomain document root to `.../ishep-preview/public`; no other directory is web-accessible. Select PHP 8.3+ with PDO, pdo_mysql, mbstring, openssl, fileinfo, session, json, and filter. Zip and intl are recommended, not required.

## Exact deployment

1. In DirectAdmin create `preview.example.com`, select PHP 8.3+, and set its document root to the release `public` directory.
2. Issue and enforce a Let's Encrypt certificate before sharing the URL.
3. Create an empty MySQL/MariaDB database and user, grant that user privileges on only that database, and note DirectAdmin's account prefixes.
4. Open phpMyAdmin, select the empty staging database, review and import `database/install.sql`. Never use `migrate:fresh`, reset commands, a development export, or real personal data.
5. Build locally with `powershell -ExecutionPolicy Bypass -File plain-php/bin/build-release.ps1`, verify the printed SHA-256, upload the ZIP, and extract it to `ishep-preview/`.
6. Copy `.env.example` to `.env`. Set `APP_ENV=staging`, `APP_DEBUG=false`, the HTTPS `APP_URL`, Africa/Johannesburg timezone, a new random `APP_KEY`, prefixed database credentials, `SESSION_SECURE=true`, `SESSION_SAMESITE=Lax`, and absolute staging storage/log paths. Do not use localhost or Windows paths.
7. Make `storage/logs`, `storage/sessions`, and `storage/private/documents` writable by the PHP account (normally directories 750 or 770 and files 640 or 660 depending on ownership). Never use 777.
8. Protect the subdomain with DirectAdmin Password Protected Directories. Keep application authentication enabled, use no credentials in URLs, and send link and password through separate channels. `robots.txt` and `X-Robots-Tag` discourage indexing but are not access control.
9. Via DirectAdmin SSH/terminal, run `php bin/test-db.php` and `php bin/verify-schema.php`. Create preview accounts only with `php bin/user-admin.php`; see `ROLE_TESTING_GUIDE.md` in the repository.
10. Smoke-test `/health`, `/`, `/login`, `/register`, `/careers`, `/bursaries`, all role dashboards, authorization failures, mobile navigation, and invoice/receipt print preview.
11. Back up the current release directory and staging database before each update. Send the manager only the HTTPS link after checks pass.

If `AllowOverride` is unavailable, ask the host to apply the rules from `public/.htaccess` in the subdomain VirtualHost: disable indexes, deny dot/sensitive files, serve existing files directly, route other requests to `public/index.php`, and set the supplied security headers. Do not assume root access.

## Rollback

Put the preview behind maintenance/password protection, restore the previous timestamped release directory and its matching `.env`, restore the matching staging-only database backup if schema/data changed, reapply writable ownership, run the two verification commands, smoke-test `/health`, then reopen preview access. Never roll back by deleting shared data or importing a local development database.

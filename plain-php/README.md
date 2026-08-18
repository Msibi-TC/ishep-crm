# ISHEP Plain-PHP MVP

This is a parallel PHP 8.0-compatible demonstration of the functionality currently implemented in Laravel. PHP 8.3+ is recommended for production. Laravel remains intact. The MVP reads the existing users, RBAC, reference, audit, and password-reset tables through PDO; it does not run migrations or consume Laravel session/cache/queue payloads.

## Setup

1. Confirm the operator-created database is named `ishep_crm`.
2. Review the [phpMyAdmin setup guide](docs/PHPMYADMIN_SETUP.md). Import `database/install.sql` manually only when ready; setup never runs it automatically.
3. Copy `.env.example` to `.env`, keep `DB_DATABASE=ishep_crm`, and set a random `APP_KEY` plus the local database credentials. Never commit `.env`.
4. Ensure PHP has PDO MySQL, mbstring, openssl, fileinfo, session, and JSON support.
5. Generate the dependency-free Composer autoloader: `composer dump-autoload --working-dir=plain-php`.
6. Run tests: `php plain-php/tests/run.php`.

The installation SQL uses the existing `ishep_crm` database and contains no database creation, drop, truncate, or automatic import operation.

### PHP built-in server

From the repository root run:

```text
php -S 127.0.0.1:8080 -t plain-php/public plain-php/bin/serve.php
```

Open `http://127.0.0.1:8080`. Set `APP_URL` to the same origin. `/health` reports only application state, PHP version, database reachability, and environment.

### XAMPP / Apache

Use PHP 8.3+ and enable `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `session`, and `rewrite_module`. Prefer an Apache virtual host whose `DocumentRoot` is the absolute `plain-php/public` directory and allow `.htaccess` overrides there. Alternatively browse through the repository path, for example `/ishep-crm/plain-php/public/`; URL generation follows `APP_URL`, so set it to that exact base URL.

Writable directories are `storage/logs` and `storage/sessions`; `storage/private` is reserved for future secure files. They must not be web-accessible. Bootstrap is committed as a static licensed asset, so npm and Vite are not needed at runtime.

## Security and coexistence notes

- State-changing forms require CSRF tokens. Sessions use strict cookie-only mode, HttpOnly and SameSite=Lax, with Secure enabled under HTTPS.
- Login is limited to five failed attempts per normalized email/IP in approximately 60 seconds. Only active accounts can authenticate.
- Public registration always assigns `registered_user`; no public role/status input is accepted.
- Password-reset requests always return a generic message. Random tokens are stored as SHA-256 hashes, expire after 60 minutes, and are single-use. With `MAIL_DRIVER=log`, the development URL is written to the ignored application log.
- Plain-PHP and Laravel reset-token formats are deliberately not guaranteed to work across runtimes during coexistence. Request and complete a reset in the same runtime.
- The Membership, Career, Bursary and verification pages are truthful placeholders; missing CRM workflows were not invented.

Production errors suppress exception detail. Logs redact context keys associated with passwords, tokens, secrets, keys, and credentials.

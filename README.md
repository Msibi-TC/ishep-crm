# ISHEP CRM and Portal Suite

> **Current active application:** `plain-php/`
>
> **Web root:** `plain-php/public/`
>
> **Database:** `ishep_crm`
>
> **Local URL:** `http://localhost:8080`
>
> **Start command:** `php -S localhost:8080 -t plain-php/public plain-php/bin/serve.php`

Use the [plain-PHP setup guide](plain-php/README.md) and [phpMyAdmin installation guide](plain-php/docs/PHPMYADMIN_SETUP.md). Verify with `php plain-php/bin/test-db.php`, `php plain-php/bin/verify-schema.php`, and `php plain-php/tests/run.php`.

The Laravel files at the repository root are retained temporarily as legacy reference material. Do not use them as the active runtime or move/delete them during current plain-PHP feature work.

## Project purpose

ISHEP CRM and Portal Suite is currently delivered through a parallel plain-PHP application for membership administration, careers, and bursary workflows. The retained Laravel foundation documents and preserves earlier behavior while plain-PHP feature parity progresses.

## Technology stack

- PHP 8.3+
- Laravel 12
- Laravel Blade templates
- Bootstrap 5
- MySQL 8+
- Composer
- Node.js and npm
- PHPUnit
- Git and GitHub
- VS Code

## Prerequisites

- PHP 8.3 or newer
- Composer 2.x
- Node.js 20+ and npm
- MySQL 8+
- Git
- VS Code with PHP and Laravel extensions

## Installation

1. Clone the repository.
2. Copy `.env.example` to `.env`.
3. Update the MySQL settings in `.env` for your local environment.
4. Install PHP dependencies:
   `composer install`
5. Install frontend dependencies:
   `npm install`
6. Generate the application key:
   `php artisan key:generate`

## MySQL database setup

1. Open MySQL Workbench.
2. Create a database named `ishep_crm`.
3. Create a local MySQL user with the required privileges.
4. Update `.env` with the host, port, database name, username, and password.
5. Ensure the database, character set, and collation are configured to use `utf8mb4` and `utf8mb4_unicode_ci`.
6. Use UTC for application and database timestamps.

For the parallel plain-PHP MVP, the database name is also `ishep_crm`. Its reviewed, manual-only installation path is documented in [`plain-php/docs/PHPMYADMIN_SETUP.md`](plain-php/docs/PHPMYADMIN_SETUP.md). The SQL script does not create, drop, recreate, or truncate the database, and it is never imported automatically.

## Environment configuration

Use the values in `.env.example` as a template. Do not commit real credentials or application keys. Keep placeholders only in tracked files.

## Migration and seed commands

```bash
php artisan migrate
php artisan migrate:status
php artisan db:seed
```

Do not use destructive migration commands against a database containing development or production data.

## Authentication and access control

The application provides public registration, session login/logout, password reset, an authenticated dashboard, account-status enforcement, and role-protected staff dashboard placeholders. Public registration always assigns only the `registered_user` role. Staff roles (`administrator`, `finance`, and `super_user`) must be assigned through an authorised administrative process or the secure console command:

```bash
php artisan users:assign-role user@example.com administrator
```

The user and role must already exist. Company, Individual, and Student are membership types, not roles. Applicant status will arise from a future application submission and is not a permanent role.

The active plain-PHP runtime provides `GET /dashboard`, `GET /profile`, `GET /profile/edit`, and CSRF-protected `POST /profile`. Profile updates always target the authenticated session user. Account email is read-only here, and roles and account status cannot be changed through this workflow. Existing installations apply `plain-php/database/patches/2026_08_18_create_member_profiles.sql` once; new installations receive the same schema from `plain-php/database/install.sql`.

Membership applications use `membership_applications` and append-only `membership_application_events`, installed by `2026_08_18_create_membership_applications.sql`. Members use `/membership/application`; administrators and super users review through `/admin/membership-applications`. The lifecycle is `draft → submitted → under_review → approved|rejected`, with allowed withdrawal and explicit rejected-application correction. Approval does not mean paid or activated membership. Documents, payments, activation, and certificates remain deferred, and finance users cannot review.

## Frontend build commands

```bash
npm install
npm run build
npm run dev
```

## Development server

```bash
php artisan serve
```

## Testing commands

```bash
php artisan test
php artisan test --filter=HomePageTest
```

Tests use SQLite in memory and do not modify the configured MySQL development database.

Active plain-PHP verification:

```bash
php plain-php/bin/test-db.php
php plain-php/bin/verify-schema.php
php plain-php/tests/run.php
php -S localhost:8080 -t plain-php/public plain-php/bin/serve.php
```

## Git workflow

```bash
git status
git add <files>
git commit -m "chore: establish ISHEP CRM project foundation"
git push origin <branch>
```

## Notes

Task 2 establishes authentication, RBAC, reference data, and audit storage only. It does not implement membership applications, payments, careers, or bursary workflows.

# ISHEP CRM and Portal Suite

## Project purpose

ISHEP CRM and Portal Suite is a Laravel-based multi-portal platform for membership administration, careers, and bursary workflows. This foundation establishes the project structure, environment configuration, shared branding, and public landing pages for the future CRM and portal system.

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

## Git workflow

```bash
git status
git add <files>
git commit -m "chore: establish ISHEP CRM project foundation"
git push origin <branch>
```

## Notes

Task 2 establishes authentication, RBAC, reference data, and audit storage only. It does not implement membership applications, payments, careers, or bursary workflows.

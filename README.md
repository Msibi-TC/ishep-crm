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

## Environment configuration

Use the values in `.env.example` as a template. Do not commit real credentials or application keys. Keep placeholders only in tracked files.

## Migration commands

```bash
php artisan migrate
php artisan migrate:fresh
php artisan migrate:status
```

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

## Git workflow

```bash
git status
git add <files>
git commit -m "chore: establish ISHEP CRM project foundation"
git push origin <branch>
```

## Notes

This task intentionally establishes the foundation only. It does not implement the full membership, career, or bursary workflows.

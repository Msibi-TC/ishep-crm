# ISHEP CRM Project Structure

## Active runtime

- Active application: `plain-php/`
- Web/document root: `plain-php/public/`
- Active database: `ishep_crm`
- Local URL: `http://localhost:8080`
- Start command: `php -S localhost:8080 -t plain-php/public plain-php/bin/serve.php`

The Laravel application files at the repository root are temporary legacy reference files. They are not the active runtime and must not be moved, expanded, or deleted during current plain-PHP feature work.

## Directory map

| Path | Responsibility |
|---|---|
| `plain-php/public/` | Front controller, Apache rules, and public static assets; the only web-exposed directory |
| `plain-php/src/Bootstrap/` | Application construction and dependency wiring |
| `plain-php/src/Config/` and `plain-php/config/` | Environment loading and centralized configuration |
| `plain-php/src/Database/` | PDO connection factory |
| `plain-php/src/Http/` | Request/response objects, controllers, and middleware |
| `plain-php/src/Repositories/` | Prepared PDO queries and persistence boundaries |
| `plain-php/src/Services/` | Authentication, registration, password-reset, authorization, and rate-limit workflows |
| `plain-php/src/Security/` | Sessions, CSRF, and authoritative password policy |
| `plain-php/src/Validation/` | Server-side form validation |
| `plain-php/templates/` | Escaped server-rendered layouts and page templates |
| `plain-php/public/assets/` | Local Bootstrap, custom CSS, and minimal vanilla JavaScript |
| `plain-php/database/` | Reviewed manual installer and non-destructive SQL patches |
| `plain-php/tests/` | Static, unit-style, database-integration, and disposable-record checks |
| `plain-php/bin/` | Built-in-server router and read-only database/schema verification commands |
| `plain-php/storage/logs/` | Private structured application logs |
| `plain-php/storage/sessions/` | Native PHP session files and rate-limit state |
| `plain-php/storage/private/` | Reserved non-public file storage |

## Local configuration and safety

`plain-php/.env` contains local secrets and must remain ignored and untracked. Configure it from `plain-php/.env.example`; never paste its values into documentation or logs. Logs, session files, private uploads, Composer-generated `plain-php/vendor/`, database exports, and disposable test data must not be committed.

The database installer is manual-only. Existing installations that predate membership selection use `plain-php/database/patches/2026_08_18_add_user_membership_type.sql` once after confirming the column is absent. Never drop, recreate, truncate, or reset `ishep_crm` as part of application startup or testing.

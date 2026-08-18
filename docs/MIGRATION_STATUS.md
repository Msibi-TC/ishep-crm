# Plain-PHP Migration Status

## Audit snapshot

| Field | Status |
|---|---|
| Repository | `C:\xampp\htdocs\ishep-crm` (`Msibi-TC/ishep-crm`) |
| Remote | `origin` = `https://github.com/Msibi-TC/ishep-crm.git` |
| Starting branch | `main` at `bf49894`, clean and synchronized with `origin/main` |
| Audit branch | `migration/plain-php-audit` |
| Audit date | 2026-08-18 |
| Current technology | PHP 8.3.33 CLI, Laravel 12.66.0, MySQL target, Bootstrap 5, Vite 7.3.6, PHPUnit 11.5.56 |
| Target technology | PHP 8.3+, Apache, PDO/MySQL or MariaDB, front controller, server templates, Bootstrap/static assets, native sessions/CSRF |
| Migration stage | Urgent parallel MVP implemented across Stages 1–3; parity verification remains incomplete |
| Latest pre-audit commit | `bf49894` (`docs: finalize task 2 status record`) |
| Audit commit | `bcd0dfe` (`docs: audit Laravel to plain PHP migration`) |
| Documentation correction | `a812741` (`docs: fix migration plan whitespace`); no history rewrite was performed |
| Pushed branch | `migration/plain-php-audit` successfully pushed to `origin` |
| Push state | Local branch synchronized with `origin/migration/plain-php-audit` before this checkpoint update |
| MVP branch | `migration/plain-php-mvp`, created from audit checkpoint `83bb837` |
| Implementation state | Parallel runtime exists under `plain-php/`; Laravel remains unchanged |
| Working tree state | Clean immediately before this checkpoint-only status edit |
| Audit-status commit | The commit containing this update; it does not claim to contain its own final hash |

## Documentation reviewed

No `AGENTS.md` exists. Reviewed `README.md`, `docs/architecture.md`, `docs/database-conventions.md`, `docs/development-plan.md`, `docs/PROJECT_STATUS.md`, Git history, all relevant code/configuration/schema/templates/assets/tests, and dependency lockfiles. The prohibited team repository was not accessed.

## Verified functionality and inventory

- Implemented: five public presentation routes; registration; login/logout; password reset; active-account checks; seeded RBAC; generic/member and three staff dashboard pages; province/membership-type seed data; role assignment CLI.
- Partial/placeholder: membership/Career/Bursary/verification pages, permission enforcement foundation, profession reference storage, and audit-log storage.
- Missing: membership applications/profiles, documents/uploads, student eligibility, verification processing, finance operations, subscriptions/payments/refunds/certificates, Career/Bursary workflows, reports, notices, application jobs/scheduling, PDFs/exports, and external APIs.
- Counts: 18 application routes (21 total at last integrated Laravel listing), six concrete controllers, three custom middleware, two form requests, seven models, 14 active application views plus one unused scaffold view, seven migrations, 16 tables, four seeders including the root seeder, one factory, one custom console command, and 22 test methods.

Authentication uses Laravel session auth, password hashing, email normalization, login throttling, generic errors, regeneration/invalidation, password-broker tokens, and account status checks. RBAC uses roles/permissions with server-side role middleware; four roles and 22 permissions are seeded. Public registration can assign only `registered_user`.

No application upload, private-document, generated certificate, PDF, export, third-party API, payment gateway, custom mail class, job, listener, or scheduled task was found. Password reset is the sole mail/notification capability and `.env.example` uses the log mailer. Queue configuration/tables are present but unused; the example selects synchronous execution.

## Database state and preservation

The seven migrations define 16 tables: users, password reset tokens, sessions, two cache tables, three queue tables, four RBAC tables, three reference tables, and audit logs. All are PDO-compatible. Preserve user/RBAC/reference/audit data and schema. Leave Laravel session/cache/queue tables in place during coexistence; their serialized payloads are not portable and should not be consumed by the new runtime. Explicitly replace Eloquent enum, boolean, decimal, JSON, datetime, hashing, and timestamp behavior.

The live schema was not queried during this audit because the absent vendor dependencies could not be restored in the current environment. The last integrated status records MySQL 8.0.46 and all seven migrations applied. No migrations, seeders, schema writes, data reads, or data exports were performed.

## Baseline verification

| Check | 2026-08-18 result |
|---|---|
| `composer validate --no-check-publish` | Passed |
| Frontend dependency restore | `npm.cmd ci` passed: 89 packages, zero reported vulnerabilities |
| `npm.cmd run build` | Passed: Vite 7.3.6, 60 modules, 7.63s |
| `git diff --check` before docs | Passed |
| PHPUnit / route listing / Pint | Blocked: `vendor/` absent; archive install lacks ZIP; source install timed out |
| `migrate:status` | Not run for the same dependency reason |
| Last integrated recorded baseline | 22 tests, 80 assertions, 21 routes, Pint passed, seven migrations applied |

The PHPUnit configuration selects SQLite `:memory:`, array sessions/cache/mail, and sync queues, so the existing suite is designed not to touch valued MySQL data. No screenshot automation or Markdown linter is configured.

## Files created and completed work

- Created `docs/PLAIN_PHP_MIGRATION_PLAN.md`.
- Created `docs/MIGRATION_STATUS.md`.
- Audited Git identity/state/history, documentation, dependencies, routes, code, security, schema, views/assets, and tests.
- Defined database preservation, plain-PHP architecture, security replacements, route/feature parity gates, nine implementation/deployment stages, HOSTAFRICA checklist, risks, and decisions.
- Application code, build configuration, dependencies/lockfiles, environment files, database, and uploads were not changed. `vendor/`, `node_modules/`, and generated build artifacts are ignored local prerequisites/artifacts only.

## Plain-PHP MVP checkpoint

The parallel MVP implements the requested public pages/placeholders, registration, login/logout, password-reset request/reset, member and role-protected staff dashboards, active-account enforcement, CSRF, throttling, native sessions, PDO repositories, health endpoint, local Bootstrap assets, centralized errors/logging, and a dependency-free test runner. It targets the existing users/RBAC/reference/audit/reset tables without schema changes and deliberately does not consume Laravel framework payload tables.

Verification on 2026-08-18:

- Composer generated the PSR-4 autoloader without network packages.
- All plain-PHP source/template/test files passed `php -l`.
- `php plain-php/tests/run.php` passed 21 checks.
- Built-in-server smoke checks returned 200 for public/auth forms, 302 for an unauthenticated dashboard, 404 for a missing page, 405 for an invalid method, 419 for missing CSRF, and 503 from health when database credentials were intentionally unavailable.
- DB-backed registration/login/reset/RBAC were not live-tested because no `plain-php/.env` credentials were created or copied. No database query or write was performed during this implementation.

## Blockers, risks, and pending decisions

The no-dependency MVP runs on the PHP 8.3 CLI despite ZIP being unavailable. A live database demonstration requires a local, ignored `plain-php/.env` populated by the operator. XAMPP/Apache still requires alignment from PHP 8.0.30 to PHP 8.3+ with `PDO`, `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `session`, and `json`; enable `zip` for reliable Composer workflows. `intl` remains recommended for future locale-aware formatting.

Primary risks are authentication/security regression, confusion between placeholders/permissions and implemented domains, loss of Eloquent casts/transactions, incomplete business rules, premature framework-table removal, visual drift, and unconfirmed shared-host capabilities.

Confirm: HOSTAFRICA plan/PHP/extensions/document root/cron/SMTP/backups; database engine/version; on-host Composer policy; membership/application/document/verification rules; account/RBAC/session/email-verification rules; fees/payment/refund/renewal/certificate rules; Career/Bursary/reporting/retention requirements; and eventual framework-table disposition.

## Remaining stages and recommended next task

Stages 1–3 now have an MVP implementation but still require configured-database integration tests, security review, visual acceptance, and Apache/XAMPP verification before their parity gates can close. Entirely remaining stages are: 4 membership/documents/verification; 5 finance; 6 Career/Bursary; 7 communications/reports; 8 parity/Laravel retirement; and 9 deployment/operations.

Recommended next task: configure an ignored local MVP environment, run read/write parity checks against a disposable or backed-up personal development database, add database integration tests, and review authentication/reset/RBAC before deployment. Do not add missing CRM domains or alter the schema in that task.

## Append-only changelog

### 2026-08-18 — Stage 0 audit

- Verified clean synchronized `main`, correct personal remote, ignored/untracked `.env`, and no tracked private uploads/database exports.
- Created `migration/plain-php-audit` from `bf49894`.
- Completed the Laravel inventory and plain-PHP migration design.
- Recorded current and historical baseline results separately.
- No application runtime or database changes made.

### 2026-08-18 — Audit checkpoint

- Recorded audit commit `bcd0dfe` and the non-rewritten whitespace follow-up `a812741`.
- Confirmed `migration/plain-php-audit` was pushed to `origin` and synchronized before this status update.
- Confirmed only the two approved migration documents differ from `origin/main`.
- Confirmed the Stage 0 documentation is committed and the plain-PHP application implementation has not started.
- Kept Stage 1 blocked pending aligned PHP 8.3 Apache/CLI runtimes and required extensions.

### 2026-08-18 — Urgent plain-PHP MVP

- Created `migration/plain-php-mvp` from the completed audit branch.
- Added the independent `plain-php/public/index.php` runtime and supporting architecture without deleting or modifying Laravel.
- Implemented the represented public, authentication, dashboard, account-status, and RBAC behavior; future CRM pages remain truthful placeholders.
- Added local Bootstrap assets, Apache/built-in-server instructions, security controls, PDO repositories, reset tokens, health reporting, and automated checks.
- Performed no migrations, schema changes, seed operations, database queries, or database writes; live DB parity remains pending local ignored credentials.

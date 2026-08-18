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
| Migration stage | Stage 0 audit complete; implementation not started |
| Latest pre-audit commit | `bf49894` (`docs: finalize task 2 status record`) |
| Audit commit | `bcd0dfe` (`docs: audit Laravel to plain PHP migration`) |
| Documentation correction | `a812741` (`docs: fix migration plan whitespace`); no history rewrite was performed |
| Pushed branch | `migration/plain-php-audit` successfully pushed to `origin` |
| Push state | Local branch synchronized with `origin/migration/plain-php-audit` before this checkpoint update |
| Implementation state | Stage 0 documentation only; Stage 1 application implementation has not started |
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

## Blockers, risks, and pending decisions

There is no blocker to completing the documentation audit. Stage 1 remains blocked because the active PHP 8.3 CLI lacks ZIP extraction and the alternate XAMPP PHP is 8.0.30. Before Stage 1, align the Apache and CLI runtimes on PHP 8.3 and enable `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `json`, `mbstring`, `openssl`, `pcre`, `PDO`, `pdo_mysql`, `session`, `tokenizer`, `xml`, and `zip`. `intl` is recommended for future locale-aware formatting but is not a Stage 1 requirement.

Primary risks are authentication/security regression, confusion between placeholders/permissions and implemented domains, loss of Eloquent casts/transactions, incomplete business rules, premature framework-table removal, visual drift, and unconfirmed shared-host capabilities.

Confirm: HOSTAFRICA plan/PHP/extensions/document root/cron/SMTP/backups; database engine/version; on-host Composer policy; membership/application/document/verification rules; account/RBAC/session/email-verification rules; fees/payment/refund/renewal/certificate rules; Career/Bursary/reporting/retention requirements; and eventual framework-table disposition.

## Remaining stages and recommended next task

Remaining stages are: 1 foundation; 2 public presentation; 3 authentication/RBAC; 4 membership/documents/verification; 5 finance; 6 Career/Bursary; 7 communications/reports; 8 parity/Laravel retirement; 9 deployment/operations.

Recommended next task: Stage 1 only—create a parallel, non-routed plain-PHP foundation with Composer PSR-4 autoloading, configuration loader, PDO connection factory, request/response/router, middleware pipeline, native secure session and CSRF services, escaping/validation helpers, error/logging boundary, test harness, and a health check. Do not migrate domain routes or alter existing tables in that task.

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

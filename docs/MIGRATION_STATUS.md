# Plain-PHP Migration Status

## 2026-08-18 — Profession dropdown reference-data fix

- Root cause: the `professions` table existed, and the prepared active-only alphabetical repository query and profile template were correct, but the table had never been seeded. Before the fix it contained 0 total, 0 active, and 0 inactive rows.
- Added and applied idempotent additive patch `2026_08_18_seed_professions.sql`; the same 22-row reference list is present in `install.sql`. `INSERT IGNORE` relies on the unique profession name and never updates or deletes existing data. Applying the patch twice remained at 22 rows.
- After the fix: 22 total, 22 active, and 0 inactive professions. `/profile/edit` returned 200 with all profession options visible; a disposable member selected and saved Community Health Worker, `/profile` displayed it, and a subsequent edit selected the saved option.
- Added an explicit form warning/disabled selection when any required reference list is unavailable. Automated verification passes 156 checks, including ordering, active filtering, option/value/escaping structure, selection preservation, invalid-ID rejection, and patch safety/idempotency.
- The disposable HTTP member, profile, role, and audit data were removed; no test record remains.

## 2026-08-18 — Finance and membership activation workflow

- The private-document checkpoint `a8988a2` and dashboard follow-up `9e17e71` were verified pushed; origin `migration/plain-php-mvp` was at `9e17e71` before finance work.
- Applied additive `2026_08_18_create_finance_workflow.sql`, mirrored in `install.sql`: fee schedules, memberships, invoices/items, payments/allocations, refund requests/refunds, and finance events (26 application tables).
- Fee schedules are independent of placeholder fees, effective-dated and overlap-protected. Approval idempotently creates one membership; a positive effective fee creates one invoice, otherwise status is `awaiting_fee_configuration` with no zero invoice.
- Exact minor-unit calculations, transactional allocation, duplicate/overpayment rejection, append-only reversals, auditable refund transitions, random public references, member ownership, and finance/super-user authorization are enforced. Full settlement activates membership; reversal recalculates its status.
- Printable HTML invoices and receipts disclose manual recording. Historical reconciliation is dry-run by default.
- Deferred: gateway integrations, PDFs/certificates, CSV export, confirmed fees, taxes/proration, renewal/expiry rules, automated refunds, and public verification.

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

The current local target is the operator-created, empty `ishep_crm` database. `plain-php/database/install.sql` targets that exact database and is manual-only. It does not create, drop, recreate, truncate, or automatically import a database. The script and application contain no legacy database-name reference.

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

### 2026-08-18 — Database-name correction and manual installer

- Standardized the plain-PHP example configuration, installer, setup guide, and README instructions on `DB_DATABASE=ishep_crm`.
- Added a non-destructive manual SQL installer for the existing empty `ishep_crm` database and a phpMyAdmin walkthrough.
- Confirmed the installer does not create, drop, recreate, truncate, or automatically import the database and contains no legacy database-name reference.
- Did not connect to, select through a client, import into, or otherwise modify the local database.

### 2026-08-18 — Registration live-verification attempt

- Verified repository `C:\xampp\htdocs\ishep-crm`, remote `https://github.com/Msibi-TC/ishep-crm.git`, and branch `migration/plain-php-mvp`.
- Confirmed `plain-php/.env` exists, remains ignored and untracked, and loads `DB_DATABASE=ishep_crm` without exposing configuration values.
- Confirmed PDO MySQL is enabled and the required database environment keys are present; the configured password currently resolves empty or missing.
- `php plain-php/bin/test-db.php` failed safely because MySQL rejected the configured account with driver error 1045. Schema verification and database-backed registration tests remain blocked pending corrected local credentials.
- A fresh-cookie `GET /register` returned HTTP 200, while `/health` returned HTTP 503 because the database was unreachable. The earlier public error was associated with a logged `PDOException`; a stale authenticated session can cause layout authentication to attempt the same rejected connection.
- `php plain-php/tests/run.php` passed 25 static checks, and syntax validation passed across all non-vendor plain-PHP files.
- Confirmed application data access uses PDO prepared statements. No migration, import, schema change, registration write, cleanup, or other database mutation was performed.
- Added read-only `plain-php/bin/test-db.php` and `plain-php/bin/verify-schema.php` locally; they remain uncommitted until the complete live verification succeeds.
- Registration workflow changes, live disposable-account testing, documentation finalization, commit, and push remain pending the MySQL credential correction.

### 2026-08-18 — Authentication experience completed

- The local `.env` credential correction resolved MySQL error 1045; `php plain-php/bin/test-db.php` now passes against `ishep_crm` without exposing credentials.
- Schema verification passes with all 10 required tables, 4 roles, 22 permissions, 9 provinces, 3 membership types, exactly one `registered_user` role, and the required user membership column.
- The registration-domain mismatch was an absent `users.membership_type_id`. Applied the reviewed additive nullable column/index/foreign-key change and recorded it in `plain-php/database/patches/2026_08_18_add_user_membership_type.sql`; no existing row was deleted or overwritten.
- Registration now loads active membership types, validates the selected active record, normalizes email, enforces the centralized password policy, rejects duplicates safely, hashes with `password_hash()`, uses prepared PDO statements and a transaction, rolls back when role assignment fails, assigns only `registered_user`, regenerates the session, and redirects to the member dashboard.
- Added field-level accessible errors, focused error summary, safe old input, neutral/pass/fail password requirements, ARIA live feedback, confirmation matching, and keyboard-accessible show/hide controls for registration, login, and reset forms. Passwords are never repopulated or transmitted for live validation.
- Added authenticated-user redirects away from guest authentication routes while retaining POST+CSRF logout, environment-aware secure cookies, HttpOnly, SameSite=Lax, session regeneration, account-status enforcement, generic login errors, and throttling.
- Live disposable account `codex.authentication.test@example.test` registered successfully, normalized correctly, stored a verified non-plaintext hash and membership type, received only `registered_user`, logged in, accessed the member dashboard, logged out, and was redirected to login when revisiting the protected dashboard.
- Deleted exactly the disposable user after verification; its cascading test role link was removed and a follow-up count confirmed zero matching test users. The automated suite also removes its named integration users in `finally`.
- Verification results: database connection passed; schema/reference baseline passed; `php plain-php/tests/run.php` passed 40 checks; all non-vendor PHP files passed `php -l`; `node --check plain-php/public/assets/js/auth-password.js` passed; `git diff --check` passed.
- HTTP smoke results: `/`, `/register`, `/login`, `/forgot-password`, and `/health` returned 200; unauthenticated `/dashboard` returned 302; invalid CSRF returned 419. Registration and login both regenerated the session identifier.
- Added `docs/PROJECT_STRUCTURE.md`, active-runtime details near the top of `README.md`, read-only database verification commands, password UX assets/policy, membership repository/schema patch, authentication middleware, and expanded disposable integration tests.
- Remaining risk: perform a final manual screen-reader/mobile browser pass for announcement cadence, focus order, and visual presentation. Recommended next feature: implement the real member profile and member dashboard using the completed authentication foundation.

### 2026-08-18 — Member profile and functional dashboard

- Authentication checkpoint `0d7f1cd` was already pushed and synchronized on `origin/migration/plain-php-mvp`.
- Added authenticated profile display/edit/update routes. Ownership comes only from the session; POST updates require CSRF.
- Applied the reviewed additive `2026_08_18_create_member_profiles.sql` patch after confirming the table was absent. It stores telephone, province, profession, organisation, job title, optional biography, and timestamps separately from account credentials.
- Full name, read-only email, membership type, status, and joined timestamps remain sourced from `users`; members cannot change roles, permissions, email, or account status here.
- Added prepared repositories, a transactional update/audit service, allow-list validator, active-reference validation, safe errors, escaping, and centralized completion based on name, email, membership type, telephone, province, and profession. Optional fields do not reduce completion.
- Replaced the dashboard placeholder with truthful profile, completion, membership-application, document, and recent-login summaries.
- Disposable integration and HTTP users/professions plus related audit/profile/role data were removed. Verification passed: PDO connection, 11/11 schema tables, 60 automated checks, all non-vendor PHP syntax, HTTP status matrix, `git diff --check`, and private-file checks.
- Release target: commit `feat: add member profile and dashboard` pushed only to `origin/migration/plain-php-mvp`; the resulting hash and push confirmation are reported at handoff.
- Recommended next task: implement the membership-application workflow using the completed profile foundation.

### 2026-08-18 — End-to-end membership application workflow

- Started from clean pushed profile checkpoint `6c9415904fdc2a79b8c1d87349a633d58fc55926` on `migration/plain-php-mvp`.
- Added and applied the reviewed additive `2026_08_18_create_membership_applications.sql` only after confirming both target tables were absent. New installs receive the same application/event schema.
- Public references use `ISH-APP-YYYY-` plus 16 uppercase random hex characters; sequential IDs remain internal.
- Centralized transitions cover submission, withdrawal, review, approval, rejection, and explicit rejected correction. Approved and withdrawn rows are terminal; withdrawn members may create a new application.
- Submission requires an active authenticated registered user, membership permission, complete profile, active type, declaration, CSRF, transaction, timeline event, and audit entry.
- Administrator and super-user reviewers receive a bounded searchable/filterable queue, profile summary, timeline, separated public/private notes, and transactional decisions. Registered and finance users cannot review.
- Member and staff dashboards now use real application state/counts. Fees remain unconfirmed; documents, payments, activation, certificates, Careers, and Bursary workflows remain deferred.
- Schema verification covers 13 tables and the public-reference unique index. The integration suite passes 85 checks and removes named disposable data in `finally`.
- Release target: `feat: add membership application workflow` pushed only to `origin/migration/plain-php-mvp`; final verification, commit hash, and push state are reported at handoff.
- Recommended next stage: private document collection and verification after retention and file rules are approved.

### 2026-08-18 — Private document upload and verification

- Started from clean pushed application checkpoint `bcb4fb48121bef21031d27c952cb9b5944a499fd`.
- Applied additive `2026_08_18_create_member_documents.sql` after confirming all four targets were absent; schema verification now covers 17 tables and unique document references.
- Added configurable PDF/JPEG/PNG validation at 5 MB, actual MIME checks, extension matching, random private names, SHA-256, contained paths, authorized attachment downloads, Apache deny rules, no-store and nosniff headers.
- Members can upload, download, replace, and logically remove allowed states. Administrator and super-user reviewers can search, download, verify, and reject; finance and ordinary members cannot review.
- Centralized pending, verified, rejected, replaced, and removed transitions preserve replacement history and write events plus audits.
- `supporting_document` is optional because final requirements are absent. Submission is unchanged. Antivirus and retention expiry require production decisions.
- Automated tests pass 113 checks with generated harmless fixtures and finally-style cleanup. Final HTTP, syntax, secret, commit, and push results are reported at handoff.
- Release target: `feat: add private document verification workflow` pushed only to `origin/migration/plain-php-mvp`.
- Recommended next stage: approve production antivirus, retention, backup, and per-membership requirements before making documents mandatory.

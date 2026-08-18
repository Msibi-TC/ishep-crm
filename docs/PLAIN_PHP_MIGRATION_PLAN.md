# Laravel to Plain-PHP Migration Plan

**Audit date:** 2026-08-18

**Repository:** `Msibi-TC/ishep-crm`

**Scope:** Evidence-based migration design only; no runtime or schema changes

## Executive summary

The repository is a small Laravel 12 foundation, not a completed CRM. It has 18 application routes, six concrete controllers, seven models, 14 active application Blade views (plus the unused Laravel `welcome` view), seven migrations creating 16 tables, and 22 tests recorded as passing at the last integrated checkpoint. Implemented behavior comprises public presentation pages, registration, login/logout, password recovery, account-status checks, RBAC foundations, four dashboard pages, reference data, a staff-role console command, and audit-log storage. Membership applications, member profiles, document handling, payment operations, careers/bursary workflows, verification processing, reports, notices, and audit-log writing are not implemented.

The existing application can be migrated incrementally to a small front-controller application using PHP 8.3+, PDO, server-rendered templates, native secure sessions, and explicit security services. The 16 existing tables are structurally usable from PDO. Domain/reference/RBAC/user/audit data should be preserved. Laravel framework tables should initially remain in place and be retired only after data-independent verification confirms that the plain-PHP runtime no longer uses them. Laravel must remain operational until route-by-route parity gates pass.

## Audit evidence and documentation reviewed

No `AGENTS.md` files exist in the repository. The audit reviewed:

- `README.md`
- `docs/architecture.md`
- `docs/database-conventions.md`
- `docs/development-plan.md`
- `docs/PROJECT_STATUS.md`
- all tracked application, route, configuration, migration, seeder, factory, template, asset, and test files
- `composer.json`, `composer.lock`, `package.json`, `package-lock.json`, `vite.config.js`, `phpunit.xml`, `.env.example`, and Git history

Claims from `PROJECT_STATUS.md` were treated as historical evidence only and checked against code. The separate shared team repository was not accessed.

## Verified current stack

| Area | Verified state | Migration treatment |
|---|---|---|
| PHP | Constraint `^8.2`; local CLI 8.3.33 | Target PHP 8.3+; confirm hosting version |
| Framework | Laravel Framework 12.66.0 | Replace after parity, then remove |
| Composer | Local 2.10.2; PSR-4 autoloading | Retain for autoloading/PHPUnit and narrowly justified mail packages |
| Database | `.env.example` selects MySQL; InnoDB, `utf8mb4_unicode_ci`, UTC conventions | Preserve compatible schema/data; PDO MySQL |
| Frontend | Bootstrap 5.3.x; custom CSS; Bootstrap JS | Preserve compiled visual behavior; serve static assets |
| Build | Vite 7.3.6, Laravel Vite plugin 2.x, Tailwind plugin present but no Tailwind application design found | Build/copy final static assets, then remove Node/Vite production need |
| JavaScript | Axios and Bootstrap are installed; application JS only imports Bootstrap | Replace with minimal vanilla JS; remove unused Axios if confirmed |
| Tests | PHPUnit 11.5.56; tests force SQLite `:memory:` | Retain PHPUnit with a plain-PHP bootstrap and isolated test database |
| Mail | Laravel password broker/notification; local example uses log mailer | SMTP adapter and reset-message service |
| Session/cache/queue | Example: file session, file cache, sync queue | Native file sessions; application cache only if needed; synchronous work/outbox |
| Filesystem | Default local disk; public and S3 disks configured by framework defaults | Explicit private/public storage services; no current application upload use |
| Scheduler | No scheduled tasks | DirectAdmin cron only when a future job exists |
| External integrations | None found | Do not add without a business requirement |

### Dependency disposition

Production dependencies are Laravel and Tinker; both are removable after retirement. Development dependencies are Faker, Pail, Pint, Sail, Mockery, Collision, and PHPUnit. PHPUnit (and optionally Faker/Mockery) can remain for tests; Laravel-specific development tools can be removed after migration. Bootstrap is a frontend runtime asset but currently installed as a build dependency. Vite, the Laravel Vite plugin, Tailwind tooling, Concurrently, Axios (apparently unused), and Node/npm are build/development dependencies that can be removed after stable static assets and vanilla JS replace them.

Composer should remain: PSR-4 autoloading and PHPUnit are worthwhile and do not make production Laravel-dependent. Add a mail library only if a reviewed native SMTP implementation is not appropriate.

## Functionality inventory

| Capability | Status | Evidence / boundary |
|---|---|---|
| Landing, membership, careers, bursaries pages | Implemented presentation | Public controller and Blade views; three portal pages are informational placeholders |
| Public membership verification | Placeholder | GET form only; no POST route, query, or verification service |
| Registration | Implemented | Normalizes email, validates password/terms, transactionally creates active user and registered-user role |
| Login/logout | Implemented | Generic failure, 5-attempt throttle, account status, session regeneration/invalidation, last-login update |
| Password recovery | Implemented | Laravel password broker and notification; reset token table |
| Member dashboard | Implemented presentation | Authenticated static dashboard; no profile/domain records |
| Administrator/Finance/Super User dashboards | Implemented presentation/RBAC | Role middleware enforced; pages are placeholders |
| Permissions | Partially implemented | 22 seeded permissions and query helpers; route enforcement currently uses roles only |
| Reference data | Implemented storage | Provinces, professions, membership types; professions have no seeder |
| Audit logging | Partially implemented | Table/model exist; no application writer calls found |
| Role assignment | Implemented console operation | Existing user/role only; no web administration UI |
| Membership applications, documents, student eligibility | Missing | Permission names only; no routes/tables/services/views |
| Payments, refunds, subscriptions, certificates | Missing | Permission names and placeholder Finance page only |
| Career/Bursary workflows | Missing | Public placeholders and permission names only |
| Reports, notices, notifications | Missing | Permission names only |
| Uploads, PDFs, exports, APIs/payment gateway | Missing | No implementation found |
| Jobs/queues/scheduler | Configured framework foundation, unused | Framework tables/config exist; no job classes or schedule |

## Route inventory and replacement map

All application routes below are in `routes/web.php`. Laravel also registers framework endpoints (including `/up`); the last integrated `route:list` recorded 21 total routes, whereas 18 are application routes audited here.

| Method | URI / name | Handler, middleware, view/response | Entities | Status | Plain-PHP replacement |
|---|---|---|---|---|---|
| GET | `/` `home` | `PublicPageController::home`; public; `public.home` | None | Implemented | `PublicController::home`, `pages/public/home.php` |
| GET | `/membership` `membership` | public; `public.membership` | None | Placeholder content | Same route/template |
| GET | `/careers` `careers` | public; `public.careers` | None | Placeholder | Same route/template |
| GET | `/bursaries` `bursaries` | public; `public.bursaries` | None | Placeholder | Same route/template |
| GET | `/verify-membership` `verify.membership` | public; `public.verify-membership` | None | Placeholder form | Same GET; do not add processing until specified |
| GET | `/register` `register` | `RegisteredUserController::create`; guest; `auth.register` | None | Implemented | `RegistrationController::create` |
| POST | `/register` `register.store` | guest, CSRF; redirect | users, roles, user_roles | Implemented | validator + transaction + auth/session services |
| GET | `/login` `login` | `AuthenticatedSessionController::create`; guest; `auth.login` | None | Implemented | `SessionController::create` |
| POST | `/login` `login.store` | guest, CSRF; redirect | users | Implemented | rate limiter + user repository + session login |
| GET | `/forgot-password` `password.request` | guest; `auth.forgot-password` | None | Implemented | `PasswordResetController::requestForm` |
| POST | `/forgot-password` `password.email` | guest, CSRF; generic redirect | users, password_reset_tokens | Implemented | token repository + SMTP mail service |
| GET | `/reset-password/{token}` `password.reset` | guest; `auth.reset-password` | reset token input | Implemented | route parameter + reset template |
| POST | `/reset-password` `password.update` | guest, CSRF; redirect | users, password_reset_tokens | Implemented | token validation, password hash, transaction |
| POST | `/logout` `logout` | auth + active, CSRF; redirect | Session | Implemented | invalidate session/cookie + rotate CSRF |
| GET | `/dashboard` `dashboard` | auth + active; `dashboard` | users/roles for nav | Implemented presentation | `DashboardController::index` |
| GET | `/dashboard/administrator` `dashboard.administrator` | auth + active + administrator role | users, user_roles, roles | Placeholder | centralized authorization + template |
| GET | `/dashboard/finance` `dashboard.finance` | auth + active + finance role | same | Placeholder | centralized authorization + template |
| GET | `/dashboard/super-user` `dashboard.super-user` | auth + active + super_user role | same | Placeholder | centralized authorization + template |

Grouping: public routes are the first five; authentication routes are the next nine including logout; member/system support is `/dashboard`; administration, finance, and system support are the three staff dashboards. There are no implemented application routes for membership applications, reports, operational Career/Bursary portals, document handling, or verification processing.

## Controller, view, model, and supporting-code inventory

- Six concrete controllers: four authentication controllers, `DashboardController`, and `PublicPageController` (plus one empty abstract base controller).
- Three middleware: active-account, role, and permission enforcement.
- Two form requests: login and registration.
- Seven models: User, Role, Permission, Province, Profession, MembershipType, AuditLog.
- Fourteen active application views: four auth, four dashboards, five public pages, and one shared layout. `welcome.blade.php` is unused scaffold residue.
- No services, repositories, DTOs, policies, events, listeners, jobs, custom notifications/mailables, or HTTP APIs exist. One console command assigns roles. `AppServiceProvider` defines permission Gate behavior and `@role`/`@permission` Blade directives.
- Tests: 22 test methods across one unit and three feature classes. Authentication/RBAC coverage is substantive; visual layout, CSRF failure behavior, inactive-session eviction, reset expiry, concurrency, database portability, audit immutability, and all future domains lack dedicated coverage.

## Database inventory

Seven migrations create 16 tables. All use Laravel conventions but are accessible without Eloquent.

| Table | Purpose and key constraints/indexes | Laravel coupling | Recommendation |
|---|---|---|---|
| users | bigint PK; unique email; account-status index; nullable self-FKs created_by/updated_by | casts, automatic timestamps/hash | Preserve; map types explicitly in repository |
| password_reset_tokens | email PK, token, created_at | Laravel broker token format | Preserve during auth cutover; replace token generation/expiry atomically |
| sessions | string PK; nullable indexed user_id (not FK); last_activity index | serialized Laravel payload | Do not reuse payloads; allow Laravel sessions to expire, use separate native sessions |
| cache / cache_locks | string PK; expiration indexes | Laravel serialization/locking | Leave during parallel run; retire if unused |
| jobs / job_batches / failed_jobs | queue framework storage; unique failed UUID | Laravel payload formats | Leave untouched; retire only after confirming unused |
| roles | bigint PK; unique code; is_system index | timestamps/boolean casts | Preserve |
| permissions | bigint PK; unique code | timestamps | Preserve |
| role_permissions | FKs cascade; unique role/permission pair | pivot conventions only | Preserve |
| user_roles | FKs cascade; nullable assigner FK null-on-delete; unique user/role | pivot timestamp naming | Preserve |
| provinces | unique code/name; active index | boolean/timestamps | Preserve |
| professions | unique name; active index | boolean/timestamps | Preserve; currently unseeded |
| membership_types | unique code; decimal(12,2); billing period; student/active indexes | enum/decimal/boolean casts | Preserve; validate allowed strings explicitly |
| audit_logs | actor FK null-on-delete; action/entity/composite indexes; JSON snapshots; created_at only | JSON/datetime casts; model allows writes | Preserve; enforce append-only repository/database permissions where practical |

All 16 tables are compatible with MySQL/MariaDB and PDO. Confirm target MariaDB JSON behavior/version if MariaDB is selected. Numeric/boolean/date/JSON conversion currently supplied by Eloquent must become explicit. Preserve migration history during coexistence. The actual local schema was not queried in this audit because dependencies could not be restored; the last integrated project record says all seven migrations were applied to MySQL 8.0.46 in batches 1–2.

Future migrations should be numbered forward-only SQL files plus a small CLI runner and `schema_migrations(version, filename, checksum, applied_at)`. Acquire an advisory lock, verify checksums, execute one migration transaction where the DDL engine supports it, fail closed, back up before production changes, and require an explicit corrective forward migration instead of automatic destructive rollback.

## Security mapping

| Current Laravel control | Plain-PHP control and acceptance requirement |
|---|---|
| `Hash::make/check` and hashed cast | `password_hash(PASSWORD_DEFAULT)` / `password_verify`; rehash on login when needed |
| Session guard/regeneration/invalidation | Central session wrapper; strict mode; Secure/HttpOnly/SameSite cookies; regenerate on login and privilege change; delete session on logout |
| Web CSRF middleware | Per-session tokens, constant-time compare, POST-only state changes, token rotation policy |
| RateLimiter by normalized email/IP | Persistent/file-safe limiter with equivalent 5 attempts/60-second behavior and generic errors |
| Role middleware, Gate, Blade directives | Authorization service queried by middleware and templates; UI checks never replace server enforcement |
| AccountStatus enum/middleware | Allowed-value validator and every-request active-account guard |
| FormRequest validation | Central validator/request objects; normalize email before lookup/uniqueness checks |
| Eloquent/query builder | PDO prepared statements only; explicit transactions for registration/reset/RBAC writes |
| Blade escaped output | `htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')` helper; raw output allowlist only |
| Password broker | Random 32-byte single-use tokens, store only token hashes, expiry/rate limits, generic response, invalidate on use |
| Laravel errors/logging | Central exception handler; generic production pages; structured server logs without secrets/PII |
| Local filesystem defaults | Private storage outside document root, random server names, allowlisted size/MIME/extension, checksum, authorized streaming, no user-controlled paths |
| Audit model/table | Append-only audit service, actor/action/entity/IP metadata, redact secrets and sensitive document content |

Public verification must disclose only the minimum confirmed membership state and must use a non-enumerable identifier plus throttling. No current verification backend or upload workflow exists, so these are future requirements rather than parity claims.

## Target architecture

```text
public/
  index.php  .htaccess  assets/
src/
  Bootstrap/  Config/  Routing/
  Http/Controllers/  Http/Middleware/  Http/Requests/
  Domain/  Services/  Repositories/  Database/
  Auth/  Security/  Validation/
templates/
  layouts/  components/  pages/
storage/
  private/  logs/  cache/  sessions/
database/
  migrations/  seeders/
config/
bin/
tests/
docs/
```

`public/index.php` constructs a request, loads environment/configuration, builds a small explicit dependency container, dispatches the router through middleware, and emits a response. Controllers coordinate only; services own use cases and transactions; repositories contain prepared PDO statements; domain objects/enums express rules; templates receive prepared view data. A global exception boundary logs a correlation ID and emits environment-appropriate errors. Authentication, authorization, CSRF, validation, flash messages, mail, secure files, and logging are centralized services. CLI scripts under `bin/` share bootstrap/configuration for migrations and future cron tasks. Avoid dynamic service locators, active-record magic, or a home-grown general framework.

## Feature migration map and parity gates

| Feature | Existing components/data/tests | Plain-PHP components | Stage / acceptance criteria |
|---|---|---|---|
| Public shell/pages | 5 routes, PublicPageController, layout/public views, HomePage tests | public controller, layout/components/static assets | 2: same URLs/content/navigation/responsive rendering; page tests pass |
| Registration | route/controller/request; users/roles/user_roles; RBAC test | registration controller/service, validator, three repositories | 3: normalization, password policy, terms, fixed role, transaction and session regeneration match |
| Login/logout | session controller/request; users; auth tests | auth/rate-limit/session services | 3: generic errors, statuses, throttle, remember decision, regeneration/invalidation match |
| Password reset | two controllers/views; token/users tables; test | token repository, reset service, SMTP mail | 3: enumeration resistance, single-use expiry, hash update and mail test pass |
| Dashboard/RBAC | 4 routes/views, 3 middleware, role/permission models, tests | dashboard controller, auth/account/authorization middleware | 3: anonymous redirect, inactive denial, matching role allow/nonmatching 403 |
| Reference/audit foundations | 4 models/tables and seeders | repositories, enum/value validation, audit writer | 1/4: seeded data preserved; append-only audit added before sensitive new writes |
| Staff-role CLI | command and RBAC test | `bin/assign-role.php` use case | 3: existing-only, idempotent, normalized email, audited assignment |
| Portal placeholders | membership/career/bursary/verification views | templates only | 2: preserve placeholder truthfulness; do not invent workflows |

Each route stays served by Laravel until its replacement exists, uses the intended database safely, passes automated and manual responsive checks, preserves authentication/authorization/CSRF semantics, and meets the row’s criteria. Retirement requires an inventory showing no inbound route, cron, CLI, mail, asset, or data dependency on Laravel.

## Staged plan

| Stage | Goal / modules | DB effects | Verification and rollback | Entry → completion | Complexity / Git approach / risks |
|---|---|---|---|---|---|
| 0 | Audit, baseline, risks | None | Documents reviewed; revert docs commit | Clean main → approved plan | Low; this audit branch |
| 1 | Parallel bootstrap, router, config, PDO, sessions, CSRF, errors, test harness | Add only migration bookkeeping if approved | Unit/HTTP/security tests; route traffic remains Laravel | Stage 0 accepted → health page/test foundation | High; one feature branch, small commits; session/config risk |
| 2 | Public pages, shared layout, static assets | None | Snapshot/manual responsive checklist; switch routes back to Laravel | Foundation stable → five pages at parity | Medium; asset/layout commits; visual drift |
| 3 | Auth, reset, RBAC, statuses, dashboards, role CLI | Prefer existing tables unchanged | Full auth matrix and DB integration; instant route rollback, sessions isolated | Security services reviewed → all auth parity gates | High; separate auth/RBAC branches; lockout/token risks |
| 4 | Profiles, membership applications, documents, eligibility, review, verification | New approved domain tables only | Workflow/state/security/upload tests; feature flags and backup | Business/schema decisions approved → complete audited workflow | Very high; domain slices; requirements/privacy risk |
| 5 | Finance, subscriptions, payments/refunds/certificates/renewals | New financial ledger/domain tables | Reconciliation, idempotency, authorization, backup restore | Payment rules/provider approved → signed reconciliation | Very high; separate branches; financial/regulatory risk |
| 6 | Career and Bursary portals | New approved listing/application tables | Public/admin lifecycle and privacy tests | Requirements approved → full moderated workflows | High; one portal per branch |
| 7 | Notices, mail/notifications, reports | Outbox/report tables if justified | Delivery retry, access/export tests; disable cron/outbox consumer | Operational rules approved → observable retry/reporting | High; cron/PII risk |
| 8 | Parity and Laravel retirement | Drop nothing initially; archive plan separately | Full regression, data reconciliation, rollback deployment | All gates pass → no Laravel traffic/dependencies | High; retirement branch/commit; hidden dependency risk |
| 9 | HOSTAFRICA release | Controlled migrations/import | deployment smoke, backup and restore rehearsal; prior release package/database backup | Hosting confirmed → SSL/SMTP/cron/logs monitored | High; release tag; hosting limits/config drift |

Every stage requires a clean branch from current main, reviewable commits, documentation/test updates, no destructive schema command, and a rollback artifact. Database changes require backup, forward migration review, and restoration rehearsal appropriate to risk.

## Test and visual baseline

On 2026-08-18: `composer validate` passed; `npm.cmd run build` passed with Vite 7.3.6, 60 modules, CSS 230.85 kB and JS 129.30 kB; `git diff --check` passed before documentation edits. Tests explicitly use SQLite `:memory:`, so they are isolated from MySQL. Current `artisan test`, `route:list`, Pint, and `migrate:status` could not run because `vendor/` was absent and Composer archive installation lacked ZIP support; source fallback timed out. The latest integrated repository checkpoint (2026-08-17) records 22 passing tests/80 assertions, 21 total routes, Pint passing, and seven applied migrations. Those figures are historical, not claimed as freshly reproduced.

Representative baseline pages are the landing page, login, registration, forgot/reset password, generic/member dashboard, three staff dashboards, membership placeholder, Career placeholder, Bursary placeholder, and verification placeholder. Profile, application, upload, review, finance operations, and reports have no page to baseline. No screenshot/browser automation exists.

## HOSTAFRICA DirectAdmin assessment

The architecture is conventional shared-hosting PHP, but actual plan capabilities require confirmation. Require PHP 8.3+ (8.2 is the code constraint, 8.3 is preferred), PDO/PDO_MySQL, mbstring, openssl, fileinfo, JSON, tokenizer, ctype, filter, session, and preferably intl; confirm MariaDB/MySQL version, `mod_rewrite`, `AllowOverride`, cron, outbound SMTP, Composer/SSH availability, execution limits, storage quota, and selectable document root.

Set the domain document root to `public/`. If the account cannot do so, use a reviewed deployment layout that keeps `src`, `config`, `storage`, and vendor files above `public_html`; never expose configuration/private uploads. `.htaccess` must disable indexes, deny dotfiles/sensitive extensions, route non-files to `index.php`, preserve real assets, and enforce HTTPS without creating proxy loops. Keep production secrets in a non-public environment/config file with restrictive permissions; do not rely on editable values committed to Git.

Create a least-privileged database/user in DirectAdmin, import only reviewed schema/data through phpMyAdmin or CLI, verify `utf8mb4`/UTC, and run controlled migrations. Configure authenticated TLS SMTP through environment values. Cron should invoke CLI scripts with absolute paths and locking. Make `storage/private`, logs, sessions, and cache writable only to the account/PHP process. Enable SSL, redact/rotate logs, schedule database and private-file backups, keep an off-host copy, and test restoration. Deployment must include maintenance/health checks, versioned release package, migration record, smoke tests, and rollback to the prior package plus database restore only when schema compatibility requires it.

## Risk register

| Risk | Impact / mitigation |
|---|---|
| Aspirational permissions/status docs mistaken for features | Scope inflation; keep implemented/placeholder/missing labels and code evidence |
| Authentication regression | Account compromise/lockout; security test matrix, isolated sessions, staged route cutover |
| Laravel password-token/session formats reused incorrectly | Broken or unsafe sessions; do not share session payloads, deliberately transition reset tokens |
| Eloquent casts/timestamps lost | Data corruption; explicit repository hydration and UTC/decimal/JSON tests |
| Shared-host limitations unknown | Deployment failure; confirm plan capabilities before Stage 1/9 decisions |
| Incomplete business rules and zero fees | Incorrect domain/financial design; obtain approval before Stages 4–5 |
| Visual drift after Vite removal | User-facing regression; retain Bootstrap styles and page checklist/snapshots |
| Audit table is storage only | Missing accountability; implement centralized writer before sensitive workflows |
| Framework tables removed prematurely | Runtime/data loss; leave in place through coexistence and retire separately |
| Current PHP lacks ZIP support; XAMPP PHP is 8.0.30 | Repeatable setup failure; align XAMPP/CLI PHP 8.3 and required extensions |

## Assumptions and decisions requiring confirmation

- Confirm the actual HOSTAFRICA plan, PHP selector/extensions, document-root control, cron frequency, SMTP policy, and backup/restore facilities.
- Confirm PHP 8.3 as the production minimum and MySQL versus MariaDB/version.
- Confirm whether Composer is available on-host or dependencies must be built into a release artifact.
- Confirm membership/application lifecycle, profile fields, document types/retention, student eligibility, verification disclosure, and audit retention.
- Confirm role/permission governance, inactive/pending account behavior, email verification requirement, remember-me policy, and session lifetime.
- Confirm membership fees, billing periods, payment provider, refund/renewal/certificate rules before finance design.
- Confirm Career/Bursary workflows, notices, reporting/export requirements, POPIA responsibilities, retention, and recovery objectives.
- Decide whether unused framework tables remain archived indefinitely or are removed in a later separately approved migration.

## Laravel retirement gates

Laravel, Artisan, Blade, Vite, Node/npm, and Laravel-specific packages may be removed only after every implemented route/use case has a passing plain-PHP replacement; security and authorization matrices pass; database counts/invariants reconcile; visuals are accepted; SMTP/cron/private storage work in staging; backups and rollback are rehearsed; logs/health monitoring are live; no Laravel CLI/route/job/mail/session dependency remains; and a separately reviewed retirement change confirms no production data is dropped.

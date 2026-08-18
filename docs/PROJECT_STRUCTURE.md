# ISHEP CRM Project Structure

## Careers and Bursary domain

- `plain-php/database/patches/2026_08_18_create_careers_bursaries.sql`: six additive opportunity, application, and event tables, mirrored in `install.sql`.
- `plain-php/src/Repositories/PortalRepository.php`: prepared public filters, ownership-scoped applications, staff queues, and timelines.
- `plain-php/src/Services/PortalService.php` and `PortalStatus.php`: validation, transactions, permissions, publishing, submission, withdrawal, and review transitions.
- `plain-php/src/Http/Controllers/PortalController.php` and `PortalStaffMiddleware.php`: shared HTTP orchestration for both domains.
- `plain-php/templates/pages/portals/`: responsive public, member, and staff pages using escaped plain text.

## Plain-PHP finance domain

- `plain-php/database/patches/2026_08_18_create_finance_workflow.sql`: additive nine-table schema mirrored in `install.sql`.
- `plain-php/src/Repositories/FinanceRepository.php`: prepared queries and invoice recalculation.
- `plain-php/src/Services/FinanceService.php`: transactional fees, provisioning, payments, reversals, refunds, audit, and activation.
- `plain-php/src/Support/Money.php` and `PublicReference.php`: exact minor-unit money and random public IDs.
- `plain-php/src/Http/Controllers/FinanceController.php`, middleware, and `templates/pages/finance/`: ownership/role enforcement and responsive printable views.
- `plain-php/bin/reconcile-approved-memberships.php`: dry-run-first historical reconciliation.

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
| `plain-php/src/Services/` | Authentication, registration, profiles, membership-application lifecycle/review, password reset, authorization, and rate limiting |
| `plain-php/src/Security/` | Sessions, CSRF, and authoritative password policy |
| `plain-php/src/Validation/` | Server-side form validation |
| `plain-php/templates/` | Escaped server-rendered layouts and page templates |
| `plain-php/public/assets/` | Local Bootstrap, custom CSS, and minimal vanilla JavaScript |
| `plain-php/database/` | Reviewed manual installer and non-destructive SQL patches |
| `plain-php/tests/` | Static, unit-style, database-integration, and disposable-record checks |
| `plain-php/bin/` | Built-in-server router and read-only database/schema verification commands |
| `plain-php/storage/logs/` | Private structured application logs |
| `plain-php/storage/sessions/` | Native PHP session files and rate-limit state |
| `plain-php/storage/private/` | Denied, Git-ignored private documents served only through authorized controllers |

## Local configuration and safety

`plain-php/.env` contains local secrets and must remain ignored and untracked. Configure it from `plain-php/.env.example`; never paste its values into documentation or logs. Logs, session files, private uploads, Composer-generated `plain-php/vendor/`, database exports, and disposable test data must not be committed.

The database installer is manual-only. Existing installations apply the membership-type, profile, application, and `2026_08_18_create_member_documents.sql` additive patches only after confirming targets are absent. Document validation and the HOSTAFRICA-compatible private root are centralized in `plain-php/config/documents.php`. Never drop, recreate, truncate, or reset `ishep_crm` during startup or testing.

## Brand assets and presentation

- `plain-php/public/assets/images/ishep-logo.jpeg` is the single official logo source used by the shared header and printable invoice/receipt views.
- `plain-php/public/assets/css/app.css` owns the ISHEP palette and semantic component tokens. Red identifies primary actions and brand accents, charcoal provides structural contrast, gold is reserved for accents/focus, and semantic success/warning/danger/info colours retain their meanings.
- The shared layout provides a skip link, visible keyboard focus, responsive navigation/footer, intrinsic logo dimensions, reduced-motion handling, narrow-table containment, and grayscale-readable print rules.

## Staging operations

- `plain-php/bin/user-admin.php` and `UserAdministrationService` provide CLI-only audited account, role, and status operations; there is no public role-assignment interface.
- `plain-php/bin/build-release.ps1` creates an ignored, checksummed plain-PHP-only DirectAdmin archive and validates its runtime structure and private-file exclusions.
- `docs/DIRECTADMIN_STAGING_DEPLOYMENT.md`, `ROLE_TESTING_GUIDE.md`, and `STAGING_RELEASE_CHECKLIST.md` define deployment, rollback, access-control, acceptance, and cleanup procedures.

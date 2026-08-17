# Architecture overview

## Purpose

This project is the foundation for the ISHEP CRM and Portal Suite. It is intentionally structured to support modular growth without prematurely locking in a full membership or payment domain model.

## Layers

- Public web layer: landing pages and public portal routes
- Application layer: controllers, services, and domain logic
- Data layer: MySQL-backed persistence for future modules
- Asset layer: Bootstrap 5 and Laravel Vite pipeline

## Authentication and RBAC

- Laravel's session guard authenticates users and regenerates sessions after login.
- Account middleware permits only active accounts on protected routes.
- Roles and permissions use explicit many-to-many tables and server-side middleware.
- `registered_user` is the only self-registration role. `administrator`, `finance`, and `super_user` are staff roles assigned through authorised processes.
- Company, Individual, and Student are membership types. Applicant is an application state, not a role.
- Blade role/permission directives support navigation visibility, while middleware remains the enforcement boundary.
- Audit logs provide an append-only foundation for recording future sensitive operations.

## Membership domain

- `MemberProfile` is a one-to-one extension of authenticated identity; login credentials remain only on `users`.
- Users have historical applications and memberships. Services enforce at most one non-terminal application and one current membership transactionally.
- Application transitions and membership transitions append immutable history records.
- Company applications reference an owned organization; Individual and Student applications cannot create organization records.
- `MembershipApplicationService`, `StudentEligibilityService`, and `MembershipApprovalService` own workflow rules rather than controllers.
- Active membership numbers use the database-issued membership ID in `ISHEP-YYYY-000001` format, protected by a unique constraint.

## Document and verification security

- `SecureDocumentService` validates uploads, creates random filenames, stores SHA-256 checksums, and writes to the private local disk outside the public root.
- Policies restrict downloads to the owner or a reviewer with `memberships.review`; raw storage paths are hidden.
- Public verification is rate-limited and exposes only an approved display name, membership type/status, and renewal date.

## Naming conventions

- App\Services for domain workflows
- App\Repositories for data access abstractions
- App\Enums for status and taxonomy values
- App\Policies for authorization checks
- App\DTOs for data transfer objects

## Security posture

- Secrets remain in `.env` and are excluded from Git
- `.env.example` contains placeholders only
- MySQL is configured for `utf8mb4` and UTC timestamps
- Password hashes and remember/reset tokens are never rendered or logged
- Login attempts are throttled and invalid-credential responses are generic
- No payment, refund, subscription, careers, bursary, or final-certificate workflow is implemented yet

## Future direction

This foundation is designed for membership, careers, and bursary modules to be added incrementally with clean routing and a consistent presentation layer.

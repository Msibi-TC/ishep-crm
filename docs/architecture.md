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
- No payment, membership-application, careers, or bursary workflow is implemented yet

## Future direction

This foundation is designed for membership, careers, and bursary modules to be added incrementally with clean routing and a consistent presentation layer.

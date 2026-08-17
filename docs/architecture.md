# Architecture overview

## Purpose

This project is the foundation for the ISHEP CRM and Portal Suite. It is intentionally structured to support modular growth without prematurely locking in a full membership or payment domain model.

## Layers

- Public web layer: landing pages and public portal routes
- Application layer: controllers, services, and domain logic
- Data layer: MySQL-backed persistence for future modules
- Asset layer: Bootstrap 5 and Laravel Vite pipeline

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
- No payment or member-sensitive logic is implemented yet

## Future direction

This foundation is designed for membership, careers, and bursary modules to be added incrementally with clean routing and a consistent presentation layer.

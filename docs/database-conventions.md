# Database conventions

## Storage engine

Use MySQL with the InnoDB storage engine for all application tables.

## Character set and collation

- Character set: `utf8mb4`
- Collation: `utf8mb4_unicode_ci`

## Timezone

Use UTC for application and database timestamps. Locality-specific display should happen in the presentation layer, not in the storage model.

## Naming conventions

- Tables: snake_case plural names
- Columns: snake_case
- Foreign keys: `<table>_id`
- Timestamps: `created_at`, `updated_at`, `deleted_at` when needed

## Governance

All new domain models must follow the planned ISHEP conventions and be introduced incrementally as the CRM grows.

## Task 2 schema

- The existing `users` table is retained and extended additively with account status, last login, and nullable creator/updater references.
- RBAC tables are `roles`, `permissions`, `role_permissions`, and `user_roles` with unique pivot combinations and foreign keys.
- Reference tables are `provinces`, `professions`, and `membership_types`.
- `audit_logs` is append-only and intentionally has no `updated_at` or soft deletes.
- Portable string columns store values controlled by `AccountStatus`, `SystemRole`, and `BillingPeriod` PHP backed enums.
- Roles, permissions, pivots, and audit logs do not use soft deletes.
- Membership fees use `decimal(12,2)`. Initial membership fees are zero pending business confirmation; Student must remain zero unless requirements change.

## Task 3 schema

- Profile/organization: `member_profiles`, `organizations`.
- Application workflow: `membership_applications`, `student_eligibilities`, `application_status_history`, `application_queries`.
- Documents: `document_types`, `documents`; file bytes are never stored in MySQL.
- Membership: `memberships`, `membership_status_history`.
- Status history tables are append-only and have no `updated_at`; historical workflow records are not soft-deleted.
- Membership numbers are nullable until activation and uniquely constrained.
- Portable strings are controlled by PHP enums for profile, organization, application, eligibility, document, query, and membership statuses.

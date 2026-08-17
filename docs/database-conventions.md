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

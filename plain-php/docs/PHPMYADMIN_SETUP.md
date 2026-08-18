# phpMyAdmin Setup for the Plain-PHP MVP

The local database is the existing, empty `ishep_crm` database created by the operator. These instructions never drop, recreate, truncate, or automatically import into it.

## Before importing

1. Start MySQL from XAMPP and open phpMyAdmin.
2. Confirm that `ishep_crm` already exists and is the database you intend to initialize.
3. Back up the database if it is no longer empty. Stop if it contains data you did not expect.
4. Review [`../database/install.sql`](../database/install.sql). It starts with `USE ishep_crm`, creates only missing MVP tables, and inserts missing reference/RBAC rows with `INSERT IGNORE`.
5. Confirm the script contains no `DROP DATABASE`, `CREATE DATABASE`, `DROP TABLE`, `TRUNCATE`, or reference to any other database.

## Manual import

Import only when you are ready; the application and repository do not run the SQL automatically.

1. Click the `ishep_crm` database in phpMyAdmin.
2. Select **Import**.
3. Choose `plain-php/database/install.sql`.
4. Keep the character set as UTF-8 and run the import once.
5. Read the complete phpMyAdmin result before continuing. If any statement fails, do not repeatedly rerun or delete tables; record the error and review the schema safely.

The script creates the ten tables used directly by the MVP: `users`, `password_reset_tokens`, `roles`, `permissions`, `role_permissions`, `user_roles`, `provinces`, `professions`, `membership_types`, and `audit_logs`. It does not create Laravel session, cache, queue, or migration-bookkeeping tables, and it never creates a user account or administrator.

## Configure the application

Copy `plain-php/.env.example` to the ignored `plain-php/.env` and set locally:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ishep_crm
DB_USERNAME=your_local_database_user
DB_PASSWORD=your_local_database_password
```

Use a least-privileged local user where practical. Never paste credentials into documentation, Git, screenshots, or support messages.

## Safe verification

After the manual import, the following read-only statements can confirm the reference baseline:

```sql
SELECT COUNT(*) AS role_count FROM roles;
SELECT COUNT(*) AS permission_count FROM permissions;
SELECT COUNT(*) AS province_count FROM provinces;
SELECT COUNT(*) AS membership_type_count FROM membership_types;
```

Expected counts for a previously empty database are 4 roles, 22 permissions, 9 provinces, and 3 membership types. Then start the application and open `/health`; it should report the database as `reachable` without exposing connection details.

Do not use destructive phpMyAdmin actions such as Drop, Empty, or Truncate as part of this setup.

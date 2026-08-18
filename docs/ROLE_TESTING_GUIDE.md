# Role testing guide

All account administration is CLI-only and audited. `/register` is for members only and requires an active membership type; it assigns only `registered_user`. Staff accounts have `membership_type_id = NULL`, no member profile, and no membership application. Never add a staff role to a public registration request.

Create the three local disposable dashboard accounts. Each command generates a cryptographically secure temporary password and displays it exactly once. Copy each password immediately; passwords are not included in this guide, logs, Git, documentation, chat, or URLs.

```powershell
php plain-php/bin/create-staff-user.php --name="Test Administrator" --email="administrator.test@example.test" --role=administrator
php plain-php/bin/create-staff-user.php --name="Test Finance" --email="finance.test@example.test" --role=finance
php plain-php/bin/create-staff-user.php --name="Test Super User" --email="superuser.test@example.test" --role=super_user
php plain-php/bin/list-user-roles.php --email=administrator.test@example.test
php plain-php/bin/list-user-roles.php --email=finance.test@example.test
php plain-php/bin/list-user-roles.php --email=superuser.test@example.test
```

The first command is expected to refuse the existing `administrator.test@example.test` member account and must not be forced. For the disposable administrator dashboard test, use `administrator.dashboard.test@example.test` with the same command and `--name="Test Administrator Dashboard"`.

Use `--dry-run` before provisioning to validate name, email, and role without writes. The optional forced-password-reset flag is intentionally not provided because this application has no safe forced-change workflow.

If a displayed password is lost, reset only a disposable staff account by piping a new password through standard input:

```powershell
Read-Host 'New temporary password' -AsSecureString | ForEach-Object { [Net.NetworkCredential]::new('', $_).Password } | php plain-php/bin/reset-staff-password.php --email=finance.test@example.test
```

```powershell
$temporary = Read-Host 'Temporary password' -AsSecureString
$plain = [Net.NetworkCredential]::new('', $temporary).Password
$plain | php plain-php/bin/user-admin.php create --name="Preview Member" --email=member.dashboard.test@example.test --membership-type=1 --password-stdin
$plain = $null
php plain-php/bin/user-admin.php assign-role --email=administrator.dashboard.test@example.test --role=administrator
php plain-php/bin/user-admin.php list-roles --email=administrator.dashboard.test@example.test
php plain-php/bin/user-admin.php remove-role --email=administrator.dashboard.test@example.test --role=administrator --confirm
php plain-php/bin/user-admin.php set-status --email=member.dashboard.test@example.test --status=suspended --confirm
php plain-php/bin/user-admin.php set-status --email=member.dashboard.test@example.test --status=active
```

Staff accounts receive exactly the requested staff role. They do not receive `registered_user`, a membership type, a member profile, or a membership application.

| Role | Must allow | Must deny |
|---|---|---|
| registered_user | `/dashboard`, profile, membership application/documents, own finance, public and own portal applications | all `/admin/*`, finance staff mutations, role management |
| administrator | `/dashboard/administrator`, membership/document review, Career/Bursary management and review | finance staff routes and role assignment UI (none exists) |
| finance | `/dashboard/finance`, fees, invoices, payments, reversals, refunds | membership/document review and portal moderation |
| super_user | super-user dashboard and every approved staff route through normal middleware | no authorization bypass |

For each role, use a separate private browser profile: log in, confirm the role dashboard, request every allowed and denied URL directly, confirm denied authenticated requests return 403, submit a harmless form with an invalid CSRF token and confirm 419, log out, and confirm protected URLs redirect to login. Staff access to `/membership/application`, `/profile/edit`, `/membership/application/documents`, and member finance routes must redirect to `/dashboard`; staff must not see member onboarding actions. Suspend the account and confirm the next protected request logs it out. Repeat navigation checks at 320, 375, 768, 1024, and 1440px.

After review, inspect first with `php plain-php/bin/remove-test-user.php --email=...`. The command is dry-run by default; add `--apply` only after reviewing the displayed account. It accepts clearly marked `Test ...` accounts at `@example.test`, refuses unmarked accounts unless `--confirm-non-test` is explicitly supplied, and never removes the final active super-user.

Automated preview-data seeding is intentionally deferred: opportunities can safely be created through the existing administrator forms, while a generic cleanup command could delete manager-entered records. Label manual examples with `PREVIEW -`, avoid demonstration payments, and remove them through reviewed staff/database procedures after acceptance.

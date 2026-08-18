# Staging release checklist

- [ ] Correct repository, `migration/plain-php-mvp` branch, reviewed commit, and clean Git status
- [ ] Database connection, schema verification, full tests, PHP/JavaScript/PowerShell checks pass
- [ ] Official logo and red/charcoal/gold branding present; no obsolete primary-blue theme
- [ ] Production configuration has no localhost URL, Windows path, debug output, or default secret
- [ ] Release contains no `.env`, passwords, credentials, logs, sessions, uploads, private data, database export, fixtures, or legacy Laravel/Node files
- [ ] PHP 8.3+ and PDO, pdo_mysql, mbstring, openssl, fileinfo, session, json, filter enabled
- [ ] Document root points only to `ishep-preview/public`; Apache rewrites and security headers work
- [ ] HTTPS/Let's Encrypt enabled before sharing
- [ ] Empty prefixed database/user created and `install.sql` imported through phpMyAdmin
- [ ] `.env` created server-side with staging values and random application secret
- [ ] Storage paths are outside public and writable without 777
- [ ] DirectAdmin password protection enabled; noindex header and robots.txt verified
- [ ] Four role accounts created through CLI; exact roles and direct-URL authorization tested
- [ ] Public routes, 403/404/405/419/500 behavior, session isolation, logout, suspension, and CSRF tested
- [ ] Mobile layout at 320/375/768/1024/1440 and invoice/receipt print preview checked
- [ ] Release ZIP checksum verified and previous release/database backed up
- [ ] Manager HTTPS link and access credentials sent through separate channels
- [ ] Preview credentials rotated or accounts removed after review

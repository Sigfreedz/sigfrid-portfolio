# Sigfrid Portfolio

Personal portfolio and Playground built with PHP, MySQL, JavaScript, and CSS.

## Local development

1. Start Apache and MySQL/MariaDB in XAMPP.
2. Create the `sigfrid_portfolio` database and import [`database/portfolio.sql`](database/portfolio.sql).
3. Copy `.env.example` to `.env` for reference. XAMPP currently uses the local defaults in [`php/db.php`](php/db.php); the application does not load `.env` automatically.
4. Open `http://localhost/portfolio/`.

The Playground is available at `http://localhost/portfolio/playground.php`.

## Deployment target

The production target is Azure App Service on Linux with Azure Database for MySQL Flexible Server. GitHub is the source repository and deployment trigger. Production database values must be configured as Azure App Service Application settings, not committed to this repository:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `DB_SSL_CA` (when required by the selected Azure MySQL configuration)
- `DB_SSL_VERIFY`

See [`plan.md`](plan.md) for the repository, Azure provisioning, deployment, and verification gates.

## Validation

Run PHP syntax checks before publishing:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }
```

Do not commit `.env`, production credentials, private keys, local logs, or database dumps containing secrets.

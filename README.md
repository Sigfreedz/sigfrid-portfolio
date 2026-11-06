# Sigfrid Portfolio

Personal portfolio and Playground built with PHP, MySQL, JavaScript, and CSS.

**🌐 Live Site:** https://sigfrid.freehosting.dev/

## Features

- **Responsive Design**: Nord-themed glassmorphism with dark/light mode toggle
- **Dynamic Content**: Skills, projects, and radar chart loaded from MySQL database
- **Interactive Playground**: PHP-converted Java projects including playable Lucky Nine card game
- **Education Timeline**: Visual representation of academic journey
- **Tech Stack Showcase**: 20+ technologies with skill level indicators

## Local Development

1. Start Apache and MySQL/MariaDB in XAMPP.
2. Create the `sigfrid_portfolio` database and import [`database/portfolio.sql`](database/portfolio.sql).
3. Copy `.env.example` to `.env` for reference. XAMPP currently uses the local defaults in [`php/db.php`](php/db.php); the application does not load `.env` automatically.
4. Open `http://localhost/portfolio/`.

The Playground is available at `http://localhost/portfolio/playground.php`.

## Deployment

**Current Platform:** InfinityFree (PHP 8.4, MariaDB 11.4)

The site is deployed via FTP to InfinityFree shared hosting. GitHub remains the source repository. Production database credentials must never be committed to this repository:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

**Deployment Steps:**
1. Upload runtime files via FTP to `htdocs/` (excludes `.git/`, `database/`, Java source files)
2. Update `php/db.php` with production database credentials
3. Import `database/portfolio.sql` via phpMyAdmin in InfinityFree control panel
4. Verify all endpoints return data: `/php/getSkills.php`, `/php/getStack.php`, `/php/getRadar.php`, `/php/getProjects.php`

See [`plan.md`](plan.md) for detailed deployment history and verification checklist.

## Project Structure

```
portfolio/
├── index.php              # Main landing page
├── playground.php         # Interactive project showcase
├── css/
│   └── style.css         # Nord theme with glassmorphism
├── js/
│   └── main.js           # Async data loading and theme toggle
├── php/
│   ├── db.php            # Environment-aware database connection
│   ├── getSkills.php     # Tech stack JSON endpoint
│   ├── getStack.php      # Full stack page endpoint
│   ├── getRadar.php      # Radar chart data endpoint
│   ├── getProjects.php   # Featured projects endpoint
│   └── playground/       # PHP implementations of Java projects
│       ├── DavidStar.php
│       ├── NumberedTriangle.php
│       ├── LootMaster.php
│       ├── LuckyNine.php # Playable card game with virtual players
│       └── dispatcher.php
└── database/
    └── portfolio.sql     # Complete schema with seed data
```

## Technologies

- **Frontend**: HTML5, CSS3 (Nord theme, glassmorphism), JavaScript (async/await, Fetch API)
- **Backend**: PHP 8.2+ (PDO, JSON endpoints)
- **Database**: MySQL/MariaDB
- **Deployment**: InfinityFree (PHP 8.4, MariaDB 11.4)
- **Development**: XAMPP, Git

## Validation

Run PHP syntax checks before publishing:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }
```

Do not commit `.env`, production credentials, private keys, local logs, or database dumps containing secrets.

## License

Personal portfolio project by Sigfrid Barrera.

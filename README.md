# CRM

Laravel 13 CRM with Blade, Tailwind CSS, Vite, SQLite, Fortify, and Spatie permissions.

## Prerequisites

- PHP 8.3+
- Composer
- Node.js and npm
- SQLite support (`pdo_sqlite`)

## Installation

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`.

## Demo data

Demo data is seeded automatically in `local`, `development`, and `testing` environments. Re-running `php artisan db:seed` is safe and does not duplicate demo accounts, meeting types, customers, or targets.

Development-only demo accounts (password `password`):

- `admin@demo.test`
- `manager@demo.test`
- `analyst@demo.test`
- `rep1@demo.test` through `rep8@demo.test`

Do not use these credentials in production.

## Commands

```bash
composer dump-autoload
php artisan package:discover
php artisan route:list
php artisan migrate:status
php artisan test
npm run build
```

## Notes

- SQLite is used by default.
- Do not run `migrate:fresh` or `db:wipe` against a database you need to preserve.
- Build assets before deploying or testing frontend changes.

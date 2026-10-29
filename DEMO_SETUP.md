# CRM demo setup

This project includes development-only sample data for exploring the CRM UI and workflows.

## Install

1. Copy `.env.example` to `.env` and configure your local database and mail settings.
2. Install PHP dependencies with `composer install`.
3. Install frontend dependencies with `npm install`.
4. Generate an application key with `php artisan key:generate`.
5. Run migrations and seed the local demo data with `php artisan migrate --seed`.
6. Build frontend assets with `npm run build`.
7. Start the app with `php artisan serve`.

The `DatabaseSeeder` runs `DemoDataSeeder` only in `local`, `development`, or `testing` environments. It creates sample users, leads, tasks, communications, targets, customers, and meeting types. Re-running the demo seeder is idempotent for demo accounts, meeting types, customers, and targets; lead/task/communication sample records are only created when the leads table is empty.

## Local demo accounts

The current user factory sets the development password to `password`. The demo seeder creates these accounts:

- `admin@demo.test`
- `manager@demo.test`
- `analyst@demo.test`
- Sales-representative accounts: `rep1@demo.test` through `rep8@demo.test`.

Use these credentials only on a local development database. Change passwords before any shared or deployed environment. Do not enable demo seeding in production and never use fake demo accounts as real customer accounts.

## Safety

- Never commit `.env`, API keys, credentials, or a database containing real customer data.
- Do not run `migrate:fresh` or `db:wipe` against a database you need to preserve.
- Public homepage KPI tiles are explicitly illustrative preview values; authenticated CRM metrics should come from actual database queries.

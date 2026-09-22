# Lumina — Wedding Photography CRM

SaaS platform for studios: public website, lead CRM, bookings, payments, files, galleries, and a client portal.

## Demo

URL: https://demo.vedmint.com

| Role | Email | Password |
| --- | --- | --- |
| Super Admin | superadmin@vedmint.com | password |
| Studio Admin | admin@demo.vedmint.com | password |
| Manager | manager@demo.vedmint.com | password |
| Photographer | photographer@demo.vedmint.com | password |
| Videographer | videographer@demo.vedmint.com | password |
| Editor | editor@demo.vedmint.com | password |
| Client | client@demo.vedmint.com | password |

Database: SQLite at `database/database.sqlite`.

## Local setup

```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run build
php artisan storage:link
php artisan queue:work
```

Scheduler: `* * * * * php artisan schedule:run`

Tests: `php artisan test`

## External credentials (optional)

WhatsApp, Stripe/Razorpay, Google Cloud Storage, and Google Drive use provider adapters. Without secrets, the app logs or skips those actions instead of faking success.

See `docs/GCP.md`.

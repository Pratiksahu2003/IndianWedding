# GCP production architecture

```
User → Cloud Load Balancer / CDN → Cloud Run (app + queue worker + scheduler)
                                 → Cloud SQL (MySQL 8)
                                 → Memorystore Redis
                                 → Cloud Storage (private media)
                                 → Secret Manager
```

This demo host uses SQLite + local disk. For GCP:

1. Build the Dockerfile and deploy to Cloud Run.
2. Point `DB_*` at Cloud SQL via the Cloud SQL Auth Proxy / Unix socket.
3. Set `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`, `SESSION_DRIVER=redis`.
4. Install `league/flysystem-google-cloud-storage`, set `MEDIA_DISK=gcs`, `GOOGLE_CLOUD_STORAGE_BUCKET`, and a service account in Secret Manager.
5. Run a second Cloud Run service or job for `php artisan queue:work` and Cloud Scheduler for `php artisan schedule:run`.
6. Health check: `GET /health` (no internals) and Laravel `/up`.
7. Backups: Cloud SQL automated backups + GCS object versioning. Restore: create a new instance from backup, swap connection secrets, replay queue if needed.

Never commit `APP_KEY`, SQL passwords, payment secrets, or GCS JSON keys.

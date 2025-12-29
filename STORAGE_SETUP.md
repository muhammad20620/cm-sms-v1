# Storage setup (uploads)

This project stores uploads (e.g. **logos**) on Laravel's **`public` filesystem disk**:

- Files are written to: `storage/app/public/...`
- Files are served from: `public/storage/...` (symlink)

## One-time setup

Create the symlink:

```bash
php artisan storage:link
```

Ensure storage is writable by the web server user:

- `storage/`
- `bootstrap/cache/`

## Why this avoids the “Unable to write in public/…” error

On many hosts (including Bitnami images), the web server **cannot write directly into `public/`**.
Laravel’s storage directory is the intended writable location for runtime uploads.



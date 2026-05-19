#!/bin/bash

cd /var/www

echo "=== Starting Fawatiry ==="

# Generate app key if missing
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set!"
fi

# Clear old caches
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Run migrations (warn but don't stop if it fails)
echo "Running migrations..."
php artisan migrate --force --no-interaction 2>&1 || echo "WARNING: Migration failed - check DB_URL"

# Seed database (runs once — will be removed after first deploy)
php artisan db:seed --force --no-interaction 2>&1 || echo "WARNING: Seeder failed"

# Storage symlink
php artisan storage:link --force 2>/dev/null || true

# Cache for performance
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache  2>/dev/null || true

echo "=== Starting nginx + php-fpm ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

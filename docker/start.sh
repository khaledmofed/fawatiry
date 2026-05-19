#!/bin/bash
set -e

cd /var/www

# Generate app key if missing
php artisan key:generate --no-interaction --force 2>/dev/null || true

# Run migrations
php artisan migrate --force --no-interaction

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# Cache config & routes for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start services
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

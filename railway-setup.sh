#!/bin/bash
# Railway deployment setup script for Laravel

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Create storage link
php artisan storage:link || true

# Cache configuration
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run migrations
php artisan migrate --force || true

# Start the server
exec php artisan serve --host=0.0.0.0 --port=$PORT


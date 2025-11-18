#!/bin/bash
# Render build script for Laravel

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Create storage link
php artisan storage:link || true

# Cache configuration (optional, can help with performance)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true


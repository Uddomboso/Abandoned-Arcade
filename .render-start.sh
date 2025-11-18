#!/bin/bash
# Render start script for Laravel

# Run migrations
php artisan migrate --force || true

# Start the server
php artisan serve --host=0.0.0.0 --port=$PORT


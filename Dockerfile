# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets
RUN npm ci && \
    NODE_ENV=production npm run build && \
    echo "=== Build verification ===" && \
    ls -la public/build/ && \
    echo "=== Manifest check ===" && \
    test -f public/build/.vite/manifest.json && echo "Manifest exists" || echo "WARNING: Manifest not found"

# Create storage link
RUN php artisan storage:link || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/public/build || true

# Configure Apache
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        Options -Indexes\n\
    </Directory>\n\
    <Directory /var/www/html/public/games>\n\
        Options -Indexes\n\
        AllowOverride All\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Expose port (Railway will provide PORT via environment variable)
EXPOSE 80

# Start command - use php artisan serve for Railway compatibility
# Railway provides PORT environment variable dynamically
# Clear config cache to ensure fresh environment variables are loaded
CMD sh -c "php artisan config:clear && php artisan cache:clear && php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=\${PORT:-80}"


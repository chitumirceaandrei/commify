#!/bin/sh

set -e

# Ensure the storage and bootstrap/cache directories are writable
echo "Setting permissions for storage and cache..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install Composer dependencies if vendor is missing
if [ ! -d /var/www/html/vendor ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# Check if the .env file exists, and if not, copy from example
if [ ! -f .env ]; then
    echo "Copying .env.example to .env..."
    cp .env.example .env
fi

# Generate the application key if not already set
if [ -z "$(grep 'APP_KEY=' .env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Run database seeders
echo "Running seeders..."
php artisan db:seed --force

# Start Nginx and PHP-FPM
echo "Starting Nginx and PHP-FPM..."
nginx && php-fpm

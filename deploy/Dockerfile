# deploy/Dockerfile

# Stage 1: Build stage
FROM php:8.3-fpm-alpine as build

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    zip \
    libzip-dev \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    nodejs \
    npm \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd

# Install Composer
COPY --from=composer:2.7.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Install PHP and Node.js dependencies
RUN composer install --no-dev --prefer-dist --optimize-autoloader \
    && npm install \
    && npm run build

# Stage 2: Production stage
FROM php:8.3-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    zip \
    libzip-dev \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    nginx \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd

# Copy Composer binary from the build stage
COPY --from=composer:2.7.6 /usr/bin/composer /usr/bin/composer

# Copy application files and vendor directory from the build stage
COPY --from=build /var/www/html /var/www/html
COPY ./deploy/nginx.conf /etc/nginx/http.d/default.conf
COPY ./deploy/php.ini "$PHP_INI_DIR/conf.d/app.ini"

WORKDIR /var/www/html

# Entrypoint script
COPY ./deploy/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Add a volume for persistent storage
VOLUME ["/var/www/html/storage"]

CMD ["docker-entrypoint.sh"]

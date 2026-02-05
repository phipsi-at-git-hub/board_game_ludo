# PHP-FPM Base Image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy correct php.ini depending on environment
ARG APP_ENV=dev
COPY docker/php.ini.${APP_ENV} /usr/local/etc/php/conf.d/custom.ini

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader

# Copy rest of the application
COPY . .

# Ensure permissions (optional, useful if mounted volumes)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port (optional)
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]

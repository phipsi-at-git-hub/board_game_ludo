# Base PHP-FPM Image
FROM php:8.3-fpm

# PHP Extensions
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev \
    && docker-php-ext-install pdo pdo_mysql

# Composer install
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Working tree
WORKDIR /var/www/html

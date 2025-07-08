FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /var/www/html

# Copy project
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=ext-intl --ignore-platform-req=ext-gd

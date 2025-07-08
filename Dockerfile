FROM node:20 as assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.3-fpm

# Install dependencies including Node.js and npm
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev \
    nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

COPY --from=assets /app/public/build /var/www/html/public/build

# Set working dir
WORKDIR /var/www/html

# Copy project
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=ext-intl --ignore-platform-req=ext-gd

# Install npm dependencies for development (including devDependencies)
RUN npm install

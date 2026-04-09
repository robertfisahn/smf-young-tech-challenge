FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    curl \
    tesseract-ocr \
    tesseract-ocr-data-pol \
    tesseract-ocr-data-eng \
    sqlite-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite gd zip opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN mkdir -p \
    storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    database

RUN chmod -R 775 storage bootstrap/cache database

RUN composer install --no-interaction --prefer-dist --no-dev

EXPOSE 8000
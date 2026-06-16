FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && apt-get update && apt-get install -y --no-install-recommends \
        unzip \
        libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress

COPY . .

RUN a2enmod rewrite

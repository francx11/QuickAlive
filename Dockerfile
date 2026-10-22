# 8.4.27+ corrige CVE-2026-109618 y CVE-2026-109619 (RCE sin autenticación). No bajar de aquí.
FROM php:8.4.27-apache

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

COPY docker/php/security.ini /usr/local/etc/php/conf.d/zz-security.ini

RUN a2enmod rewrite

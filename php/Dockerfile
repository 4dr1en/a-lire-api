FROM php:8.1-fpm

RUN apt-get update \
    && apt-get install -y zlib1g-dev g++ libicu-dev zip libzip-dev \
    && docker-php-ext-install intl opcache \
    && pecl install apcu \
    && docker-php-ext-enable apcu opcache \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

# Install dependencies for using postgresql
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

WORKDIR /var/www/html

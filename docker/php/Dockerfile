FROM php:8.2-fpm-alpine

# Extensiones necesarias
RUN apk add --no-cache \
        libzip-dev \
        libsodium-dev \
        oniguruma-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        sodium \
        mbstring \
    && apk del $PHPIZE_DEPS

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

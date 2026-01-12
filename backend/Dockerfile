FROM php:8.3-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash git curl unzip icu-dev libzip-dev oniguruma-dev postgresql-dev \
    && docker-php-ext-install intl zip pdo_pgsql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# RoadRunner binary will be installed via composer (spiral/roadrunner)
EXPOSE 8000

CMD ["sh", "-lc", "composer install || true; php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000 --watch"]
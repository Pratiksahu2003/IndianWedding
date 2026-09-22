FROM php:8.3-fpm-alpine

RUN apk add --no-cache git unzip icu-dev libzip-dev sqlite-dev oniguruma-dev \
    && docker-php-ext-install intl pdo pdo_mysql pdo_sqlite opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader \
    && mkdir -p storage/logs storage/framework/{cache,sessions,views} database \
    && chown -R www-data:www-data storage bootstrap/cache database

USER www-data
EXPOSE 8080
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]

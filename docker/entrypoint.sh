#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data storage bootstrap/cache || true
fi

if [ -f artisan ]; then
    php artisan storage:link --force >/dev/null 2>&1 || true

    if [ "${RUN_MIGRATIONS:-false}" = "true" ] && { [ "$#" -eq 0 ] || [ "$1" = "php-fpm" ]; }; then
        php artisan migrate --force
    fi

    if [ "${APP_ENV:-production}" = "production" ]; then
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan event:cache >/dev/null 2>&1 || true
    fi
fi

if [ "$#" -eq 0 ] || [ "$1" = "php-fpm" ]; then
    exec php-fpm
fi

exec "$@"

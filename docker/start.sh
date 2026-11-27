#!/bin/bash
set -e

# Render tells us which port to listen on (default 10000)
PORT="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

cd /var/www/html
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
php artisan storage:link || true
php artisan migrate --force
chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground

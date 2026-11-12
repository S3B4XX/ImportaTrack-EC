#!/bin/sh
set -e

# Render indica el puerto en la variable PORT (por defecto 10000).
PORT="${PORT:-10000}"
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migraciones + datos iniciales (solo si la base está vacía)
php artisan app:bootstrap

exec apache2-foreground

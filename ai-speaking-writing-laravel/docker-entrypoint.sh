#!/bin/bash
set -e

echo "Waiting for MySQL to be ready..."
until php -r "try { new PDO('mysql:host=mysql;dbname=iclc_db', 'iclc_user', 'password'); echo 'MySQL is ready!\n'; exit(0); } catch (PDOException \$e) { exit(1); }" 2>/dev/null; do
  echo "Waiting for MySQL..."
  sleep 2
done

cd /var/www/html

echo "Installing Composer dependencies..."
if [ ! -d "vendor" ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

echo "Generating application key..."
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
  php artisan key:generate --force || true
fi

echo "Running migrations and seeders..."
php artisan migrate --force || php artisan migrate:fresh --seed --force

echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8000


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

echo "Checking database status..."
# Kiểm tra xem database đã có bảng chưa
TABLES_EXIST=$(php artisan tinker --execute="echo DB::connection()->getDatabaseName(); exit(DB::select('SHOW TABLES') ? 0 : 1);" 2>/dev/null || echo "0")

if [ "$TABLES_EXIST" = "0" ] || ! php -r "try { \$pdo = new PDO('mysql:host=mysql;dbname=iclc_db', 'iclc_user', 'password'); \$result = \$pdo->query('SHOW TABLES'); exit(\$result->rowCount() > 0 ? 0 : 1); } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
  echo "First time setup: Running fresh migrations with seeders..."
  php artisan migrate:fresh --seed --force
else
  echo "Database exists: Running migrations only (no seed)..."
  php artisan migrate --force
fi

echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8000


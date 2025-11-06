#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Wait for MySQL to be ready with retry logic
wait_for_mysql() {
    log_info "Waiting for MySQL to be ready..."
    local max_attempts=30
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if php -r "try { 
            \$pdo = new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); 
            \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            \$pdo->query('SELECT 1');
            exit(0); 
        } catch (PDOException \$e) { 
            exit(1); 
        }" 2>/dev/null; then
            log_info "MySQL is ready!"
            return 0
        fi
        
        if [ $attempt -eq $max_attempts ]; then
            log_error "MySQL connection failed after $max_attempts attempts"
            exit 1
        fi
        
        log_warn "Waiting for MySQL... (attempt $attempt/$max_attempts)"
        sleep 2
        attempt=$((attempt + 1))
    done
}

wait_for_mysql

cd /var/www/html

# Install Composer dependencies
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    log_info "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
else
    log_info "Composer dependencies already installed"
fi

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    log_info "Generating application key..."
    php artisan key:generate --force || true
fi

# Optimize Laravel for production
log_info "Optimizing Laravel..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Check if database has tables
DB_HAS_TABLES=false
if php -r "
try { 
    \$pdo = new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); 
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$result = \$pdo->query('SHOW TABLES'); 
    exit(\$result->rowCount() > 0 ? 0 : 1); 
} catch (Exception \$e) { 
    exit(1); 
}" 2>/dev/null; then
    DB_HAS_TABLES=true
fi

# Run migrations
if [ "$DB_HAS_TABLES" = false ]; then
    log_info "First time setup: Running fresh migrations with seeders..."
    php artisan migrate:fresh --seed --force
else
    log_info "Database exists: Running migrations only..."
    php artisan migrate --force || {
        log_warn "Migration failed, trying fresh..."
        php artisan migrate:fresh --seed --force
    }
fi

# Create storage link if not exists
if [ ! -L "public/storage" ]; then
    log_info "Creating storage symbolic link..."
    php artisan storage:link || true
fi

# Set proper permissions
log_info "Setting permissions..."
chmod -R 775 storage bootstrap/cache || true
chown -R www-data:www-data storage bootstrap/cache || true

# Clear and rebuild cache
log_info "Rebuilding cache..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

# Start Laravel server
log_info "Starting Laravel server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000

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

# Copy .env.example to .env if .env doesn't exist
if [ ! -f ".env" ] && [ -f ".env.example" ]; then
    log_info "Creating .env file from .env.example..."
    cp .env.example .env
fi

# Install Composer dependencies
# Check if vendor exists and composer.lock is newer than vendor (indicating dependencies changed)
NEED_COMPOSER_INSTALL=false
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    NEED_COMPOSER_INSTALL=true
elif [ -f "composer.lock" ] && [ "composer.lock" -nt "vendor/autoload.php" ]; then
    NEED_COMPOSER_INSTALL=true
    log_info "composer.lock is newer than vendor, dependencies may have changed"
fi

if [ "$NEED_COMPOSER_INSTALL" = true ]; then
    log_info "Installing/Updating Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev || {
        log_error "Composer install failed, trying with dev dependencies..."
        composer install --no-interaction --prefer-dist --optimize-autoloader
    }
else
    log_info "Composer dependencies up to date"
fi

# Install Node dependencies and build assets
if [ -f "package.json" ]; then
    NEED_NPM_INSTALL=false
    if [ ! -d "node_modules" ]; then
        NEED_NPM_INSTALL=true
    elif [ -f "package-lock.json" ] && [ "package-lock.json" -nt "node_modules" ]; then
        NEED_NPM_INSTALL=true
        log_info "package-lock.json changed, reinstalling npm dependencies"
    fi

    if [ "$NEED_NPM_INSTALL" = true ]; then
        log_info "Installing Node dependencies (including dev packages)..."
        if command -v npm >/dev/null 2>&1; then
            if [ -f "package-lock.json" ]; then
                npm ci --include=dev --omit=optional || npm install --include=dev --omit=optional
            else
                npm install --include=dev --omit=optional
            fi
        else
            log_warn "npm not found, skipping frontend dependency installation"
        fi
    else
        log_info "Node dependencies up to date"
    fi

    if command -v npm >/dev/null 2>&1; then
        if [ ! -f "public/build/manifest.json" ] || find resources -type f -newer public/build/manifest.json | grep -q .; then
            log_info "Building frontend assets with Vite..."
            npm run build || log_warn "npm build failed, continuing..."
        else
            log_info "Frontend assets already built"
        fi
    fi
fi

# Generate application key if not set in .env
if [ -f ".env" ]; then
    CURRENT_KEY=$(grep -E '^APP_KEY=' .env | cut -d '=' -f2- | tr -d '[:space:]')
else
    CURRENT_KEY=""
fi

if [ -z "$CURRENT_KEY" ] || [ "$CURRENT_KEY" = "base64:" ]; then
    log_info "Generating application key..."
    php artisan key:generate --force || {
        log_error "Failed to generate application key. Please ensure .env exists and is writable."
            exit 1
    }
else
    log_info "Application key already present."
fi

# Optimize Laravel for production (only if not in debug mode)
if [ "${APP_ENV:-production}" != "local" ] && [ "${APP_DEBUG:-false}" != "true" ]; then
    log_info "Optimizing Laravel for production..."
    php artisan config:cache || log_warn "Config cache failed, continuing..."
    php artisan route:cache || log_warn "Route cache failed, continuing..."
    php artisan view:cache || log_warn "View cache failed, continuing..."
else
    log_info "Skipping optimization (development mode)"
fi

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
    php artisan migrate:fresh --seed --force || {
        log_error "Initial migration failed!"
        exit 1
    }
else
    log_info "Database exists: Running migrations only..."
    php artisan migrate --force || {
        log_warn "Migration failed. This might be due to schema conflicts."
        log_warn "To reset database, run: docker compose exec app php artisan migrate:fresh --seed"
        # Don't fail completely, let the app start anyway
        log_warn "Continuing with existing database state..."
    }
fi

# Create storage link if not exists
if [ ! -L "public/storage" ]; then
    log_info "Creating storage symbolic link..."
    php artisan storage:link || true
fi

# Set proper permissions
log_info "Setting permissions..."
# Create directories if they don't exist
mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
# Try to change ownership, but don't fail if it doesn't work (for different user IDs)
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || {
    log_warn "Could not change ownership to www-data, continuing with current permissions..."
}

# Clear and rebuild cache (always clear on restart to ensure fresh state after git pull)
log_info "Clearing cache to ensure fresh state..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

# Rebuild cache if in production mode
if [ "${APP_ENV:-production}" != "local" ] && [ "${APP_DEBUG:-false}" != "true" ]; then
    log_info "Rebuilding cache for production..."
    php artisan config:cache || log_warn "Config cache failed, continuing..."
    php artisan route:cache || log_warn "Route cache failed, continuing..."
    php artisan view:cache || log_warn "View cache failed, continuing..."
fi

# Start Laravel server
log_info "Starting Laravel server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000

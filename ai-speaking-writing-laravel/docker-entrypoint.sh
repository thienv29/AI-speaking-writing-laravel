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
    local max_attempts=60
    local attempt=1
    local root_password="${MYSQL_ROOT_PASSWORD:-rootpassword}"
    
    while [ $attempt -le $max_attempts ]; do
        # First, try to connect with root user (to check if MySQL is up and create DB/user if needed)
        if php -r "try { 
            \$pdo = new PDO('mysql:host=${DB_HOST}', 'root', '${root_password}'); 
            \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            \$pdo->query('SELECT 1');
            exit(0); 
        } catch (PDOException \$e) { 
            exit(1); 
        }" 2>/dev/null; then
            # MySQL is up, ensure database and user exist
            # Note: MySQL container may have already created the user via MYSQL_USER env var
            # We need to ensure it uses mysql_native_password plugin for compatibility
            php -r "
            try {
                \$pdo = new PDO('mysql:host=${DB_HOST}', 'root', '${root_password}');
                \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                \$pdo->exec('CREATE DATABASE IF NOT EXISTS ${DB_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
                
                \$stmt = \$pdo->query(\"SELECT COUNT(*) FROM mysql.user WHERE user='${DB_USERNAME}' AND host='%'\");
                \$userExists = \$stmt->fetchColumn() > 0;
                
                if (!\$userExists) {
                    \$pdo->exec(\"CREATE USER '${DB_USERNAME}'@'%' IDENTIFIED WITH mysql_native_password BY '${DB_PASSWORD}'\");
                } else {
                    \$pdo->exec(\"ALTER USER '${DB_USERNAME}'@'%' IDENTIFIED WITH mysql_native_password BY '${DB_PASSWORD}'\");
                }
                
                \$pdo->exec(\"GRANT ALL PRIVILEGES ON ${DB_DATABASE}.* TO '${DB_USERNAME}'@'%'\");
                \$pdo->exec('FLUSH PRIVILEGES');
                exit(0);
            } catch (PDOException \$e) {
                exit(1);
            }
            " 2>/dev/null || true
            
            # Wait a bit for privileges to take effect
            sleep 1
            
            # Now try to connect with the app user
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
            else
                # If connection fails, try one more time after a short delay
                sleep 2
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
            fi
        fi
        
        if [ $attempt -eq $max_attempts ]; then
            log_error "MySQL connection failed after $max_attempts attempts"
            log_error "Please check MySQL container logs: docker compose logs mysql"
            exit 1
        fi
        
        if [ $((attempt % 5)) -eq 0 ]; then
            log_warn "Waiting for MySQL... (attempt $attempt/$max_attempts)"
        fi
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

# Install Composer dependencies (only if not already installed in image)
# Dependencies should be installed during image build, but check anyway
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    log_info "Vendor directory not found, installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev || {
        log_error "Composer install failed, trying with dev dependencies..."
        composer install --no-interaction --prefer-dist --optimize-autoloader
    }
else
    log_info "Composer dependencies already installed (from image build)"
fi

# Install Node dependencies and build assets (only if not already built in image)
# Assets should be built during image build, but check anyway
if [ -f "package.json" ]; then
    if [ ! -d "node_modules" ]; then
        log_info "Node modules not found, installing..."
        if command -v npm >/dev/null 2>&1; then
            npm ci --omit=optional || npm install --omit=optional
        fi
    fi

    if command -v npm >/dev/null 2>&1; then
        if [ ! -f "public/build/manifest.json" ]; then
            log_info "Frontend assets not found, building..."
            npm run build || log_warn "npm build failed, continuing..."
        else
            log_info "Frontend assets already built (from image build)"
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

# Check if we should force fresh migrations (useful when cloning repo fresh)
# Set FORCE_FRESH_MIGRATIONS=true in .env to force fresh migrations
FORCE_FRESH=false
if [ -f ".env" ]; then
    FORCE_FRESH_VALUE=$(grep -E '^FORCE_FRESH_MIGRATIONS=' .env | cut -d '=' -f2- | tr -d '[:space:]')
    if [ "$FORCE_FRESH_VALUE" = "true" ] || [ "$FORCE_FRESH_VALUE" = "1" ]; then
        FORCE_FRESH=true
        log_warn "FORCE_FRESH_MIGRATIONS=true detected - will run fresh migrations"
    fi
fi

# Run migrations
if [ "$DB_HAS_TABLES" = false ] || [ "$FORCE_FRESH" = true ]; then
    if [ "$FORCE_FRESH" = true ] && [ "$DB_HAS_TABLES" = true ]; then
        log_warn "Running fresh migrations (FORCE_FRESH_MIGRATIONS=true) - existing data will be lost!"
    else
        log_info "First time setup: Running fresh migrations with seeders..."
    fi
    php artisan migrate:fresh --seed --force || {
        log_error "Initial migration failed!"
        exit 1
    }
    # Remove the flag after successful fresh migration
    if [ "$FORCE_FRESH" = true ] && [ -f ".env" ]; then
        sed -i 's/^FORCE_FRESH_MIGRATIONS=.*/FORCE_FRESH_MIGRATIONS=false/' .env
        log_info "Set FORCE_FRESH_MIGRATIONS=false after successful fresh migration"
    fi
else
    log_info "Database exists: Running migrations only..."
    php artisan migrate --force || {
        log_error "Migration failed! This might be due to schema conflicts."
        log_error "If you cloned the repo fresh, you may need to reset the database:"
        log_error "  1. Stop containers: docker compose down"
        log_error "  2. Remove MySQL volume: docker volume rm <project-name>_mysql_data"
        log_error "  3. Start again: docker compose up -d"
        log_error "  OR set FORCE_FRESH_MIGRATIONS=true in .env and restart"
        exit 1
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

FROM php:8.1-cli

# Metadata
LABEL maintainer="I-CLC Development Team"
LABEL description="I-CLC Learning Platform - Laravel Application"

# Install system dependencies
# Note: Using mariadb-client instead of mysql-client (compatible with Debian Trixie+)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    mariadb-client \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install Node.js (for asset builds)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && npm install -g npm@latest \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache

# Configure PHP for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Create non-root user for security (if not exists)
RUN (getent group www-data || groupadd -g 1000 www-data) && \
    (id -u www-data >/dev/null 2>&1 || useradd -u 1000 -g www-data -s /bin/bash -m www-data)

# Entrypoint tự động:
# 1. Đợi MySQL ready (với retry logic)
# 2. composer install (nếu chưa có vendor)
# 3. php artisan key:generate (nếu chưa có APP_KEY)
# 4. Optimize Laravel (config, route, view cache)
# 5. Chạy migrations (fresh --seed nếu lần đầu, migrate nếu đã có data)
# 6. Tạo storage link và set permissions
# 7. php artisan serve
ENTRYPOINT ["docker-entrypoint.sh"]

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php -r "file_get_contents('http://localhost:8000') ? exit(0) : exit(1);"


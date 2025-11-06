FROM php:8.1-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy entrypoint script (tự động chạy migrations khi container start)
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Entrypoint sẽ tự động:
# 1. Đợi MySQL ready
# 2. composer install (nếu chưa có vendor)
# 3. php artisan key:generate (nếu chưa có APP_KEY)
# 4. php artisan migrate:fresh --seed (lần đầu) hoặc migrate (lần sau)
# 5. php artisan serve
ENTRYPOINT ["docker-entrypoint.sh"]


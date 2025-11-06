# Hướng dẫn Deploy với Docker Compose

## Link Git Repository

```
https://github.com/thienv29/AI-speaking-writing-laravel.git
Branch: writing
```

## Yêu cầu

- Docker và Docker Compose đã cài đặt
- Git đã cài đặt

## Các bước Deploy

### 1. Clone repository

```bash
git clone https://github.com/thienv29/AI-speaking-writing-laravel.git
cd AI-speaking-writing-laravel/ai-speaking-writing-laravel
git checkout writing
```

### 2. Copy file .env

```bash
cp .env.example .env
```

### 3. Cấu hình .env

Chỉnh sửa file `.env`:

```env
APP_NAME="I-CLC Learning Platform"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost:8050

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=iclc_db
DB_USERNAME=iclc_user
DB_PASSWORD=password

GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-1.5-flash
```

### 4. Chạy Docker Compose

```bash
# Build và start containers
docker-compose up -d --build

# Install dependencies (chờ container khởi động xong)
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Seed database (optional)
docker-compose exec app php artisan db:seed

# Create storage link
docker-compose exec app php artisan storage:link

# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

### 5. Truy cập ứng dụng

- **URL**: http://localhost:8050
- **Database**: localhost:3307 (MySQL)

## Lệnh quản lý

### Xem logs
```bash
docker-compose logs -f app
```

### Stop containers
```bash
docker-compose down
```

### Restart containers
```bash
docker-compose restart
```

### Rebuild containers
```bash
docker-compose up -d --build
```

### Truy cập vào container
```bash
docker-compose exec app bash
```

## Cấu hình Port

Port mặc định: **8050**

Để thay đổi port, sửa file `docker-compose.yml`:

```yaml
nginx:
  ports:
    - "YOUR_PORT:80"
```

## Lưu ý

1. Đảm bảo port 8050 không bị sử dụng bởi ứng dụng khác
2. Database sẽ được lưu trong volume `mysql_data`
3. Cần cấu hình GEMINI_API_KEY trong `.env` để sử dụng tính năng chấm điểm
4. Sau khi deploy, chạy `php artisan storage:link` nếu cần

## Troubleshooting

### Lỗi permission
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Reset database
```bash
docker-compose exec app php artisan migrate:fresh
docker-compose exec app php artisan db:seed
```

### Clear all cache
```bash
docker-compose exec app php artisan optimize:clear
```


# Hướng dẫn Deploy Project I-CLC Learning Platform

## Yêu cầu hệ thống
- Docker và Docker Compose đã được cài đặt
- Git đã được cài đặt

## Các bước thực hiện

### 1. Clone project từ branch writing
```bash
git clone -b writing https://github.com/thienv29/AI-speaking-writing-laravel.git
cd AI-speaking-writing-laravel/ai-speaking-writing-laravel
```

### 2. Cài đặt Composer dependencies
```bash
docker run --rm -v $(pwd):/app composer install
```

### 3. Tạo file .env
```bash
cp .env.example .env
```

### 4. Chỉnh sửa file .env - Thêm GEMINI_API_KEY
Mở file `.env` và thêm API key Gemini vào:
```bash
nano .env
# Hoặc
vi .env
```

Tìm dòng `GEMINI_API_KEY=your_gemini_api_key_here` và thay thế `your_gemini_api_key_here` bằng API key thực tế (sẽ được gửi riêng).

### 5. Chạy Docker Compose (tự động setup)
```bash
docker-compose up -d
```

Hệ thống sẽ tự động:
- Đợi MySQL khởi động
- Generate application key
- Chạy migrations và seeders
- Khởi động Laravel server

**Đợi khoảng 1-2 phút để setup hoàn tất, sau đó truy cập:** http://localhost:8000

**Lưu ý:** Nếu cần reset database:
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

**Clear cache khi cần:**
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

## Truy cập ứng dụng

- **URL**: http://localhost:8000
- **Database**: localhost:3307 (user: iclc_user, password: password, database: iclc_db)

## Các lệnh quản lý hữu ích

```bash
# Xem logs
docker-compose logs -f app

# Dừng containers
docker-compose down

# Khởi động lại
docker-compose restart

# Rebuild containers
docker-compose up -d --build

# Chạy lại migrations và seeders (nếu cần)
docker-compose exec app php artisan migrate:fresh --seed
```

## Lưu ý

- API key Gemini sẽ được gửi riêng qua tin nhắn/email
- File `.env` không được commit vào git (đã được gitignore)
- Nếu gặp lỗi, kiểm tra logs bằng: `docker-compose logs -f app`

## Hỗ trợ

Nếu gặp vấn đề trong quá trình deploy, vui lòng liên hệ để được hỗ trợ.


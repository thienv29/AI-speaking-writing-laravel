# Hướng dẫn Deploy Project I-CLC Learning Platform

## Yêu cầu hệ thống
- Docker và Docker Compose đã được cài đặt
- Git đã được cài đặt

## 🚀 Lần đầu deploy (Setup mới)

### 1. Clone project từ branch writing
```bash
git clone -b writing https://github.com/thienv29/AI-speaking-writing-laravel.git
cd AI-speaking-writing-laravel/ai-speaking-writing-laravel
```

### 2. Tạo file .env
```bash
cp .env.example .env
```

### 3. Chỉnh sửa file .env - Thêm GEMINI_API_KEY
Mở file `.env` và thêm API key Gemini vào:
```bash
nano .env
# Hoặc
vi .env
```

Tìm dòng `GEMINI_API_KEY=your_gemini_api_key_here` và thay thế `your_gemini_api_key_here` bằng API key thực tế (sẽ được gửi riêng).

### 4. Chạy Docker Compose (tự động setup)
```bash
docker-compose up -d
```

Hệ thống sẽ tự động:
- Cài đặt Composer dependencies
- Đợi MySQL khởi động
- Generate application key
- **Chạy migrations và seeders (lần đầu)**
- Khởi động Laravel server

**Đợi khoảng 1-2 phút để setup hoàn tất, sau đó truy cập:** http://localhost:8000

---

## 🔄 Cập nhật code (Lần sau)

Khi có code mới, chỉ cần:

```bash
git pull
docker-compose up -d --build
```

Hoặc nếu không cần build lại image:

```bash
git pull
docker-compose restart
```

**Lưu ý:**
- Hệ thống sẽ tự động chạy migrations mới (nếu có)
- **KHÔNG** chạy lại seeders (giữ nguyên dữ liệu)
- **KHÔNG** drop database (giữ nguyên dữ liệu)

---

## 📋 Các lệnh quản lý hữu ích

```bash
# Xem logs
docker-compose logs -f app

# Dừng containers
docker-compose down

# Khởi động lại
docker-compose restart

# Rebuild containers (khi có thay đổi Dockerfile)
docker-compose up -d --build

# Reset database về trạng thái ban đầu (XÓA HẾT DỮ LIỆU)
docker-compose exec app php artisan migrate:fresh --seed

# Chạy migrations mới (không mất dữ liệu)
docker-compose exec app php artisan migrate

# Clear cache khi cần
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

## 🗄️ Truy cập ứng dụng

- **URL**: http://localhost:8000
- **Database**: localhost:3307 (user: iclc_user, password: password, database: iclc_db)

## ⚠️ Lưu ý quan trọng

- API key Gemini sẽ được gửi riêng qua tin nhắn/email
- File `.env` không được commit vào git (đã được gitignore)
- Khi có migration mới, hệ thống sẽ tự động chạy khi restart container
- **Lần đầu:** Chạy `migrate:fresh --seed` (tạo database + dữ liệu mẫu)
- **Lần sau:** Chỉ chạy `migrate` (giữ nguyên dữ liệu, chỉ thêm migration mới)
- Nếu gặp lỗi, kiểm tra logs bằng: `docker-compose logs -f app`

## 🆘 Hỗ trợ

Nếu gặp vấn đề trong quá trình deploy, vui lòng liên hệ để được hỗ trợ.

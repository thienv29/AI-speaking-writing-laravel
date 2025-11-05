# Hướng Dẫn Deploy với Ngrok

## 🚀 Ngrok là gì?

Ngrok là công cụ tạo **tunnel** từ localhost của bạn ra internet, cho phép người khác truy cập website đang chạy local của bạn qua URL công khai.

## 📋 Các Bước Setup

### 1. Cài đặt Ngrok

**macOS:**
```bash
brew install ngrok
```

**Hoặc download từ:** https://ngrok.com/download

### 2. Đăng ký tài khoản Ngrok (miễn phí)

1. Vào https://ngrok.com và đăng ký
2. Copy **authtoken** từ dashboard
3. Chạy lệnh:
```bash
ngrok config add-authtoken YOUR_AUTH_TOKEN
```

### 3. Khởi động Laravel server

```bash
cd ai-speaking-writing-laravel
php artisan serve
# Server sẽ chạy tại http://localhost:8000
```

### 4. Chạy Ngrok

Mở terminal mới và chạy:
```bash
ngrok http 8000
```

Hoặc nếu Laravel chạy port khác:
```bash
ngrok http 8050  # nếu bạn dùng port 8050
```

### 5. Cấu hình Laravel để nhận request từ ngrok

**Option 1: Sửa .env tạm thời (khuyến nghị)**

Khi ngrok chạy, bạn sẽ nhận được URL như:
```
Forwarding: https://abc123.ngrok-free.app -> http://localhost:8000
```

Update `.env`:
```env
APP_URL=https://abc123.ngrok-free.app
```

Sau đó chạy:
```bash
php artisan config:clear
php artisan cache:clear
```

**Option 2: Trust ngrok domain (tốt hơn)**

Sửa `config/app.php` hoặc thêm vào `.env`:
```env
APP_URL=https://abc123.ngrok-free.app
```

Hoặc trust tất cả ngrok domains:
```php
// config/app.php
'url' => env('APP_URL', 'http://localhost'),
'trusted_proxies' => '*', // trong production nên cụ thể hơn
```

### 6. Cấu hình CORS (nếu cần)

Nếu API bị CORS error, check `config/cors.php`:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['*'], // hoặc thêm ngrok domain cụ thể
```

## ⚠️ Lưu ý Quan Trọng

### 1. Gemini API Key
- ✅ Đã có sẵn trong `.env` → Không cần thay đổi
- ✅ Ngrok chỉ expose local server, không ảnh hưởng API keys

### 2. Database
- ✅ Database đang chạy local → OK
- ✅ Người khác test sẽ dùng chung database của bạn

### 3. Security
- ⚠️ **KHÔNG** expose production database
- ⚠️ Chỉ dùng để **test/demo**, không phải production
- ⚠️ Ngrok free có giới hạn:
  - Session timeout sau 2 giờ
  - URL thay đổi mỗi lần restart
  - Có thể bị rate limit

### 4. Static Assets (CSS, Images)

Kiểm tra các file CSS/JS/images có load đúng không:
- Nếu dùng relative paths → OK
- Nếu dùng absolute paths → cần update `APP_URL`

## 🔧 Troubleshooting

### Problem 1: CSS/Images không load
**Fix:** 
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Problem 2: API không hoạt động
**Fix:** Check `APP_URL` trong `.env` phải match với ngrok URL

### Problem 3: Ngrok URL thay đổi
**Fix:** 
- Mỗi lần restart ngrok → URL mới
- Cần update `APP_URL` trong `.env` và clear cache lại

### Problem 4: Session không persist
**Fix:** 
- Ngrok free có thể reset session
- Có thể upgrade lên ngrok paid để giữ session

## 📝 Quick Start Script

Tạo file `start-ngrok.sh`:
```bash
#!/bin/bash

# Start Laravel server in background
php artisan serve --port=8000 &
LARAVEL_PID=$!

# Wait a bit
sleep 2

# Start ngrok
ngrok http 8000

# Cleanup khi dừng
kill $LARAVEL_PID
```

Chạy: `chmod +x start-ngrok.sh && ./start-ngrok.sh`

## 🎯 Workflow Test với Ngrok

1. **Start Laravel:**
```bash
php artisan serve --port=8000
```

2. **Start Ngrok (terminal khác):**
```bash
ngrok http 8000
```

3. **Copy ngrok URL** (ví dụ: `https://abc123.ngrok-free.app`)

4. **Update .env:**
```env
APP_URL=https://abc123.ngrok-free.app
```

5. **Clear cache:**
```bash
php artisan config:clear
```

6. **Share URL** với bạn bè: `https://abc123.ngrok-free.app`

7. **Test:**
   - Homepage: `https://abc123.ngrok-free.app/`
   - Writing: `https://abc123.ngrok-free.app/writing`
   - API: `https://abc123.ngrok-free.app/api/questions/1`

## ✅ Checklist

- [ ] Ngrok đã cài đặt và authenticated
- [ ] Laravel server đang chạy (`php artisan serve`)
- [ ] Ngrok đang chạy và có URL
- [ ] `.env` đã update `APP_URL` = ngrok URL
- [ ] Đã clear cache (`php artisan config:clear`)
- [ ] Database đã có data để test
- [ ] Gemini API key đã config trong `.env`
- [ ] Test thử trên browser của bạn trước
- [ ] Share URL với bạn bè!

## 🎉 Tips

- **Ngrok paid** ($8/tháng): Giữ URL cố định, không timeout
- **Ngrok free**: OK để test ngắn hạn
- **Alternatives**: 
  - Cloudflare Tunnel (miễn phí)
  - LocalTunnel (miễn phí)
  - Serveo (miễn phí)


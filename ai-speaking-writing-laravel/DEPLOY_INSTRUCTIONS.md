# Hướng dẫn Deploy

## Các vấn đề đã được fix

1. ✅ **Build image và push lên Docker Hub** - Image mới được build và push lên Docker Hub
2. ✅ **Sử dụng image từ Docker Hub** - Deploy nhanh hơn, không cần build lại
3. ✅ **Migrations tự động chạy** với `FORCE_FRESH_MIGRATIONS=true`
4. ✅ **Seeder tự động xóa data cũ** và tạo data mới đúng cấu trúc

## Build và Push Image lên Docker Hub

### 1. Build và push image mới
```bash
cd ai-speaking-writing-laravel
./build-and-push-image.sh
```

Script sẽ:
- Build image từ Dockerfile
- Hỏi bạn có muốn push lên Docker Hub không
- Push image lên Docker Hub nếu bạn chọn "y"

### 2. Hoặc build và push thủ công
```bash
# Build image
docker build -t thienv29/iclc-speaking-and-writing-app:latest .

# Push lên Docker Hub
docker push thienv29/iclc-speaking-and-writing-app:latest
```

### 3. Tag version cụ thể (khuyến nghị)
```bash
# Build với version tag
docker build -t thienv29/iclc-speaking-and-writing-app:v1.0.0 .
docker tag thienv29/iclc-speaking-and-writing-app:v1.0.0 thienv29/iclc-speaking-and-writing-app:latest

# Push cả 2 tags
docker push thienv29/iclc-speaking-and-writing-app:v1.0.0
docker push thienv29/iclc-speaking-and-writing-app:latest
```

## Các bước deploy

### 1. Clone project
```bash
git clone -b test-merge-branch https://github.com/thienv29/AI-speaking-writing-laravel.git
cd AI-speaking-writing-laravel/ai-speaking-writing-laravel
```

### 2. Tạo file .env
```bash
cp .env.example .env
```

Chỉnh sửa `.env` với các giá trị cần thiết:
```env
APP_KEY=base64:... (generate bằng: php artisan key:generate)
GEMINI_API_KEY=your_api_key_here
FORCE_FRESH_MIGRATIONS=true  # Set true lần đầu để chạy fresh migrations
```

### 3. Build và chạy containers
```bash
docker compose -f docker-compose.deploy.yml up -d --build
```

### 4. Kiểm tra logs
```bash
docker compose -f docker-compose.deploy.yml logs -f app
```

### 5. Kiểm tra data sau khi deploy
```bash
docker compose -f docker-compose.deploy.yml exec app php artisan tinker --execute="
echo 'Lessons: ' . \App\Models\Lesson::count() . PHP_EOL;
echo 'Exercises: ' . \App\Models\Exercise::count() . PHP_EOL;
echo 'Questions: ' . \App\Models\Question::count() . PHP_EOL;
echo 'Groups: ' . \App\Models\Group::count() . PHP_EOL;
"
```

**Kết quả mong đợi:**
- Lessons: 5
- Exercises: 10
- Questions: 100
- Groups: 5

## Nếu gặp lỗi

### Lỗi: Migrations chưa chạy
```bash
docker compose -f docker-compose.deploy.yml exec app php artisan migrate:fresh --seed
```

### Lỗi: Code không được cập nhật
```bash
# Rebuild container
docker compose -f docker-compose.deploy.yml up -d --build --force-recreate app
```

### Lỗi: Database schema không đúng
```bash
# Xóa volume và chạy lại
docker compose -f docker-compose.deploy.yml down -v
docker compose -f docker-compose.deploy.yml up -d --build
```

## Cấu trúc data sau khi seed

- **5 Lessons:**
  - Bài 1: Luyện phát âm từ vựng (2 exercises, 20 questions)
  - Bài 2: Luyện phát âm câu (2 exercises, 20 questions)
  - Bài 3: Luyện viết trả lời câu hỏi (2 exercises, 20 questions)
  - Bài 4: Luyện viết hoàn thành câu (2 exercises, 20 questions)
  - Bài 5: Luyện viết từ thành câu (2 exercises, 20 questions)

- **10 Exercises:** Mỗi lesson có 2 exercises, mỗi exercise có 10 questions

- **100 Questions:** Tổng cộng 100 questions

- **5 Groups:** Từ vựng cơ bản, Ngữ pháp cơ bản, Giao tiếp hàng ngày, Câu hỏi mở rộng, Luyện tập tổng hợp


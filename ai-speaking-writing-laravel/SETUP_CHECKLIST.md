# SETUP GUIDE - Để máy khác pull về chạy được

## ⚠️ VẤN ĐỀ QUAN TRỌNG

### 1. GrammarCheckerService.php BỊ XÓA
- ❌ File `app/Services/GrammarCheckerService.php` đã bị xóa
- ⚠️ Nhưng `TemplateValidatorService` vẫn đang sử dụng nó
- 💥 **Sẽ gây lỗi khi pull về!**

**Giải pháp**: Cần restore hoặc recreate GrammarCheckerService.php

### 2. Ollama KHÔNG BẮT BUỘC
- ✅ Ollama chỉ là **optional** local service
- ✅ Không có Ollama → App vẫn chạy được
- ✅ Code đã check `OLLAMA_ENABLED=false` → không gọi Ollama

### 3. OpenAI/Gemini KHÔNG BẮT BUỘC
- ✅ Chỉ cần khi muốn dùng grammar checking với AI models
- ✅ Không có API keys → App vẫn chạy được
- ✅ LanguageTool (FREE) sẽ được dùng mặc định

---

## ✅ SETUP STEPS

### 1. Clone và Install Dependencies
```bash
git clone <repo>
cd ai-speaking-writing-laravel
composer install
npm install  # Nếu có frontend assets
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
```bash
# Update .env với database credentials
php artisan migrate
php artisan db:seed  # Nếu có seeders
```

### 4. Storage Link
```bash
php artisan storage:link
```

### 5. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 📋 REQUIREMENTS

### Required (Bắt buộc):
- ✅ PHP >= 7.4
- ✅ Composer
- ✅ MySQL/PostgreSQL/SQLite
- ✅ Laravel 8 dependencies (tự động install với `composer install`)

### Optional (Tùy chọn):
- ⚠️ **Ollama**: Chỉ cần nếu muốn dùng local LLM (KHÔNG BẮT BUỘC)
- ⚠️ **OpenAI API Key**: Chỉ cần nếu muốn dùng GPT (KHÔNG BẮT BUỘC)
- ⚠️ **Gemini API Key**: Chỉ cần nếu muốn dùng Gemini (KHÔNG BẮT BUỘC)

---

## 🔧 ENV CONFIGURATION

### Minimum .env (Để chạy được):
```env
APP_NAME="I-CLC Learning"
APP_ENV=local
APP_KEY=base64:... # Generate với artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Optional (Grammar Checking):
```env
# Ollama (Local, optional)
OLLAMA_ENABLED=false
OLLAMA_URL=http://localhost:11434
OLLAMA_MODEL=mistral

# OpenAI GPT (Optional, có phí)
OPENAI_API_KEY=
OPENAI_MODEL=gpt-3.5-turbo

# Gemini (Optional, có phí)
GEMINI_API_KEY=
GEMINI_MODEL=gemini-pro
```

**Note**: Để các giá trị này empty hoặc không set → App vẫn chạy được với LanguageTool (FREE)

---

## ⚠️ CHECKLIST TRƯỚC KHI PUSH

- [ ] **GrammarCheckerService.php**: Cần restore hoặc fix TemplateValidatorService
- [ ] **.env.example**: Cần tạo với tất cả configs
- [ ] **README.md**: Cần update với setup instructions
- [ ] **Assets**: Images và sounds có trong git
- [ ] **Migrations**: Tất cả migrations đã commit
- [ ] **Dependencies**: composer.json đầy đủ

---

## 🚨 VẤN ĐỀ CẦN FIX NGAY

1. **GrammarCheckerService missing** → Code sẽ crash
2. **.env.example missing** → Người khác không biết config gì
3. **README không đầy đủ** → Không có hướng dẫn setup

---

## ✅ KẾT LUẬN

**App có thể chạy được NHƯNG cần:**
1. Fix GrammarCheckerService issue
2. Tạo .env.example
3. Update README với setup guide

**Ollama/OpenAI/Gemini**: KHÔNG BẮT BUỘC - App vẫn chạy được không có chúng!


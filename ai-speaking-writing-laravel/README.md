# I-CLC Learning Platform

English learning platform for children with AI-powered grammar and spell checking.

## Features

- ✍️ **Writing Exercises**: Multiple exercise types (WAQ, WCS, WSG)
- 🤖 **AI-Powered Scoring**: Google Gemini API for intelligent grammar checking and feedback
- 👶 **Kid-Friendly Feedback**: Simple, encouraging feedback designed for children under 13
- 🌐 **Translation**: English-Vietnamese dictionary with popup UI
- 📊 **Detailed Scoring**: Granular scoring with visual feedback and highlights
- 🎨 **Kid-Friendly UI**: Beautiful, animated interface designed for children
- 🔊 **Sound Effects**: Audio feedback for correct/incorrect answers
- 🎯 **Embed Support**: Iframe-ready pages for embedding into other websites
- 📈 **Review System**: View and filter student attempts

## Requirements

- **Docker & Docker Compose** (recommended - everything is automated)
- OR manual setup: PHP >= 8.1, Composer, MySQL, Node.js & NPM

## Quick Start (Docker - Recommended)

**Chỉ cần 1 lệnh, mọi thứ tự động!**

```bash
# 1. Clone repository
git clone <repository-url>
cd ai-speaking-writing-laravel

# 2. Chạy Docker Compose (tự động setup mọi thứ)
docker compose up -d
```

**Đó là tất cả!** 🎉

Docker entrypoint script sẽ tự động:
- ✅ Đợi MySQL sẵn sàng
- ✅ Tạo `.env` từ `.env.example`
- ✅ Cài đặt Composer dependencies
- ✅ Cài đặt Node dependencies và build assets
- ✅ Generate Laravel APP_KEY
- ✅ Chạy migrations (fresh --seed nếu lần đầu)
- ✅ Tạo storage link và set permissions
- ✅ Clear và rebuild cache
- ✅ Start Laravel server

**Truy cập ứng dụng:**
- Main app: http://localhost:8000
- Admin panel: http://localhost:8000/admin
- phpMyAdmin: http://localhost:8081
- Embed page: http://localhost:8000/embed/question/{id}

**Kiểm tra logs:**
```bash
docker compose logs -f app
```

**Dừng ứng dụng:**
```bash
docker compose down
```

**Reset database (chạy lại migrations và seeders):**
```bash
docker compose exec app php artisan migrate:fresh --seed
```

## Manual Installation (Without Docker)

### 1. Clone the repository
```bash
git clone <repository-url>
cd ai-speaking-writing-laravel
```

### 2. Install dependencies
```bash
composer install
npm install
npm run build
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database
Edit `.env` and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run migrations
```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

### 6. Clear cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 7. Start server
```bash
php artisan serve
```

## Configuration

### Gemini API Setup (Required for Writing Scoring)

The app uses Google Gemini API for intelligent writing scoring and feedback.

1. **Get Gemini API Key:**
   - Go to https://aistudio.google.com/app/apikey
   - Sign in with your Google account
   - Click "Create API Key"
   - Copy the API key

2. **Configure in `.env` (auto-created from `.env.example`):**
   ```env
   GEMINI_API_KEY=your_api_key_here
   GEMINI_MODEL=gemini-2.5-flash
   ```

   **With Docker:** Edit `.env` file in project root, then restart:
   ```bash
   docker compose restart app
   ```

### 3. API Limits (Free Tier)

- **60 requests/minute**
- **1,500 requests/day**

The app includes rate limiting and caching to stay within these limits.

## Project Structure

```
app/
├── Http/Controllers/    # API and Web controllers
├── Models/              # Eloquent models
├── Services/            # Business logic services
│   ├── TemplateValidatorService.php
│   ├── AttemptService.php
│   ├── TranslationService.php
│   └── ...
└── Support/            # Helper classes

database/
├── migrations/         # Database migrations
└── seeders/           # Database seeders

resources/
└── views/             # Blade templates

public/
├── css/               # Stylesheets
└── assets/            # Images, sounds
```

## Embed Feature (Iframe)

The app supports embedding writing exercises into other websites using iframe.

### Route

```
http://localhost:8000/embed/question/{question_id}
```

### Usage Example

```html
<iframe 
    src="http://localhost:8000/embed/question/1" 
    width="100%" 
    height="800px"
    frameborder="0"
    allow="clipboard-read; clipboard-write">
</iframe>
```

### Features

- ✅ Standalone HTML page (no parent layout)
- ✅ Responsive design optimized for iframe
- ✅ CORS headers configured for cross-origin requests
- ✅ Content-Security-Policy allows embedding from any origin
- ✅ Question navigation works within iframe
- ✅ API calls (submit answers) work from iframe

### Notes

- The embed page loads the same question interface but without header/footer/navigation
- All JavaScript and CSS are included and work independently
- API endpoints are accessible from iframe (CORS enabled)

## Docker Services

Project includes multiple Docker services:

1. **Laravel App** (`iclc-app`)
   - PHP 8.1 + Laravel
   - Auto-setup với entrypoint script
   - Port: 8000

2. **MySQL Database** (`iclc-mysql`)
   - MySQL 8.0
   - Port: 3306 (internal only)
   - Auto-creates database và user

3. **AI Speech Service** (`iclc-ai-speech-service`)
   - Python FastAPI service
   - Text-to-Speech (TTS) và Speech-to-Text (STT)
   - Port: 5001 (mapped từ internal 8000)
   - Accessible via Laravel proxy: `/api/tts` và `/api/stt`

4. **phpMyAdmin** (`iclc-phpmyadmin`)
   - Database management UI
   - Port: 8081

**All services communicate via Docker network `iclc-network`**

## Development

### With Docker (Recommended)
```bash
# Start services
docker compose up -d

# View logs
docker compose logs -f app

# Run artisan commands
docker compose exec app php artisan <command>

# Access container shell
docker compose exec app bash
```

### Without Docker
```bash
# Start Laravel server
php artisan serve

# Start Python speech service (separate terminal)
cd ../ai-speech-service
python -m uvicorn app:app --reload --port 8000
```

Default URLs:
- Laravel: http://localhost:8000
- Python Speech Service: http://localhost:5001

### Testing
```bash
docker compose exec app php artisan test
# or
php artisan test
```

## License

Proprietary - I-CLC Learning Platform

## Support

For issues and questions, please contact the development team.

## Git Repository

```
https://github.com/thienv29/AI-speaking-writing-laravel.git
Branch: writing
```

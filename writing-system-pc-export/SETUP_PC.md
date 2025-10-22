# 🖥️ SETUP WRITING SYSTEM ON PC

## 📋 Requirements:
- XAMPP hoặc WAMP (Apache + MySQL + PHP 7.4+)
- Composer
- Node.js + npm

## 🚀 Setup Steps:

### 1. Install XAMPP
- Download từ: https://www.apachefriends.org/
- Install và start Apache + MySQL

### 2. Install Composer
- Download từ: https://getcomposer.org/
- Add to PATH

### 3. Install Node.js
- Download từ: https://nodejs.org/
- npm sẽ được install cùng

### 4. Setup Project
```bash
# Copy project to C:\xampp\htdocs\
cd C:\xampp\htdocs\ai-speaking-writing-laravel

# Install dependencies
composer install
npm install

# Copy environment file
copy .env.example .env

# Generate app key
php artisan key:generate
```

### 5. Database Setup
```sql
-- Tạo database trong phpMyAdmin
CREATE DATABASE ai_speaking_writing;
```

### 6. Configure .env
```env
APP_NAME="Writing System"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_speaking_writing
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Run Migrations
```bash
php artisan migrate:fresh --seed
```

### 8. Start Server
```bash
php artisan serve
```

### 9. Test API
- Open Postman
- Import postman_test_collection.json
- Set base_url = http://localhost:8000
- Test all endpoints

## ✅ Success Indicators:
- Server starts without errors
- Database tables created
- API endpoints return 200 OK
- Postman tests pass

## 🔧 Troubleshooting:
- Port 8000 busy: Use `php artisan serve --port=8001`
- Database connection error: Check MySQL service
- Composer error: Update PHP version
- npm error: Clear cache with `npm cache clean --force`

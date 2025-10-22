#!/bin/bash

# Export Writing System project for PC deployment
echo "=== EXPORTING WRITING SYSTEM FOR PC ==="

# Create export directory
EXPORT_DIR="writing-system-pc-export"
mkdir -p "$EXPORT_DIR"

echo "📁 Creating project structure..."

# Copy core Laravel files
cp -r app "$EXPORT_DIR/"
cp -r database "$EXPORT_DIR/"
cp -r routes "$EXPORT_DIR/"
cp -r resources "$EXPORT_DIR/"
cp -r config "$EXPORT_DIR/"
cp -r bootstrap "$EXPORT_DIR/"
cp -r public "$EXPORT_DIR/"
cp -r storage "$EXPORT_DIR/"
cp -r tests "$EXPORT_DIR/"

# Copy configuration files
cp composer.json "$EXPORT_DIR/"
cp composer.lock "$EXPORT_DIR/"
cp package.json "$EXPORT_DIR/"
cp webpack.mix.js "$EXPORT_DIR/"
cp phpunit.xml "$EXPORT_DIR/"
cp artisan "$EXPORT_DIR/"
cp server.php "$EXPORT_DIR/"

# Copy environment template
cp .env.example "$EXPORT_DIR/"

# Copy documentation
cp POSTMAN_TEST_GUIDE.md "$EXPORT_DIR/"
cp POSTMAN_QUICK_START.md "$EXPORT_DIR/"
cp PC_DEPLOYMENT_PACKAGE.md "$EXPORT_DIR/"
cp postman_test_collection.json "$EXPORT_DIR/"
cp demo_server.php "$EXPORT_DIR/"

echo "📄 Creating PC setup instructions..."

# Create PC setup script
cat > "$EXPORT_DIR/SETUP_PC.md" << 'EOF'
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
EOF

echo "🗂️ Creating file structure overview..."

# Create file structure
cat > "$EXPORT_DIR/FILE_STRUCTURE.txt" << 'EOF'
Writing System Project Structure:

app/
├── Http/Controllers/
│   ├── WritingQaExerciseController.php          # Q&A exercises API
│   ├── WritingSentenceBuildingExerciseController.php  # Sentence building API
│   ├── WritingCompleteSentenceExerciseController.php  # Complete sentence API
│   └── WritingAttemptController.php             # Writing attempts API
└── Models/
    ├── WritingQaExercise.php                    # Q&A exercise model
    ├── WritingSentenceBuildingExercise.php      # Sentence building model
    ├── WritingCompleteSentenceExercise.php      # Complete sentence model
    └── WritingAttempt.php                       # Writing attempt model

database/
├── migrations/
│   ├── 2025_10_17_092721_create_writing_qa_exercises_table.php
│   ├── 2025_10_17_092724_create_writing_sentence_building_exercises_table.php
│   ├── 2025_10_17_092726_create_writing_complete_sentence_exercises_table.php
│   └── 2025_10_17_092728_create_writing_attempts_table.php
└── seeders/
    ├── DatabaseSeeder.php                       # Main seeder (updated)
    └── WritingExerciseTypeSeeder.php            # Writing exercise types

routes/
└── api.php                                      # API routes (updated)

Configuration Files:
├── composer.json                                # PHP dependencies
├── package.json                                 # Node.js dependencies
├── .env.example                                 # Environment template
├── webpack.mix.js                               # Asset compilation
└── artisan                                      # Laravel command line

Documentation:
├── POSTMAN_TEST_GUIDE.md                        # Detailed testing guide
├── POSTMAN_QUICK_START.md                       # Quick start guide
├── PC_DEPLOYMENT_PACKAGE.md                     # Deployment info
├── postman_test_collection.json                 # Postman collection
└── demo_server.php                              # Demo server for testing

API Endpoints:
GET  /api/writing/qa-exercises                   # Get Q&A exercises
GET  /api/writing/sentence-building-exercises    # Get sentence building exercises
GET  /api/writing/complete-sentence-exercises    # Get complete sentence exercises
GET  /api/writing/attempts                       # Get writing attempts
POST /api/writing/attempts                       # Create writing attempt
POST /api/writing/attempts/{id}/submit           # Submit writing attempt
EOF

echo "📦 Creating zip package..."

# Create zip file
zip -r "writing-system-pc-export.zip" "$EXPORT_DIR"

echo "✅ Export completed!"
echo "📁 Files exported to: $EXPORT_DIR"
echo "📦 Zip package: writing-system-pc-export.zip"
echo ""
echo "🚀 Next steps:"
echo "1. Copy writing-system-pc-export.zip to PC"
echo "2. Extract to C:\\xampp\\htdocs\\"
echo "3. Follow SETUP_PC.md instructions"
echo "4. Test with Postman collection"





# I-CLC Learning Platform

English learning platform for children with AI-powered grammar and spell checking.

## Features

- ✍️ **Writing Exercises**: Multiple exercise types (WAQ, WCS, WSG)
- 📝 **Template Validation**: Smart validation for common patterns (name, age, hobby, weather, etc.)
- ✅ **Grammar Checking**: Multi-API grammar checking with fallback strategy
- 🔤 **Spell Checking**: Custom spell checker with dictionary and fuzzy matching
- 🌐 **Translation**: English-Vietnamese dictionary with popup UI
- 📊 **Detailed Scoring**: Granular scoring with visual feedback
- 🎨 **Kid-Friendly UI**: Beautiful, animated interface designed for children
- 🔊 **Sound Effects**: Audio feedback for correct/incorrect answers
- 📈 **Review System**: View and filter student attempts

## Requirements

- PHP >= 7.4
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM (for frontend assets)

## Installation

### 1. Clone the repository
```bash
git clone <repository-url>
cd ai-speaking-writing-laravel
```

### 2. Install dependencies
```bash
composer install
npm install
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
php artisan migrate
php artisan db:seed  # If seeders available
```

### 6. Storage link
```bash
php artisan storage:link
```

### 7. Clear cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 8. (Optional) Build frontend assets
```bash
npm run dev
# or
npm run production
```

## Optional Services

### Grammar Checking Services

The app works without any of these, but you can enable them for better grammar checking:

#### LanguageTool (FREE - Default)
- ✅ Already enabled by default
- 20 requests/minute limit
- No configuration needed

#### Ollama (Local LLM - FREE)
```env
OLLAMA_ENABLED=true
OLLAMA_URL=http://localhost:11434
OLLAMA_MODEL=mistral
```
**Note**: Requires Ollama installed locally. See `OLLAMA_SETUP.md` for details.

#### OpenAI GPT (Paid)
```env
OPENAI_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-3.5-turbo
```
**Note**: See `OPENAI_SETUP.md` for setup instructions.

#### Gemini (Paid)
```env
GEMINI_API_KEY=your_gemini_api_key
GEMINI_MODEL=gemini-pro
```
**Note**: See `GEMINI_SETUP.md` for setup instructions.

**Priority**: LanguageTool → OpenAI → Gemini → Ollama

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

## Documentation

- `DEPLOYMENT_GUIDE.md` - Production deployment guide
- `FREE_USAGE_GUIDE.md` - How to use free services
- `OPENAI_SETUP.md` - OpenAI GPT setup instructions
- `GEMINI_SETUP.md` - Gemini API setup instructions
- `OLLAMA_SETUP.md` - Ollama local LLM setup
- `SETUP_CHECKLIST.md` - Pre-push checklist
- `PROJECT_EVALUATION.md` - Project evaluation and metrics

## Development

### Running the server
```bash
php artisan serve
```

Default URL: `http://localhost:8000`

### Testing
```bash
php artisan test
```

## License

Proprietary - I-CLC Learning Platform

## Support

For issues and questions, please contact the development team.

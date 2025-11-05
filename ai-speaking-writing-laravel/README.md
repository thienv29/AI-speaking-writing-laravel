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

## Gemini API Setup (Required for Scoring)

The app uses Google Gemini API for intelligent scoring and feedback.

### 1. Get Gemini API Key

1. Go to https://aistudio.google.com/app/apikey
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy the API key

### 2. Configure in .env

```env
GEMINI_API_KEY=your_api_key_here
GEMINI_MODEL=gemini-1.5-flash
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

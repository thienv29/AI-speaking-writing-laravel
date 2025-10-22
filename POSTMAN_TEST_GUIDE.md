# 🧪 POSTMAN TEST GUIDE - Writing System API

## 📋 Setup Instructions

### 1. Import Collection
- Mở Postman
- Click **Import** 
- Chọn file `postman_test_collection.json`
- Collection "Writing System API Tests" sẽ được import

### 2. Set Environment Variables
- Tạo Environment mới tên "Writing System"
- Thêm variable:
  - **base_url**: `http://localhost:8000`

## 🚀 Test Endpoints

### 📝 Q&A Exercises

#### GET All Q&A Exercises
```
GET {{base_url}}/api/writing/qa-exercises
Headers:
- Accept: application/json
```

**Expected Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Test Q&A Writing Exercise",
            "question": "What is your favorite hobby?",
            "instructions": "Please answer in complete sentences.",
            "sample_answers": ["My favorite hobby is reading books."],
            "expected_word_count": 15,
            "difficulty": "easy"
        }
    ]
}
```

#### GET Q&A by Difficulty
```
GET {{base_url}}/api/writing/qa-exercises/difficulty/easy
```

#### POST Create Q&A Exercise
```
POST {{base_url}}/api/writing/qa-exercises
Headers:
- Content-Type: application/json
- Accept: application/json

Body:
{
    "exercise_id": 1,
    "title": "New Q&A Exercise",
    "question": "What is your dream job?",
    "instructions": "Write a complete answer.",
    "expected_word_count": 20,
    "difficulty": "medium"
}
```

### 🏗️ Sentence Building Exercises

#### GET All Sentence Building Exercises
```
GET {{base_url}}/api/writing/sentence-building-exercises
```

#### GET by Word Type
```
GET {{base_url}}/api/writing/sentence-building-exercises/word-type/adjective
```

#### GET by Difficulty
```
GET {{base_url}}/api/writing/sentence-building-exercises/difficulty/medium
```

#### POST Create Sentence Building Exercise
```
POST {{base_url}}/api/writing/sentence-building-exercises
Body:
{
    "exercise_id": 1,
    "title": "New Sentence Building",
    "target_word": "amazing",
    "word_type": "adjective",
    "word_meaning": "extremely surprising",
    "instructions": "Write 2 sentences using 'amazing'",
    "expected_sentence_count": 2,
    "difficulty": "easy"
}
```

### ✅ Complete Sentence Exercises

#### GET All Complete Sentence Exercises
```
GET {{base_url}}/api/writing/complete-sentence-exercises
```

#### GET Random Exercise
```
GET {{base_url}}/api/writing/complete-sentence-exercises/random
```

#### GET by Sentence Type
```
GET {{base_url}}/api/writing/complete-sentence-exercises/sentence-type/simple
```

#### POST Create Complete Sentence Exercise
```
POST {{base_url}}/api/writing/complete-sentence-exercises
Body:
{
    "exercise_id": 1,
    "title": "New Complete Sentence",
    "sentence_start": "I love to",
    "instructions": "Complete with 5+ words",
    "expected_word_count": 5,
    "sentence_type": "simple",
    "difficulty": "easy"
}
```

### 📊 Writing Attempts

#### GET All Writing Attempts
```
GET {{base_url}}/api/writing/attempts
```

#### POST Create Writing Attempt
```
POST {{base_url}}/api/writing/attempts
Body:
{
    "writing_exercise_type": "writing_qa_exercise",
    "writing_exercise_id": 1,
    "user_answer": "My favorite hobby is playing guitar and reading books.",
    "time_spent": 180
}
```

#### POST Submit Writing Attempt
```
POST {{base_url}}/api/writing/attempts/1/submit
Body:
{
    "feedback": "Great answer! Good vocabulary usage.",
    "score": 92.5,
    "is_correct": true
}
```

#### GET My Attempts (requires authentication)
```
GET {{base_url}}/api/writing/attempts/my-attempts
```

#### GET Progress Stats
```
GET {{base_url}}/api/writing/attempts/progress/1
```

## 🔧 Troubleshooting

### Server Not Running
```bash
cd "/Users/build frontend/ai-speaking-writing-laravel"
php artisan serve --port=8000
```

### Database Issues
```bash
php artisan migrate:fresh --seed
```

### Test Data
```bash
php artisan tinker
# Run test commands to verify data exists
```

## 📊 Expected Test Results

✅ **Success Response Format:**
```json
{
    "success": true,
    "data": [...],
    "message": "Optional success message"
}
```

❌ **Error Response Format:**
```json
{
    "success": false,
    "message": "Error description",
    "errors": {...}
}
```

## 🎯 Test Checklist

- [ ] GET Q&A exercises returns 200
- [ ] GET Sentence building exercises returns 200  
- [ ] GET Complete sentence exercises returns 200
- [ ] GET Random exercise returns 200
- [ ] POST Create writing attempt returns 201
- [ ] POST Submit attempt returns 200
- [ ] Filter by difficulty works
- [ ] Filter by word type works
- [ ] Filter by sentence type works
- [ ] Word count calculation works
- [ ] Character count calculation works

## 🚀 Ready to Test!

Import the collection and start testing the Writing System API!







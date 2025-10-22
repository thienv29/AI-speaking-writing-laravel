# 🚀 POSTMAN QUICK START - Writing System API

## ✅ Server đang chạy tại: `http://localhost:8004`

## 📋 Bước 1: Import Collection
1. Mở Postman
2. Click **Import** 
3. Chọn file `postman_test_collection.json`
4. Collection sẽ được import thành công

## 🔧 Bước 2: Cập nhật Base URL
1. Trong Postman, click vào collection "Writing System API Tests"
2. Click **Variables** tab
3. Thay đổi `base_url` từ `http://localhost:8000` thành `http://localhost:8004`
4. Click **Save**

## 🧪 Bước 3: Test các Endpoints

### 📝 Test Q&A Exercises
```
GET http://localhost:8004/api/writing/qa-exercises
```
**Expected:** Returns Q&A exercises with success: true

### 🏗️ Test Sentence Building
```
GET http://localhost:8004/api/writing/sentence-building-exercises
```
**Expected:** Returns sentence building exercises

### ✅ Test Complete Sentence
```
GET http://localhost:8004/api/writing/complete-sentence-exercises
```
**Expected:** Returns complete sentence exercises

### 🎲 Test Random Exercise
```
GET http://localhost:8004/api/writing/complete-sentence-exercises/random
```
**Expected:** Returns a random exercise

### 📊 Test Writing Attempts
```
GET http://localhost:8004/api/writing/attempts
```
**Expected:** Returns existing writing attempts

### ➕ Test Create Writing Attempt
```
POST http://localhost:8004/api/writing/attempts
Content-Type: application/json

{
    "writing_exercise_type": "writing_qa_exercise",
    "writing_exercise_id": 1,
    "user_answer": "My favorite hobby is playing guitar and reading books.",
    "time_spent": 180
}
```
**Expected:** Creates new attempt with word count calculation

### 📤 Test Submit Writing Attempt
```
POST http://localhost:8004/api/writing/attempts/1/submit
Content-Type: application/json

{
    "feedback": "Great answer! Good vocabulary usage.",
    "score": 92.5,
    "is_correct": true
}
```
**Expected:** Submits attempt with feedback and score

## 🎯 Test Results

✅ **All endpoints should return:**
- Status Code: 200 (GET) or 201 (POST)
- Content-Type: application/json
- Response format: `{"success": true, "data": [...]}`

## 🔍 Sample Responses

### Q&A Exercise Response:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Test Q&A Writing Exercise",
            "question": "What is your favorite hobby?",
            "instructions": "Please answer in complete sentences.",
            "difficulty": "easy",
            "expected_word_count": 15
        }
    ]
}
```

### Writing Attempt Response:
```json
{
    "success": true,
    "message": "Writing attempt created successfully",
    "data": {
        "id": 2,
        "user_answer": "My favorite hobby is playing guitar and reading books.",
        "word_count": 9,
        "character_count": 54,
        "time_spent": 180,
        "is_submitted": false
    }
}
```

## 🚀 Ready to Test!

1. ✅ Server running at http://localhost:8004
2. ✅ Collection imported
3. ✅ Base URL updated
4. 🧪 Start testing endpoints!

**Tip:** Use Postman's "Send" button to test each endpoint and see the responses in real-time!







## Hướng dẫn tích hợp Gemini API cho Grammar Checking

### 1. Lấy Gemini API Key

1. Truy cập https://ai.google.dev/
2. Đăng nhập bằng tài khoản Google
3. Tạo API key mới
4. Copy API key

### 2. Cấu hình trong Laravel

Thêm vào file `.env`:

```env
GEMINI_API_KEY=your_api_key_here
GEMINI_MODEL=gemini-pro
```

### 3. Cách hoạt động

Hệ thống sẽ tự động kiểm tra grammar theo thứ tự ưu tiên:

1. **Gemini API** (nếu có API key) - 60 requests/phút
2. **LanguageTool API** (fallback) - 20 requests/phút
3. **Regex patterns** (fallback cuối cùng)

### 4. Ưu điểm của Gemini API

- ✅ Detect được nhiều lỗi phức tạp hơn
- ✅ Hiểu ngữ cảnh tốt hơn
- ✅ Free tier: 60 requests/phút (gấp 3 lần LanguageTool)
- ✅ Có thể customize prompt để output theo format

### 5. Test

```bash
php artisan tinker
```

```php
$gc = new \App\Services\GrammarCheckerService();
$errors = $gc->checkGrammar('She like sunny.');
print_r($errors);
```

### 6. Lưu ý

- Nếu không có `GEMINI_API_KEY`, hệ thống sẽ tự động dùng LanguageTool API
- Kết quả được cache 24 giờ để giảm số requests
- Rate limiting tự động để tránh vượt quá limit


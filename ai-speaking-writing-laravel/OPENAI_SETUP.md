# OPENAI GPT SETUP GUIDE

## Cách lấy OpenAI API Key

1. **Tạo tài khoản OpenAI**:
   - Truy cập: https://platform.openai.com/
   - Đăng ký/Sign in với email

2. **Lấy API Key**:
   - Vào: https://platform.openai.com/api-keys
   - Click "Create new secret key"
   - Copy API key (chỉ hiện 1 lần, lưu cẩn thận!)

3. **Setup billing** (Nếu chưa có):
   - Vào: https://platform.openai.com/account/billing
   - Add payment method (Credit card)
   - Set up spending limits để tránh phí không mong muốn

## Cấu hình trong Laravel

Thêm vào file `.env`:

```env
# OpenAI GPT Configuration
OPENAI_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-3.5-turbo
# Optional: Organization ID (nếu có)
OPENAI_ORGANIZATION=org-xxxxxxxxxxxx
```

## Models có sẵn

- `gpt-3.5-turbo` (Mặc định) - Rẻ, nhanh, đủ dùng
- `gpt-4` - Chính xác hơn nhưng đắt hơn
- `gpt-4-turbo` - Cân bằng giữa chất lượng và giá

## Giá cả (Approximate)

- **GPT-3.5-turbo**: ~$0.0015 per 1K tokens (input), ~$0.002 per 1K tokens (output)
- **GPT-4**: ~$0.03 per 1K tokens (input), ~$0.06 per 1K tokens (output)
- **GPT-4-turbo**: ~$0.01 per 1K tokens (input), ~$0.03 per 1K tokens (output)

**Ví dụ**: 1 câu grammar check ~50-100 tokens
- GPT-3.5: ~$0.0001-0.0002/câu
- GPT-4: ~$0.002-0.004/câu

## Thứ tự ưu tiên hiện tại

1. **LanguageTool** (Free, chính xác nhất cho grammar)
2. **OpenAI GPT** (Nếu có API key) - Priority 2
3. **Gemini** (Nếu có API key) - Priority 3
4. **Ollama** (Local, fallback) - Priority 4

## Testing

Sau khi cấu hình, test bằng:

```bash
php artisan tinker
```

```php
$gc = new \App\Services\GrammarCheckerService();
$errors = $gc->checkGrammar('My favorite hobby is spending money.');
var_dump($errors);
```

## Lưu ý

- ✅ Cache đã được implement để giảm API calls
- ✅ Rate limiting: 60 requests/minute
- ✅ Timeout: 30 seconds
- ✅ False positive filtering đã được implement
- ⚠️ Nhớ set spending limits trên OpenAI dashboard
- ⚠️ Monitor costs trong OpenAI dashboard


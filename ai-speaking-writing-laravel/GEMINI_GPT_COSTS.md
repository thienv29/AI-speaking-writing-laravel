# GEMINI & GPT - CÓ TỐN PHÍ KHÔNG?

## ❓ Câu hỏi: Đang dùng Gemini/GPT có tốn phí không?

### ✅ TRẢ LỜI: KHÔNG TỐN PHÍ NẾU:
1. **Không có API keys trong `.env`** → Gemini/GPT KHÔNG được gọi → KHÔNG tốn phí
2. **LanguageTool hoạt động tốt** → Gemini/GPT KHÔNG được gọi → KHÔNG tốn phí

### ⚠️ CÓ THỂ TỐN PHÍ NẾU:
1. **Có API keys** VÀ LanguageTool fail → Fallback sang Gemini/GPT → TỐN PHÍ

## 📊 Logic hoạt động:

```
1. LanguageTool (FREE) → Thành công? → Return, KHÔNG gọi Gemini/GPT
2. LanguageTool (FREE) → Fail? → Check có API keys?
   ├─ Không có keys → Return empty, KHÔNG tốn phí
   └─ Có keys → Gọi Gemini/GPT → TỐN PHÍ
```

## 💰 Giá cả (nếu có sử dụng):

### OpenAI GPT:
- **GPT-3.5-turbo**: ~$0.0015/1K tokens (input), ~$0.002/1K tokens (output)
- **GPT-4**: ~$0.03/1K tokens (input), ~$0.06/1K tokens (output)
- **Ví dụ**: 1 câu grammar check ~50-100 tokens → ~$0.0001-0.0002/câu

### Gemini:
- **Gemini Pro**: ~$0.00025/1K tokens (input), ~$0.0005/1K tokens (output)
- **Ví dụ**: 1 câu grammar check ~50-100 tokens → ~$0.00005-0.0001/câu

## 🔒 Đảm bảo KHÔNG TỐN PHÍ:

### Option 1: Xóa API keys (Nếu có)
```bash
# Xóa hoặc comment các dòng này trong .env
# OPENAI_API_KEY=...
# GEMINI_API_KEY=...
```

### Option 2: Kiểm tra API keys có tồn tại không
```bash
grep -E "OPENAI_API_KEY|GEMINI_API_KEY" .env
```

Nếu không có output → Đang FREE ✅

## 📈 Tần suất sử dụng Gemini/GPT:

Với logic hiện tại:
- **LanguageTool rate limit**: 20 requests/minute
- **LanguageTool thường hoạt động tốt** → Gemini/GPT hiếm khi được gọi
- **Cache 24 giờ** → Giảm API calls

**Ước tính**: Nếu LanguageTool hoạt động tốt:
- 99% requests → LanguageTool (FREE)
- 1% requests → Gemini/GPT (nếu có keys và LanguageTool fail)

## 🎯 Kết luận:

**Câu trả lời: ĐANG KHÔNG TỐN PHÍ!** ✅

Vì:
1. LanguageTool được ưu tiên đầu tiên (FREE)
2. LanguageTool hoạt động tốt → Gemini/GPT không được gọi
3. Chỉ fallback khi LanguageTool fail (hiếm khi xảy ra)

**Nếu muốn 100% chắc chắn không tốn phí**: Xóa API keys khỏi `.env`


# FREE GRAMMAR CHECKING GUIDE

## ✅ Hiện tại đang dùng FREE - KHÔNG TỐN PHÍ!

### LanguageTool (FREE) - Đang được ưu tiên số 1

**Cách hoạt động:**
1. ✅ **LanguageTool được gọi đầu tiên** (FREE - 20 requests/minute)
2. ✅ Nếu LanguageTool thành công → **TRẢ VỀ NGAY**, không gọi APIs có phí
3. ✅ Chỉ fallback sang OpenAI/Gemini **KHI LanguageTool THẤT BẠI** (exception, rate limit)

**Kết quả:**
- ✅ LanguageTool trả về errors → Dùng errors đó
- ✅ LanguageTool trả về empty (không có lỗi) → Dùng kết quả đó, **KHÔNG gọi APIs có phí**
- ❌ LanguageTool thất bại → Mới fallback sang OpenAI/Gemini (có phí)

## 🔒 Đảm bảo KHÔNG TỐN PHÍ

### Option 1: Không thêm API keys (RECOMMENDED)
```env
# Đừng thêm các dòng này vào .env
# OPENAI_API_KEY=...
# GEMINI_API_KEY=...
```

→ Hệ thống sẽ chỉ dùng LanguageTool (FREE)

### Option 2: Nếu đã có API keys, hệ thống vẫn ưu tiên LanguageTool
- LanguageTool được check đầu tiên
- Chỉ khi LanguageTool fail mới dùng OpenAI/Gemini
- Rate limit LanguageTool: 20 requests/minute
- Cache: 24 giờ để giảm API calls

## 💰 Costs (nếu có API keys)

**LanguageTool**: FREE ✅
- 20 requests/minute
- Unlimited characters

**OpenAI GPT**: ~$0.0001-0.0002/câu
- Chỉ được gọi khi LanguageTool fail

**Gemini**: ~$0.0001-0.0002/câu  
- Chỉ được gọi khi LanguageTool fail

**Ollama**: FREE ✅ (local)

## 📊 Rate Limits

- **LanguageTool**: 20 requests/minute (FREE)
- **OpenAI**: 60 requests/minute (nếu có key)
- **Gemini**: 60 requests/minute (nếu có key)

## 🎯 Kết luận

**Hiện tại: KHÔNG TỐN PHÍ!** ✅

Hệ thống đang dùng LanguageTool (FREE) và chỉ fallback sang paid APIs khi:
- LanguageTool rate limit (20/min)
- LanguageTool API down
- LanguageTool có exception

**Khuyến nghị:** Không thêm OpenAI/Gemini API keys vào `.env` nếu muốn hoàn toàn FREE!


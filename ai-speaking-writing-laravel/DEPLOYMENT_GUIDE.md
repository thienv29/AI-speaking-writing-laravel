# DEPLOYMENT GUIDE: Grammar Checking Services

## Tổng quan về các giải pháp Grammar Checking

### 1. Ollama (Local LLM) - Chỉ dùng cho DEVELOPMENT
- ✅ **Local development**: Free, không cần API key
- ❌ **Production**: KHÔNG NÊN deploy vì:
  - Cần server mạnh (RAM 8GB+, GPU tốt)
  - Tốn tài nguyên server
  - Chậm hơn API (phải chạy model trên server)
  - Phức tạp setup (cần cài Ollama trên server, quản lý model)
  - Model chất lượng thấp (Mistral không chuyên về grammar)

### 2. LanguageTool API - RECOMMENDED cho Production
- ✅ **Free tier**: 20 requests/minute (đủ cho small/medium app)
- ✅ **Chính xác**: Chuyên về grammar checking
- ✅ **Nhẹ**: Chỉ cần HTTP request
- ⚠️ **Rate limit**: Cần cache để tránh vượt giới hạn

### 3. Gemini API - Tùy chọn cho Production
- ✅ **Chính xác**: Model AI tốt
- ✅ **Rate limit cao**: Nhiều requests hơn LanguageTool
- ❌ **Có phí**: Token-based pricing
- ✅ **Dễ deploy**: Chỉ cần API key

## Cấu hình cho Deployment

### Development (.env.local)
```env
# Ollama - Chỉ dùng local
OLLAMA_ENABLED=true
OLLAMA_URL=http://localhost:11434
OLLAMA_MODEL=mistral

# Gemini - Optional
GEMINI_API_KEY=your_key_here
GEMINI_MODEL=gemini-pro
```

### Production (.env.production)
```env
# Ollama - DISABLE trong production
OLLAMA_ENABLED=false

# LanguageTool - Mặc định được dùng (không cần config)
# Rate limit: 20 requests/minute

# Gemini - Nếu muốn dùng (paid)
GEMINI_API_KEY=your_production_key
GEMINI_MODEL=gemini-pro
```

## Thứ tự ưu tiên hiện tại

1. **LanguageTool** (Priority 1) - Chính xác nhất, free
2. **Gemini** (Priority 2) - Nếu có API key
3. **Ollama** (Priority 3) - Fallback, chỉ khi enabled

## Khi deploy lên production

1. **Đảm bảo Ollama bị disable**:
   ```env
   OLLAMA_ENABLED=false
   ```

2. **Nếu muốn dùng Gemini** (optional):
   - Set `GEMINI_API_KEY` trong production .env
   - Setup billing trên Google Cloud

3. **LanguageTool sẽ tự động được dùng**:
   - Không cần config gì
   - Rate limit: 20 requests/minute
   - System đã có cache và rate limiting built-in

## Checklist khi deploy

- [ ] Set `OLLAMA_ENABLED=false` trong production .env
- [ ] (Optional) Set `GEMINI_API_KEY` nếu muốn dùng Gemini
- [ ] Test grammar checking trên production
- [ ] Monitor LanguageTool rate limit
- [ ] Đảm bảo cache đang hoạt động để giảm API calls

## Kết luận

- **Local**: Dùng Ollama để test free
- **Production**: Chỉ dùng LanguageTool (free) hoặc Gemini (paid)
- **Ollama**: Không deploy lên production server


## Hướng dẫn tích hợp Ollama (Local LLM) cho Grammar Checking

### 1. Tại sao dùng Ollama?

✅ **Ưu điểm:**
- **Hoàn toàn miễn phí** - Không tốn phí API
- **Không có rate limit** - Dùng bao nhiêu cũng được
- **Privacy** - Data không bị gửi ra ngoài internet
- **Chạy local** - Không phụ thuộc internet
- **Control** - Bạn kiểm soát hoàn toàn

❌ **Nhược điểm:**
- Cần máy tính đủ mạnh (RAM, GPU)
- Cần cài đặt Ollama
- Tốn điện khi chạy

### 2. Cài đặt Ollama

#### macOS:
```bash
# Tải từ https://ollama.com/download
# Hoặc dùng Homebrew
brew install ollama
```

#### Linux:
```bash
curl -fsSL https://ollama.com/install.sh | sh
```

#### Windows:
- Tải installer từ https://ollama.com/download
- Chạy và cài đặt

### 3. Khởi động Ollama

```bash
# Khởi động Ollama service
ollama serve

# (hoặc để chạy background)
ollama serve &
```

Mặc định Ollama chạy trên: `http://localhost:11434`

### 4. Tải Model

Các model phù hợp cho grammar checking:

```bash
# Llama 3 (recommended - 8GB RAM)
ollama pull llama3

# Llama 3.1 (newer, better - 8GB RAM)
ollama pull llama3.1

# Mistral (smaller, faster - 4GB RAM)
ollama pull mistral

# Gemma 2 (good balance - 6GB RAM)
ollama pull gemma2
```

**Kiểm tra model đã tải:**
```bash
ollama list
```

### 5. Test Ollama

```bash
# Test trực tiếp
ollama run llama3 "Hello, how are you?"

# Test API
curl http://localhost:11434/api/generate -d '{
  "model": "llama3",
  "prompt": "Say hello"
}'
```

### 6. Cấu hình Laravel

Thêm vào file `.env`:

```env
# Ollama Configuration
OLLAMA_ENABLED=true
OLLAMA_URL=http://localhost:11434
OLLAMA_MODEL=llama3
```

### 7. Cách hoạt động

Hệ thống sẽ tự động kiểm tra grammar theo thứ tự ưu tiên:

1. **Ollama (local)** - Nếu `OLLAMA_ENABLED=true` ✅ **Ưu tiên cao nhất**
2. **Gemini API** - Nếu có API key
3. **LanguageTool API** - Fallback
4. **Regex patterns** - Fallback cuối cùng

### 8. Test trong Laravel

```bash
php artisan tinker
```

```php
$gc = new \App\Services\GrammarCheckerService();
$errors = $gc->checkGrammar('She like sunny.');
print_r($errors);
```

### 9. Yêu cầu hệ thống

**RAM tối thiểu:**
- Llama 3: 8GB RAM
- Mistral: 4GB RAM
- Gemma 2: 6GB RAM

**Khuyến nghị:**
- 16GB RAM trở lên
- GPU (optional nhưng nhanh hơn nhiều)

### 10. Troubleshooting

**Lỗi: "Connection refused"**
```bash
# Kiểm tra Ollama đang chạy
curl http://localhost:11434/api/tags

# Nếu không chạy, khởi động lại
ollama serve
```

**Lỗi: "Model not found"**
```bash
# Kiểm tra model đã tải
ollama list

# Nếu chưa có, tải model
ollama pull llama3
```

**Chậm:**
- Dùng model nhỏ hơn (mistral thay vì llama3)
- Hoặc dùng GPU nếu có

### 11. Production Deployment

**Nếu deploy lên server:**

1. **Cài Ollama trên server:**
   ```bash
   curl -fsSL https://ollama.com/install.sh | sh
   ollama serve
   ```

2. **Cấu hình trong `.env`:**
   ```env
   OLLAMA_URL=http://localhost:11434
   # hoặc nếu chạy trên server khác
   OLLAMA_URL=http://your-server-ip:11434
   ```

3. **Tạo systemd service (Linux):**
   ```bash
   sudo systemctl enable ollama
   sudo systemctl start ollama
   ```

### 12. So sánh các giải pháp

| Giải pháp | Chi phí | Rate Limit | Privacy | Độ chính xác |
|-----------|---------|------------|---------|--------------|
| **Ollama** | ✅ Free | ✅ Unlimited | ✅ Local | ⭐⭐⭐⭐ |
| LanguageTool | ✅ Free | 20 req/min | ❌ Cloud | ⭐⭐⭐ |
| Gemini | 💰 Paid | 60 req/min | ❌ Cloud | ⭐⭐⭐⭐⭐ |

### 13. Lưu ý

- ✅ Ollama cần chạy liên tục để service hoạt động
- ✅ Nếu Ollama không chạy, hệ thống tự động fallback về Gemini/LanguageTool
- ✅ Có thể disable Ollama bằng cách set `OLLAMA_ENABLED=false`
- ✅ Cache vẫn hoạt động để giảm số requests

---

**Kết luận:** Ollama là giải pháp tốt nhất cho project này vì:
- ✅ Không tốn phí
- ✅ Không có rate limit
- ✅ Privacy cao
- ✅ Độ chính xác tốt


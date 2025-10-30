## Về Model chuyên Grammar Checking

### Tình trạng hiện tại:

❌ **KHÔNG có model LLM mã nguồn mở nào chuyên về grammar checking** trong Ollama hoặc các nền tảng tương tự.

### Tại sao?

1. **Grammar checking là một task đặc biệt:**
   - Cần rules cụ thể (subject-verb agreement, tense, etc.)
   - Cần dictionary và patterns
   - Không giống general language understanding

2. **Các công cụ chuyên về grammar:**
   - **LanguageTool** - Dùng rules + patterns (không phải LLM)
   - **Grammarly** - Có dùng AI nhưng là proprietary, không open source
   - **ProWritingAid** - Tương tự

### Giải pháp tốt nhất hiện tại:

#### Option 1: Kết hợp nhiều công cụ (ĐANG DÙNG) ✅

```
Ollama (Mistral) → General understanding
    ↓ (nếu không detect được)
LanguageTool → Chuyên về grammar rules
    ↓ (nếu vẫn miss)
Regex patterns → Bắt các lỗi cụ thể
```

**Ưu điểm:**
- ✅ Tận dụng điểm mạnh của từng tool
- ✅ Ollama: Hiểu ngữ cảnh, detect lỗi phức tạp
- ✅ LanguageTool: Rules chính xác, chuyên về grammar
- ✅ Regex: Bắt các lỗi cụ thể mà LLM có thể miss

#### Option 2: Fine-tune model riêng

Có thể fine-tune một model (như Mistral/Llama) trên dataset về grammar errors, nhưng:
- ❌ Cần dataset lớn (hàng triệu câu có lỗi và đúng)
- ❌ Cần GPU mạnh để train
- ❌ Tốn thời gian và công sức
- ❌ Không chắc sẽ tốt hơn LanguageTool

#### Option 3: Dùng API chuyên dụng

- **Grammarly API** - Có tính phí, chính xác cao
- **LanguageTool Premium** - Tính phí, tốt hơn free tier
- **GPT-4 với prompt chuyên biệt** - Tính phí, accuracy cao

### So sánh:

| Giải pháp | Chuyên Grammar? | Accuracy | Cost | Privacy |
|-----------|------------------|----------|------|---------|
| **LanguageTool** | ✅ Yes | ⭐⭐⭐⭐⭐ | Free | ❌ Cloud |
| **Ollama (General)** | ❌ No | ⭐⭐⭐ | Free | ✅ Local |
| **Kết hợp (hiện tại)** | ⚠️ Hybrid | ⭐⭐⭐⭐ | Free | ⚠️ Mix |
| **Grammarly API** | ✅ Yes | ⭐⭐⭐⭐⭐ | Paid | ❌ Cloud |
| **Fine-tuned Model** | ⚠️ Possible | ⭐⭐⭐⭐ | Free* | ✅ Local |

*Free nhưng tốn thời gian train

### Khuyến nghị:

**Giữ nguyên hệ thống hiện tại** vì:
1. ✅ **Ollama (Mistral)** - Detect được nhiều lỗi với prompt tốt
2. ✅ **LanguageTool** - Chuyên về grammar, chính xác cao
3. ✅ **Regex** - Bắt các lỗi cụ thể
4. ✅ **Miễn phí** - Không tốn phí API
5. ✅ **Privacy** - Ollama chạy local

**Nếu muốn cải thiện:**
- Cải thiện prompt cho Ollama để chính xác hơn
- Mở rộng regex patterns trong `config/spellchecker.php`
- Thêm nhiều patterns cho LanguageTool

### Kết luận:

**Không có model chuyên về grammar trong Ollama**, nhưng **kết hợp nhiều công cụ** (như hiện tại) là giải pháp tốt nhất:
- ✅ Tận dụng điểm mạnh của từng tool
- ✅ Miễn phí và privacy tốt
- ✅ Accuracy đủ tốt cho mục đích sử dụng

Bạn có muốn tôi cải thiện prompt cho Ollama để chính xác hơn không?


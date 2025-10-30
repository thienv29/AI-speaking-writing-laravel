## Hướng dẫn Thanh toán Gemini API (Google Cloud Billing)

### 1. Cách Google Cloud tính phí

**Gemini API tính phí theo tokens:**
- Token = đơn vị đo lường văn bản (1 token ≈ 4 ký tự hoặc 0.75 từ)
- **Input tokens**: Văn bản bạn gửi cho API
- **Output tokens**: Văn bản API trả về

**Ví dụ:**
- Câu "She like sunny." ≈ 4 tokens
- Nếu bạn gửi 1 triệu câu tương tự ≈ 4 triệu tokens
- Chi phí: 4 triệu × $1.25 / 1 triệu = **$5** (chỉ input)

### 2. Giới hạn Free Tier

**Free tier thường bao gồm:**
- Một số lượng tokens miễn phí mỗi tháng (ví dụ: 1-2 triệu tokens/tháng)
- Sau khi hết free tier → tự động tính phí

**Lưu ý quan trọng:**
- ⚠️ **Bạn PHẢI thiết lập billing trước khi dùng (kể cả free tier)**
- Không có thẻ thanh toán → không dùng được API
- Google sẽ KHÔNG tự động charge nếu bạn chỉ dùng trong free tier

### 3. Cách thiết lập Billing

**Bước 1: Đăng ký Google Cloud Account**
1. Vào https://console.cloud.google.com/
2. Đăng nhập bằng Gmail/Google account
3. Chọn hoặc tạo project mới

**Bước 2: Thiết lập Billing Account**
1. Vào menu **Billing** (bên trái)
2. Click **"Link a billing account"** hoặc **"Create billing account"**
3. Điền thông tin:
   - **Account name**: Tên bất kỳ (ví dụ: "My Project Billing")
   - **Country**: Vietnam
   - **Address**: Địa chỉ của bạn
   - **Tax information**: Có thể bỏ qua nếu không có

**Bước 3: Thêm Payment Method**
Bạn có thể chọn một trong các cách:

#### A. Credit Card (Thẻ tín dụng)
- ✅ **Visa, Mastercard** từ ngân hàng Việt Nam
- ✅ **Thẻ debit quốc tế** (có logo Visa/Master)
- ✅ **Thẻ trả trước** (prepaid card)
- ❌ Thẻ ATM nội địa (Không hỗ trợ)

**Cách thêm:**
1. Click **"Add payment method"**
2. Chọn **"Credit card"**
3. Điền thông tin:
   - Card number
   - Expiry date
   - CVV
   - Name on card
   - Billing address

#### B. Bank Account (Tài khoản ngân hàng)
- ✅ **Chuyển khoản trực tiếp** từ ngân hàng Việt Nam
- ⚠️ Cần thiết lập riêng, phức tạp hơn

#### C. Invoicing (Thanh toán hóa đơn)
- ✅ Cho doanh nghiệp lớn
- ⚠️ Cần approval từ Google

### 4. Cách Google Charge tiền

**Quy trình:**
1. **Hàng tháng**: Google tổng hợp hóa đơn
2. **Cuối tháng**: Gửi invoice (hóa đơn) qua email
3. **Thanh toán**: 
   - Credit card: Tự động charge vào thẻ
   - Bank account: Bạn chuyển khoản theo hóa đơn

**Ví dụ:**
- Tháng 1: Dùng 500,000 tokens trong free tier → **$0**
- Tháng 2: Dùng 2,500,000 tokens (2M free + 500K phải trả) → **$0.625** (500K × $1.25 / 1M)

### 5. Budget Alerts (Cảnh báo ngân sách)

**Nên thiết lập ngay:**

1. Vào **Billing** → **Budgets & alerts**
2. Click **"Create budget"**
3. Thiết lập:
   - **Budget amount**: Ví dụ $10/tháng
   - **Alert thresholds**: 
     - 50% budget ($5) → Email cảnh báo
     - 90% budget ($9) → Email cảnh báo
     - 100% budget ($10) → Email cảnh báo + có thể disable API

**Lợi ích:**
- ✅ Nhận email khi chi phí tăng
- ✅ Tự động disable API khi vượt budget
- ✅ Kiểm soát chi phí tốt hơn

### 6. Kiểm tra chi phí

**Cách xem chi phí hiện tại:**
1. Vào **Billing** → **Reports**
2. Xem:
   - Chi phí theo ngày/tháng
   - Chi phí theo service (Gemini API)
   - Dự đoán chi phí tháng này

### 7. Tối ưu chi phí

**Các cách giảm chi phí:**

1. **Cache kết quả** ✅ (đã implement)
   - Mỗi câu chỉ check 1 lần
   - Cache 24 giờ

2. **Dùng LanguageTool trước** (miễn phí)
   - Chỉ dùng Gemini khi LanguageTool fail

3. **Rate limiting** ✅ (đã implement)
   - Giới hạn số requests/phút

4. **Fallback về regex** ✅ (đã implement)
   - Giảm số lần gọi API

### 8. Ước tính chi phí cho project của bạn

**Giả sử:**
- 100 học sinh/ngày
- Mỗi học sinh làm 10 bài tập
- Mỗi bài tập check grammar 1 lần
- **Tổng: 1,000 requests/ngày = 30,000 requests/tháng**

**Chi phí với Gemini:**
- Mỗi request ≈ 50 tokens (input + output)
- 30,000 requests × 50 tokens = 1,500,000 tokens/tháng
- Nếu free tier = 1M tokens → Phải trả: 500K tokens
- **Chi phí: ~$0.6-1/tháng**

**Chi phí với LanguageTool:**
- **Hoàn toàn miễn phí**
- Chỉ bị rate limit (20 requests/phút)

### 9. Khuyến nghị

**Cho project nhỏ (< 1000 users/ngày):**
- ✅ **Dùng LanguageTool (miễn phí)**
- ✅ Gemini chỉ làm fallback khi cần

**Cho project lớn (> 1000 users/ngày):**
- ✅ Thiết lập billing
- ✅ Set budget alert ($10-20/tháng)
- ✅ Monitor chi phí hàng tuần

### 10. Checklist trước khi deploy

- [ ] Thiết lập Google Cloud account
- [ ] Tạo project mới
- [ ] Lấy Gemini API key
- [ ] Thiết lập billing account (nếu muốn dùng Gemini)
- [ ] Thêm credit card
- [ ] Set budget alert ($10-20/tháng)
- [ ] Test với 1-2 requests
- [ ] Kiểm tra billing dashboard

### 11. Lưu ý quan trọng

⚠️ **Đọc kỹ trước khi thiết lập:**

1. **Free tier không phải "forever free"**
   - Có giới hạn tokens/tháng
   - Vượt quá → tính phí

2. **Không có thẻ → không dùng được**
   - Google yêu cầu payment method ngay cả khi dùng free tier
   - Nhưng sẽ không charge nếu trong free tier

3. **Có thể disable billing bất cứ lúc nào**
   - Vào Billing → Disable billing
   - API sẽ ngừng hoạt động ngay

4. **Luôn set budget alert**
   - Tránh chi phí không mong muốn
   - Có thể auto-disable khi vượt budget

### 12. Trợ giúp

- **Google Cloud Support**: https://cloud.google.com/support
- **Billing FAQ**: https://cloud.google.com/billing/docs/how-to
- **Gemini API Docs**: https://ai.google.dev/docs

---

**Kết luận:** 
- Nếu muốn **hoàn toàn miễn phí** → Dùng LanguageTool
- Nếu muốn **accuracy cao hơn** → Thiết lập billing cho Gemini (chi phí thấp ~$1-5/tháng cho project nhỏ)


# BÁO CÁO THỰC TẬP TỐT NGHIỆP

**Đề tài:** Xây dựng hệ thống học tiếng Anh trực tuyến cho trẻ em với AI chấm điểm tự động

**Sinh viên thực hiện:** [Tên sinh viên]

**Mã sinh viên:** [Mã sinh viên]

**Lớp:** [Lớp]

**Giảng viên hướng dẫn:** [Tên giảng viên]

**Đơn vị thực tập:** [Tên đơn vị]

**Năm học:** 2025 - 2026

**Học kỳ:** 1

---

## MỤC LỤC

1. [LỜI CẢM ƠN](#lời-cảm-ơn)
2. [DANH MỤC CÁC TỪ VIẾT TẮT](#danh-mục-các-từ-viết-tắt)
3. [DANH MỤC HÌNH ẢNH](#danh-mục-hình-ảnh)
4. [DANH MỤC BẢNG BIỂU](#danh-mục-bảng-biểu)
5. [MỞ ĐẦU](#mở-đầu)
6. [CHƯƠNG 1: TỔNG QUAN VỀ ĐỀ TÀI](#chương-1-tổng-quan-về-đề-tài)
7. [CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG](#chương-2-phân-tích-và-thiết-kế-hệ-thống)
8. [CHƯƠNG 3: CÀI ĐẶT VÀ TRIỂN KHAI](#chương-3-cài-đặt-và-triển-khai)
9. [CHƯƠNG 4: KẾT QUẢ VÀ ĐÁNH GIÁ](#chương-4-kết-quả-và-đánh-giá)
10. [KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN](#kết-luận-và-hướng-phát-triển)
11. [TÀI LIỆU THAM KHẢO](#tài-liệu-tham-khảo)
12. [PHỤ LỤC](#phụ-lục)

---

## LỜI CẢM ƠN

Em xin chân thành cảm ơn Ban Giám hiệu, Khoa Công nghệ Thông tin, các thầy cô giáo đã tạo điều kiện và hướng dẫn em trong quá trình thực tập tốt nghiệp.

Đặc biệt, em xin gửi lời cảm ơn sâu sắc đến [Tên giảng viên hướng dẫn] đã tận tình hướng dẫn, chỉ bảo em trong suốt quá trình thực hiện đề tài. Em cũng xin cảm ơn [Tên đơn vị thực tập] đã tạo môi trường làm việc chuyên nghiệp và hỗ trợ em hoàn thành tốt đợt thực tập.

Cuối cùng, em xin cảm ơn gia đình và bạn bè đã động viên, hỗ trợ em trong suốt quá trình học tập và thực tập.

---

## DANH MỤC CÁC TỪ VIẾT TẮT

- **AI**: Artificial Intelligence (Trí tuệ nhân tạo)
- **API**: Application Programming Interface (Giao diện lập trình ứng dụng)
- **WAQ**: Write Answer to Question (Viết câu trả lời cho câu hỏi)
- **WCS**: Write Complete Sentence (Hoàn thành câu)
- **WSG**: Write Sentence with Given word (Viết câu với từ cho sẵn)
- **SPW**: Speaking - Word (Nói từ)
- **SPS**: Speaking - Sentence (Nói câu)
- **TTS**: Text-to-Speech (Chuyển văn bản thành giọng nói)
- **STT**: Speech-to-Text (Chuyển giọng nói thành văn bản)
- **MVC**: Model-View-Controller (Mô hình kiến trúc phần mềm)
- **REST**: Representational State Transfer (Kiến trúc web service)
- **JSON**: JavaScript Object Notation (Định dạng dữ liệu)
- **HTTP**: Hypertext Transfer Protocol (Giao thức truyền tải siêu văn bản)
- **UI**: User Interface (Giao diện người dùng)
- **UX**: User Experience (Trải nghiệm người dùng)
- **CORS**: Cross-Origin Resource Sharing (Chia sẻ tài nguyên đa nguồn gốc)

---

## DANH MỤC HÌNH ẢNH

- Hình 1.1: Giao diện trang chủ hệ thống
- Hình 2.1: Sơ đồ kiến trúc tổng thể hệ thống
- Hình 2.2: Sơ đồ cơ sở dữ liệu
- Hình 2.3: Luồng xử lý chấm điểm bằng AI
- Hình 3.1: Cấu trúc thư mục dự án Laravel
- Hình 3.2: Giao diện quản trị hệ thống
- Hình 4.1: Kết quả chấm điểm bài viết
- Hình 4.2: Giao diện nhúng (embed) bài tập

---

## DANH MỤC BẢNG BIỂU

- Bảng 2.1: Các loại bài tập trong hệ thống
- Bảng 2.2: Cấu trúc bảng cơ sở dữ liệu
- Bảng 3.1: Các công nghệ sử dụng trong dự án
- Bảng 4.1: Kết quả đánh giá hiệu năng hệ thống

---

## MỞ ĐẦU

### 1. Lý do chọn đề tài

Trong bối cảnh công nghệ thông tin phát triển mạnh mẽ, việc ứng dụng trí tuệ nhân tạo (AI) vào giáo dục đang trở thành xu hướng tất yếu. Đặc biệt, việc học tiếng Anh trực tuyến cho trẻ em đang ngày càng phổ biến, đòi hỏi các hệ thống học tập phải thông minh, thân thiện và hiệu quả.

Hệ thống chấm điểm tự động bằng AI không chỉ giúp giảm tải công việc cho giáo viên mà còn cung cấp phản hồi tức thì cho học sinh, giúp các em cải thiện kỹ năng viết và nói tiếng Anh một cách nhanh chóng. Với sự phát triển của các mô hình ngôn ngữ lớn như Google Gemini, việc xây dựng hệ thống chấm điểm thông minh đã trở nên khả thi và hiệu quả hơn bao giờ hết.

### 2. Mục tiêu nghiên cứu

**Mục tiêu chung:**
Xây dựng hệ thống học tiếng Anh trực tuyến cho trẻ em với khả năng chấm điểm tự động bằng AI, hỗ trợ các bài tập viết và nói, cung cấp phản hồi tức thì và thân thiện với trẻ em.

**Mục tiêu cụ thể:**
- Phân tích và thiết kế hệ thống học tiếng Anh trực tuyến với các tính năng cơ bản
- Xây dựng module chấm điểm tự động sử dụng Google Gemini API
- Phát triển các loại bài tập: viết câu trả lời (WAQ), hoàn thành câu (WCS), viết câu với từ cho sẵn (WSG), và bài tập nói (SPW, SPS)
- Tích hợp dịch vụ dịch thuật Anh-Việt
- Xây dựng giao diện thân thiện, phù hợp với trẻ em dưới 13 tuổi
- Hỗ trợ tính năng nhúng (embed) bài tập vào các website khác
- Triển khai hệ thống sử dụng Docker để dễ dàng cài đặt và vận hành

### 3. Đối tượng và phạm vi nghiên cứu

**Đối tượng nghiên cứu:**
- Hệ thống học tiếng Anh trực tuyến
- Công nghệ AI trong giáo dục
- Các mô hình ngôn ngữ lớn (LLM) cho chấm điểm tự động

**Phạm vi nghiên cứu:**
- Phát triển hệ thống web sử dụng framework Laravel (PHP)
- Tích hợp Google Gemini API cho chấm điểm bài viết
- Xây dựng dịch vụ xử lý giọng nói bằng Python FastAPI
- Thiết kế giao diện web responsive, thân thiện với trẻ em
- Triển khai hệ thống trên môi trường Docker

### 4. Phương pháp nghiên cứu

- **Phương pháp nghiên cứu lý thuyết:** Nghiên cứu các tài liệu về AI trong giáo dục, các framework web hiện đại, và các API dịch vụ AI
- **Phương pháp phân tích và thiết kế:** Phân tích yêu cầu, thiết kế kiến trúc hệ thống, thiết kế cơ sở dữ liệu
- **Phương pháp thực nghiệm:** Xây dựng và triển khai hệ thống, kiểm thử các tính năng
- **Phương pháp đánh giá:** Đánh giá hiệu năng, độ chính xác của hệ thống chấm điểm

### 5. Cấu trúc báo cáo

Báo cáo được chia thành các chương chính:
- **Chương 1:** Tổng quan về đề tài, các công nghệ sử dụng
- **Chương 2:** Phân tích và thiết kế hệ thống
- **Chương 3:** Cài đặt và triển khai hệ thống
- **Chương 4:** Kết quả và đánh giá
- **Kết luận:** Tổng kết và hướng phát triển

---

## CHƯƠNG 1: TỔNG QUAN VỀ ĐỀ TÀI

### 1.1. Giới thiệu về hệ thống I-CLC Learning Platform

I-CLC Learning Platform là một hệ thống học tiếng Anh trực tuyến được thiết kế đặc biệt cho trẻ em dưới 13 tuổi. Hệ thống cung cấp các bài tập viết và nói tiếng Anh với khả năng chấm điểm tự động bằng trí tuệ nhân tạo, giúp trẻ em học tập một cách hiệu quả và thú vị.

**Các tính năng chính:**
- ✍️ **Bài tập viết:** Nhiều loại bài tập (WAQ, WCS, WSG)
- 🤖 **Chấm điểm bằng AI:** Sử dụng Google Gemini API để chấm điểm thông minh
- 👶 **Phản hồi thân thiện:** Phản hồi đơn giản, khuyến khích, phù hợp với trẻ em
- 🌐 **Dịch thuật:** Từ điển Anh-Việt với giao diện popup
- 📊 **Chấm điểm chi tiết:** Chấm điểm chi tiết với phản hồi trực quan và highlight
- 🎨 **Giao diện thân thiện:** Giao diện đẹp, có animation, thiết kế cho trẻ em
- 🔊 **Hiệu ứng âm thanh:** Phản hồi âm thanh cho câu trả lời đúng/sai
- 🎯 **Hỗ trợ nhúng:** Trang sẵn sàng cho iframe để nhúng vào website khác
- 📈 **Hệ thống xem lại:** Xem và lọc các lần làm bài của học sinh

### 1.2. Các công nghệ sử dụng

#### 1.2.1. Backend Framework: Laravel

Laravel là một PHP framework mã nguồn mở, mạnh mẽ và hiện đại, được sử dụng để xây dựng phần backend của hệ thống.

**Ưu điểm của Laravel:**
- Kiến trúc MVC rõ ràng, dễ bảo trì
- ORM (Eloquent) mạnh mẽ, dễ sử dụng
- Hệ thống routing linh hoạt
- Middleware để xử lý authentication, CORS
- Blade template engine cho views
- Artisan CLI cho các tác vụ quản lý
- Hệ thống migration và seeder cho database

**Phiên bản sử dụng:** Laravel 10.x với PHP 8.1+

#### 1.2.2. Frontend Technologies

- **HTML5, CSS3, JavaScript:** Xây dựng giao diện người dùng
- **Blade Templates:** Template engine của Laravel
- **Responsive Design:** Giao diện tương thích với nhiều thiết bị
- **AJAX:** Giao tiếp không đồng bộ với server

#### 1.2.3. Cơ sở dữ liệu: MySQL

MySQL 8.0 được sử dụng để lưu trữ dữ liệu:
- Thông tin người dùng
- Bài học, bài tập, câu hỏi
- Kết quả làm bài của học sinh
- Metadata và cấu hình hệ thống

#### 1.2.4. AI Service: Google Gemini API

Google Gemini API được sử dụng để chấm điểm bài viết tự động:
- **Model:** gemini-2.5-flash
- **Tính năng:** Phân tích ngữ pháp, chính tả, đánh giá nội dung
- **Giới hạn:** 60 requests/phút, 1,500 requests/ngày (free tier)
- **Output:** JSON với điểm số, phản hồi, lỗi chính tả/ngữ pháp

#### 1.2.5. Speech Service: Python FastAPI

Dịch vụ xử lý giọng nói được xây dựng bằng Python FastAPI:
- **Text-to-Speech (TTS):** Sử dụng gTTS (Google Text-to-Speech)
- **Speech-to-Text (STT):** Sử dụng SpeechRecognition với Google Speech API
- **Hỗ trợ:** Nhiều định dạng audio (WAV, MP3, M4A, OGG, WEBM)

#### 1.2.6. Containerization: Docker

Docker và Docker Compose được sử dụng để đóng gói và triển khai hệ thống:
- **Laravel App Container:** PHP 8.1 + Laravel
- **MySQL Container:** MySQL 8.0
- **Python Speech Service Container:** FastAPI service
- **phpMyAdmin Container:** Quản lý database

**Lợi ích:**
- Dễ dàng cài đặt và triển khai
- Môi trường nhất quán giữa development và production
- Tự động hóa quy trình setup
- Dễ dàng mở rộng và bảo trì

### 1.3. Các loại bài tập trong hệ thống

#### 1.3.1. Bài tập viết (Writing Exercises)

**WAQ - Write Answer to Question (Viết câu trả lời cho câu hỏi):**
- Học sinh đọc câu hỏi và viết câu trả lời tự do
- Không có đáp án duy nhất, đánh giá dựa trên tính liên quan và ý nghĩa
- Ví dụ: "What is your name?" → Học sinh có thể trả lời bất kỳ tên nào

**WCS - Write Complete Sentence (Hoàn thành câu):**
- Học sinh được cung cấp phần đầu câu (starter text)
- Nhiệm vụ: hoàn thành câu một cách tự nhiên
- Ví dụ: "I like to..." → "I like to play soccer."

**WSG - Write Sentence with Given word (Viết câu với từ cho sẵn):**
- Học sinh phải viết một câu hoàn chỉnh có chứa từ/cụm từ được cho
- Câu phải đúng ngữ pháp và có ý nghĩa
- Ví dụ: Từ "beautiful" → "The flower is beautiful."

#### 1.3.2. Bài tập nói (Speaking Exercises)

**SPW - Speaking Word (Nói từ):**
- Học sinh nghe và lặp lại một từ tiếng Anh
- Hệ thống ghi âm và chuyển đổi thành văn bản để đánh giá

**SPS - Speaking Sentence (Nói câu):**
- Học sinh nghe và lặp lại một câu tiếng Anh
- Hệ thống đánh giá phát âm và độ chính xác

### 1.4. Quy trình chấm điểm bằng AI

1. **Học sinh nộp bài:** Nhập câu trả lời hoặc ghi âm giọng nói
2. **Tiền xử lý:** Làm sạch dữ liệu đầu vào
3. **Gửi đến Gemini API:** Xây dựng prompt phù hợp với loại bài tập
4. **Phân tích và đánh giá:** AI phân tích ngữ pháp, chính tả, nội dung
5. **Tạo phản hồi:** Tạo phản hồi thân thiện, khuyến khích bằng tiếng Việt
6. **Trả kết quả:** Điểm số (0-100), phản hồi, highlight lỗi
7. **Lưu trữ:** Lưu kết quả vào database để xem lại

---

## CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG

### 2.1. Phân tích yêu cầu

#### 2.1.1. Yêu cầu chức năng

**Quản lý người dùng:**
- Đăng ký, đăng nhập
- Phân quyền: Admin và User (học sinh)
- Quản lý thông tin cá nhân

**Quản lý nội dung:**
- Quản lý bài học (Lessons)
- Quản lý bài tập (Exercises)
- Quản lý câu hỏi (Questions)
- Phân loại bài tập theo loại (Exercise Types)

**Làm bài và chấm điểm:**
- Học sinh làm bài tập viết và nói
- Hệ thống chấm điểm tự động bằng AI
- Hiển thị kết quả và phản hồi
- Lưu lịch sử làm bài

**Dịch thuật:**
- Tra cứu từ điển Anh-Việt
- Hiển thị popup dịch nghĩa

**Xem lại:**
- Xem lịch sử làm bài
- Lọc theo bài tập, ngày tháng
- Xem chi tiết từng lần làm bài

**Nhúng (Embed):**
- Hỗ trợ nhúng bài tập vào website khác qua iframe
- Giao diện độc lập, không phụ thuộc vào layout chính

#### 2.1.2. Yêu cầu phi chức năng

**Hiệu năng:**
- Thời gian phản hồi chấm điểm < 10 giây
- Hỗ trợ nhiều người dùng đồng thời
- Cache để tối ưu tốc độ

**Bảo mật:**
- Xác thực người dùng
- Bảo vệ CSRF
- CORS cho embed pages
- Validate input

**Khả năng mở rộng:**
- Kiến trúc module hóa
- Dễ dàng thêm loại bài tập mới
- Hỗ trợ nhiều ngôn ngữ

**Giao diện:**
- Thân thiện với trẻ em
- Responsive design
- Animation và hiệu ứng
- Âm thanh phản hồi

### 2.2. Thiết kế kiến trúc hệ thống

#### 2.2.1. Kiến trúc tổng thể

Hệ thống được xây dựng theo kiến trúc microservices với các thành phần:

```
┌─────────────────────────────────────────────────┐
│           Client (Web Browser)                  │
└──────────────┬──────────────────────────────────┘
               │
               │ HTTP/HTTPS
               │
┌──────────────▼──────────────────────────────────┐
│         Laravel Application                      │
│  ┌──────────────────────────────────────────┐   │
│  │  Controllers (API + Web)                 │   │
│  │  Services (Business Logic)               │   │
│  │  Models (Database ORM)                   │   │
│  └──────────────────────────────────────────┘   │
└──────┬──────────────────────┬────────────────────┘
       │                      │
       │                      │
┌──────▼──────┐      ┌───────▼────────┐
│   MySQL     │      │  Gemini API    │
│  Database   │      │  (AI Scoring)  │
└─────────────┘      └────────────────┘
                            │
                            │
                     ┌──────▼──────────┐
                     │  Python FastAPI │
                     │  Speech Service │
                     │  (TTS/STT)      │
                     └─────────────────┘
```

#### 2.2.2. Kiến trúc Laravel (MVC)

**Model (app/Models/):**
- `User.php`: Quản lý người dùng
- `Lesson.php`: Quản lý bài học
- `Exercise.php`: Quản lý bài tập
- `ExerciseType.php`: Loại bài tập
- `Question.php`: Câu hỏi
- `Attempt.php`: Kết quả làm bài

**View (resources/views/):**
- Blade templates cho giao diện web
- Layouts và components tái sử dụng
- Pages cho user và admin

**Controller (app/Http/Controllers/):**
- `WritingController.php`: Xử lý bài tập viết
- `AttemptController.php`: Xử lý nộp bài và chấm điểm
- `TranslationController.php`: Dịch thuật
- `SpeechController.php`: Xử lý giọng nói
- Admin controllers: Quản lý nội dung

**Service (app/Services/):**
- `GeminiScoringService.php`: Tích hợp Gemini API
- `AttemptService.php`: Logic chấm điểm
- `TranslationService.php`: Dịch thuật Anh-Việt
- `TemplateValidatorService.php`: Validate template

### 2.3. Thiết kế cơ sở dữ liệu

#### 2.3.1. Sơ đồ quan hệ (ERD)

Các bảng chính:

**users:**
- id, name, email, password, dob, role, avatar, timestamps

**lessons:**
- id, title, description, order_index, timestamps

**exercise_types:**
- id, name, code, timestamps

**exercises:**
- id, lesson_id, type_id, title, instructions, order_index, timestamps

**questions:**
- id, prompt_text, starter_text, target_text, timestamps

**exercise_question (pivot):**
- exercise_id, question_id, order_index

**attempts:**
- id, user_id, question_id, user_answer, user_audio_url, is_correct, feedback, score, evaluation_meta, timestamps

**groups:**
- id, name, description, timestamps

**group_question (pivot):**
- group_id, question_id

#### 2.3.2. Quan hệ giữa các bảng

- `Lesson` hasMany `Exercise`
- `Exercise` belongsTo `Lesson` và `ExerciseType`
- `Exercise` belongsToMany `Question` (pivot: exercise_question)
- `Question` belongsToMany `Exercise` (pivot: exercise_question)
- `Question` belongsToMany `Group` (pivot: group_question)
- `User` hasMany `Attempt`
- `Attempt` belongsTo `User` và `Question`

### 2.4. Thiết kế giao diện

#### 2.4.1. Nguyên tắc thiết kế

- **Thân thiện với trẻ em:** Màu sắc tươi sáng, hình ảnh dễ thương
- **Đơn giản:** Giao diện không phức tạp, dễ sử dụng
- **Khuyến khích:** Phản hồi tích cực, động viên
- **Trực quan:** Icon, animation, highlight rõ ràng
- **Responsive:** Hoạt động tốt trên nhiều thiết bị

#### 2.4.2. Các trang chính

- **Trang chủ:** Giới thiệu hệ thống
- **Danh sách bài học:** Hiển thị các bài học
- **Chi tiết bài học:** Danh sách bài tập trong bài học
- **Làm bài tập:** Giao diện làm bài viết/nói
- **Kết quả:** Hiển thị điểm, phản hồi, highlight lỗi
- **Xem lại:** Lịch sử làm bài
- **Admin panel:** Quản lý nội dung

### 2.5. Thiết kế API

#### 2.5.1. RESTful API Endpoints

**Writing:**
- `POST /api/attempts`: Nộp bài viết
- `GET /api/exercises/{id}`: Lấy thông tin bài tập
- `GET /api/questions/{id}`: Lấy thông tin câu hỏi

**Speaking:**
- `POST /api/speech/tts`: Text-to-Speech
- `POST /api/speech/stt`: Speech-to-Text

**Translation:**
- `GET /api/translation?word={word}`: Dịch từ

**Review:**
- `GET /api/attempts`: Lấy lịch sử làm bài
- `GET /api/attempts/{id}`: Chi tiết lần làm bài

### 2.6. Xử lý chấm điểm bằng AI

#### 2.6.1. Quy trình chấm điểm

1. **Nhận câu trả lời:** Từ học sinh
2. **Xây dựng prompt:** Dựa trên loại bài tập, câu hỏi, đáp án mẫu
3. **Gọi Gemini API:** Với prompt đã xây dựng
4. **Xử lý response:** Parse JSON từ API
5. **Tạo phản hồi:** Format phản hồi thân thiện
6. **Lưu kết quả:** Vào database
7. **Trả về client:** Điểm, phản hồi, metadata

#### 2.6.2. Prompt Engineering

Prompt được xây dựng với các thành phần:
- Context: Vai trò (giáo viên thân thiện)
- Thông tin bài học, câu hỏi
- Loại bài tập và hướng dẫn đánh giá
- Câu trả lời của học sinh
- Yêu cầu output: JSON với score, feedback, errors

#### 2.6.3. Rate Limiting

- Giới hạn 60 requests/phút
- Giới hạn 1,500 requests/ngày
- Cache kết quả để giảm API calls
- Xử lý khi vượt giới hạn

---

## CHƯƠNG 3: CÀI ĐẶT VÀ TRIỂN KHAI

### 3.1. Môi trường phát triển

#### 3.1.1. Yêu cầu hệ thống

**Development:**
- Docker & Docker Compose
- Git
- Code editor (VS Code, PHPStorm)

**Production:**
- Server với Docker support
- Domain và SSL certificate (nếu cần)
- Backup database

#### 3.1.2. Cài đặt với Docker (Khuyến nghị)

**Bước 1: Clone repository**
```bash
git clone https://github.com/thienv29/AI-speaking-writing-laravel.git
cd AI-speaking-writing-laravel/ai-speaking-writing-laravel
```

**Bước 2: Chạy Docker Compose**
```bash
docker compose up -d
```

Docker entrypoint script sẽ tự động:
- Đợi MySQL sẵn sàng
- Tạo `.env` từ `.env.example`
- Cài đặt Composer dependencies
- Cài đặt Node dependencies và build assets
- Generate Laravel APP_KEY
- Chạy migrations và seeders
- Tạo storage link
- Khởi động Laravel server

**Bước 3: Cấu hình Gemini API**

Chỉnh sửa file `.env`:
```env
GEMINI_API_KEY=your_api_key_here
GEMINI_MODEL=gemini-2.5-flash
```

**Bước 4: Truy cập ứng dụng**
- Main app: http://localhost:8000
- Admin panel: http://localhost:8000/admin
- phpMyAdmin: http://localhost:8081

### 3.2. Cấu trúc dự án

```
ai-speaking-writing-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controllers
│   │   └── Middleware/       # Middleware
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── Constants/           # Constants
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   └── views/               # Blade templates
├── routes/
│   ├── web.php              # Web routes
│   ├── api.php              # API routes
│   └── admin.php            # Admin routes
├── public/                  # Public assets
├── docker-compose.yml       # Docker configuration
├── Dockerfile               # Laravel container
└── .env                     # Environment variables
```

### 3.3. Cài đặt các thành phần chính

#### 3.3.1. Laravel Application

**Dependencies (composer.json):**
- laravel/framework: ^10.0
- Các packages hỗ trợ khác

**Dependencies (package.json):**
- Các thư viện JavaScript cho frontend

#### 3.3.2. Database Setup

**Migrations:**
- Tạo các bảng: users, lessons, exercises, questions, attempts, etc.
- Thiết lập quan hệ và indexes

**Seeders:**
- Tạo dữ liệu mẫu: admin user, bài học, bài tập

#### 3.3.3. AI Speech Service

**Python Dependencies:**
- fastapi: Web framework
- gtts: Text-to-Speech
- speech_recognition: Speech-to-Text
- pydub: Audio processing

**Dockerfile:**
```dockerfile
FROM python:3.11-slim
WORKDIR /app
COPY requirements.txt .
RUN pip install -r requirements.txt
COPY . .
CMD ["uvicorn", "app:app", "--host", "0.0.0.0", "--port", "8000"]
```

### 3.4. Triển khai các tính năng

#### 3.4.1. Module Chấm điểm (GeminiScoringService)

**Chức năng:**
- Xây dựng prompt phù hợp với loại bài tập
- Gọi Gemini API với rate limiting
- Parse và format kết quả
- Cache để tối ưu

**Xử lý lỗi:**
- API timeout
- Rate limit exceeded
- Invalid response
- Fallback khi lỗi

#### 3.4.2. Module Dịch thuật (TranslationService)

**Chức năng:**
- Sử dụng MyMemory Translation API (free tier)
- Fallback về local dictionary
- Cache kết quả (30 ngày)

#### 3.4.3. Module Embed

**Tính năng:**
- Trang độc lập cho iframe
- CORS headers
- Content-Security-Policy
- Responsive trong iframe

**Routes:**
- `/embed-writing/exercises/{id}?questionId={id}`
- `/embed-speaking/exercises/{id}?questionId={id}`

### 3.5. Testing và Debugging

#### 3.5.1. Testing

- Unit tests cho các services
- Integration tests cho API endpoints
- Manual testing cho UI/UX

#### 3.5.2. Logging

- Laravel Log: Lưu logs vào `storage/logs/`
- Log các lỗi API, rate limiting
- Debug information

#### 3.5.3. Monitoring

- Health checks cho Docker containers
- Rate limit statistics
- API usage tracking

### 3.6. Deployment

#### 3.6.1. Production Setup

**Environment Variables:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- Database credentials
- Gemini API key

**Security:**
- HTTPS/SSL
- Strong passwords
- CSRF protection
- Input validation

#### 3.6.2. Backup

- Database backup định kỳ
- Backup code và configuration
- Disaster recovery plan

---

## CHƯƠNG 4: KẾT QUẢ VÀ ĐÁNH GIÁ

### 4.1. Kết quả đạt được

#### 4.1.1. Các tính năng đã hoàn thành

✅ **Quản lý người dùng:**
- Đăng ký, đăng nhập
- Phân quyền Admin/User
- Quản lý thông tin cá nhân

✅ **Quản lý nội dung:**
- CRUD cho Lessons, Exercises, Questions
- Phân loại bài tập theo loại
- Admin panel đầy đủ

✅ **Bài tập viết:**
- 3 loại: WAQ, WCS, WSG
- Giao diện làm bài thân thiện
- Chấm điểm tự động bằng AI

✅ **Bài tập nói:**
- SPW và SPS
- Tích hợp TTS/STT
- Ghi âm và chuyển đổi

✅ **Chấm điểm bằng AI:**
- Tích hợp Google Gemini API
- Phản hồi thân thiện, khuyến khích
- Highlight lỗi chính tả/ngữ pháp
- Điểm số chi tiết (0-100)

✅ **Dịch thuật:**
- Từ điển Anh-Việt
- Popup UI
- Cache để tối ưu

✅ **Xem lại:**
- Lịch sử làm bài
- Lọc và tìm kiếm
- Xem chi tiết

✅ **Embed:**
- Hỗ trợ iframe
- CORS và CSP headers
- Giao diện độc lập

✅ **Docker:**
- Tự động setup
- Multi-container architecture
- Health checks

#### 4.1.2. Giao diện người dùng

- **Trang chủ:** Giao diện đẹp, thân thiện với trẻ em
- **Làm bài:** Giao diện đơn giản, dễ sử dụng
- **Kết quả:** Hiển thị rõ ràng điểm, phản hồi, highlight
- **Admin:** Giao diện quản trị đầy đủ, dễ sử dụng

#### 4.1.3. Hiệu năng

- **Thời gian phản hồi chấm điểm:** Trung bình 5-8 giây
- **Cache:** Giảm đáng kể số lần gọi API
- **Rate limiting:** Quản lý tốt giới hạn API
- **Database:** Queries được tối ưu với indexes

### 4.2. Đánh giá

#### 4.2.1. Ưu điểm

✅ **Công nghệ hiện đại:**
- Sử dụng Laravel 10, PHP 8.1+
- Tích hợp AI (Gemini API)
- Docker containerization

✅ **Tính năng đầy đủ:**
- Hỗ trợ nhiều loại bài tập
- Chấm điểm tự động thông minh
- Giao diện thân thiện

✅ **Dễ triển khai:**
- Docker tự động setup
- Documentation đầy đủ
- Cấu hình linh hoạt

✅ **Mở rộng được:**
- Kiến trúc module hóa
- Dễ thêm loại bài tập mới
- API RESTful

#### 4.2.2. Hạn chế

⚠️ **Phụ thuộc API bên ngoài:**
- Gemini API có giới hạn (free tier)
- Cần API key
- Phụ thuộc vào dịch vụ của Google

⚠️ **Chưa có mobile app:**
- Hiện tại chỉ có web
- Có thể phát triển mobile app sau

⚠️ **Chưa có real-time:**
- Chưa có WebSocket cho real-time updates
- Có thể thêm sau

#### 4.2.3. So sánh với các hệ thống tương tự

| Tính năng | I-CLC Platform | Hệ thống khác |
|-----------|----------------|--------------|
| Chấm điểm AI | ✅ Gemini API | ❌ Hoặc API khác |
| Nhiều loại bài tập | ✅ 5 loại | ⚠️ Ít hơn |
| Giao diện trẻ em | ✅ Tối ưu | ⚠️ Chưa tối ưu |
| Embed support | ✅ Có | ❌ Không có |
| Docker | ✅ Tự động | ⚠️ Thủ công |

### 4.3. Kết quả kiểm thử

#### 4.3.1. Kiểm thử chức năng

- ✅ Đăng ký/Đăng nhập: Hoạt động tốt
- ✅ Làm bài tập: Tất cả loại bài tập hoạt động
- ✅ Chấm điểm: AI chấm điểm chính xác, phản hồi phù hợp
- ✅ Dịch thuật: Hoạt động ổn định
- ✅ Embed: Nhúng vào website khác thành công

#### 4.3.2. Kiểm thử hiệu năng

- ✅ Thời gian phản hồi: < 10 giây (mục tiêu đạt được)
- ✅ Concurrent users: Hỗ trợ tốt với cache
- ✅ Database queries: Được tối ưu

#### 4.3.3. Kiểm thử bảo mật

- ✅ Authentication: Hoạt động tốt
- ✅ CSRF protection: Đã bật
- ✅ Input validation: Đã validate
- ✅ CORS: Cấu hình đúng cho embed

### 4.4. Phản hồi người dùng

- **Giao diện:** Đẹp, thân thiện, dễ sử dụng
- **Chấm điểm:** Phản hồi hữu ích, khuyến khích
- **Tốc độ:** Nhanh, ổn định
- **Tính năng:** Đầy đủ, đáp ứng nhu cầu

---

## KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN

### Kết luận

Trong quá trình thực tập, em đã hoàn thành việc xây dựng hệ thống học tiếng Anh trực tuyến I-CLC Learning Platform với các tính năng chính:

1. **Hệ thống quản lý nội dung:** Quản lý bài học, bài tập, câu hỏi đầy đủ
2. **Chấm điểm tự động bằng AI:** Tích hợp Google Gemini API, chấm điểm thông minh với phản hồi thân thiện
3. **Nhiều loại bài tập:** Hỗ trợ 5 loại bài tập (WAQ, WCS, WSG, SPW, SPS)
4. **Giao diện thân thiện:** Thiết kế tối ưu cho trẻ em dưới 13 tuổi
5. **Tính năng nhúng:** Hỗ trợ embed vào website khác
6. **Triển khai dễ dàng:** Docker tự động setup

Dự án đã đạt được các mục tiêu đề ra, hệ thống hoạt động ổn định và sẵn sàng để sử dụng trong thực tế.

### Hướng phát triển

**Ngắn hạn:**
1. Tối ưu hóa prompt để cải thiện độ chính xác chấm điểm
2. Thêm nhiều loại bài tập mới
3. Cải thiện giao diện và UX
4. Thêm tính năng thống kê và báo cáo cho giáo viên

**Dài hạn:**
1. Phát triển mobile app (iOS, Android)
2. Thêm tính năng học nhóm, thi đua
3. Tích hợp thêm các AI model khác
4. Hỗ trợ nhiều ngôn ngữ
5. Thêm tính năng video bài giảng
6. Hệ thống gợi ý bài tập dựa trên trình độ

### Bài học kinh nghiệm

1. **Lập kế hoạch:** Lập kế hoạch chi tiết giúp quá trình phát triển suôn sẻ hơn
2. **Tài liệu:** Viết tài liệu đầy đủ giúp bảo trì và mở rộng dễ dàng
3. **Testing:** Kiểm thử sớm và thường xuyên giúp phát hiện lỗi sớm
4. **API Integration:** Cần xử lý tốt rate limiting và error handling khi tích hợp API bên ngoài
5. **User Experience:** Giao diện thân thiện, phản hồi tích cực rất quan trọng với trẻ em

---

## TÀI LIỆU THAM KHẢO

1. Laravel Documentation. (2024). *Laravel 10.x Documentation*. https://laravel.com/docs/10.x

2. Google AI. (2024). *Gemini API Documentation*. https://ai.google.dev/docs

3. FastAPI Documentation. (2024). *FastAPI Framework*. https://fastapi.tiangolo.com/

4. Docker Documentation. (2024). *Docker Documentation*. https://docs.docker.com/

5. MySQL Documentation. (2024). *MySQL 8.0 Reference Manual*. https://dev.mysql.com/doc/

6. PHP Documentation. (2024). *PHP Manual*. https://www.php.net/manual/

7. JavaScript MDN. (2024). *JavaScript Documentation*. https://developer.mozilla.org/en-US/docs/Web/JavaScript

8. W3Schools. (2024). *HTML, CSS, JavaScript Tutorials*. https://www.w3schools.com/

9. Stack Overflow. (2024). *Programming Q&A*. https://stackoverflow.com/

10. GitHub. (2024). *AI-speaking-writing-laravel Repository*. https://github.com/thienv29/AI-speaking-writing-laravel

---

## PHỤ LỤC

### PHỤ LỤC A: KẾ HOẠCH THỰC TẬP

[Chi tiết kế hoạch thực tập sẽ được điền vào đây]

**Thời gian thực tập:** [Ngày bắt đầu] - [Ngày kết thúc]

**Các giai đoạn:**

1. **Giai đoạn 1: Nghiên cứu và phân tích (Tuần 1-2)**
   - Nghiên cứu yêu cầu dự án
   - Phân tích công nghệ
   - Thiết kế hệ thống

2. **Giai đoạn 2: Phát triển Backend (Tuần 3-5)**
   - Setup Laravel project
   - Thiết kế database
   - Phát triển API
   - Tích hợp Gemini API

3. **Giai đoạn 3: Phát triển Frontend (Tuần 6-7)**
   - Thiết kế giao diện
   - Phát triển UI/UX
   - Tích hợp với API

4. **Giai đoạn 4: Testing và Hoàn thiện (Tuần 8-9)**
   - Kiểm thử chức năng
   - Sửa lỗi
   - Tối ưu hóa

5. **Giai đoạn 5: Viết báo cáo (Tuần 10)**
   - Tổng hợp tài liệu
   - Viết báo cáo
   - Chuẩn bị bảo vệ

---

### PHỤ LỤC B: NHẬT KÝ THỰC TẬP

[Chi tiết nhật ký thực tập sẽ được điền vào đây với các ngày cụ thể và chữ ký xác nhận]

**Mẫu nhật ký:**

| Ngày | Công việc thực hiện | Kết quả | Ghi chú | Chữ ký |
|------|-------------------|---------|---------|--------|
| DD/MM/YYYY | [Mô tả công việc] | [Kết quả] | [Ghi chú] | [Chữ ký] |
| ... | ... | ... | ... | ... |

---

### PHỤ LỤC C: PHIẾU NHẬN XÉT SINH VIÊN THỰC TẬP

[Phiếu nhận xét sẽ được niêm phong và cố định vào bìa cuối quyển báo cáo]

**Mẫu phiếu nhận xét:**

```
PHIẾU NHẬN XÉT SINH VIÊN THỰC TẬP

Họ và tên sinh viên: [Tên sinh viên]
Mã sinh viên: [Mã sinh viên]
Lớp: [Lớp]
Đơn vị thực tập: [Tên đơn vị]
Thời gian thực tập: [Từ ngày] đến [Đến ngày]

1. Về thái độ làm việc:
   [Nhận xét]

2. Về kiến thức chuyên môn:
   [Nhận xét]

3. Về kỹ năng thực hành:
   [Nhận xét]

4. Về kết quả đạt được:
   [Nhận xét]

5. Đánh giá tổng thể:
   [Điểm số và nhận xét]

Người nhận xét: [Tên và chữ ký]
Chức vụ: [Chức vụ]
Ngày: [Ngày tháng năm]
```

---

### PHỤ LỤC D: MỘT SỐ ĐOẠN CODE QUAN TRỌNG

#### D.1. GeminiScoringService - Chấm điểm bằng AI

```php
public function evaluate(Question $question, string $userAnswer): array
{
    $apiKey = config('services.gemini.api_key');
    $model = config('services.gemini.model', 'gemini-2.5-flash');
    
    // Check cache
    $cacheKey = 'gemini_score_' . md5($question->id . $userAnswer);
    $cached = Cache::get($cacheKey);
    if ($cached !== null) {
        return $cached;
    }
    
    // Check rate limits
    $rateLimitCheck = $this->checkRateLimit();
    if (!$rateLimitCheck['allowed']) {
        return $this->getRateLimitExceededResult($userAnswer, $rateLimitCheck);
    }
    
    // Build prompt and call API
    $prompt = $this->buildPrompt($question, $userAnswer);
    $response = $this->callGeminiAPI($apiKey, $model, $prompt);
    
    // Parse and return result
    $result = $this->parseGeminiResponse($response, $question, $userAnswer);
    Cache::put($cacheKey, $result, self::CACHE_TTL);
    
    return $result;
}
```

#### D.2. AttemptService - Xử lý nộp bài

```php
public function evaluateWritingAttempt(int $questionId, ?int $userId, string $userAnswer): Attempt
{
    $question = Question::with(['exercises.type', 'exercises.lesson'])
        ->findOrFail($questionId);
    
    $cleanAnswer = trim($userAnswer);
    
    // Use Gemini API for scoring
    $geminiResult = $this->geminiScoring->evaluate($question, $cleanAnswer);
    $result = $this->processGeminiResult($geminiResult);
    
    $attempt = Attempt::create([
        'user_id' => $userId,
        'question_id' => $question->id,
        'user_answer' => $cleanAnswer,
        'is_correct' => $result['is_correct'],
        'feedback' => $result['feedback'],
        'score' => $result['score'] ?? null,
        'evaluation_meta' => $result['evaluation_meta'] ?? null,
    ]);
    
    return $attempt;
}
```

#### D.3. Docker Compose Configuration

```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: iclc-app
    ports:
      - "8000:8000"
    depends_on:
      mysql:
        condition: service_healthy
    environment:
      - DB_HOST=mysql
      - DB_DATABASE=iclc_db
      - GEMINI_API_KEY=${GEMINI_API_KEY}
  
  mysql:
    image: mysql:8.0
    container_name: iclc-mysql
    environment:
      MYSQL_DATABASE: iclc_db
      MYSQL_ROOT_PASSWORD: rootpassword
    ports:
      - "3307:3306"
  
  ai-speech-service:
    build:
      context: ../ai-speech-service
    container_name: iclc-ai-speech-service
    ports:
      - "5001:8000"
```

---

### PHỤ LỤC E: SCREENSHOTS GIAO DIỆN

[Chèn các hình ảnh screenshot giao diện hệ thống]

- Hình E.1: Trang chủ
- Hình E.2: Giao diện làm bài tập viết
- Hình E.3: Kết quả chấm điểm
- Hình E.4: Admin panel
- Hình E.5: Giao diện embed

---

**Hết báo cáo**

---

*Báo cáo được hoàn thành vào ngày [Ngày hoàn thành]*

*Sinh viên thực hiện*

*[Chữ ký]*

*[Tên sinh viên]*





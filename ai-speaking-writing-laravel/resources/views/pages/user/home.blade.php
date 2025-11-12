@extends('layouts.app')

@section('meta')
<!-- Primary Meta Tags -->
<title>I-CLC Speaking & Writing Learning </title>
<meta name="title" content="I-CLC Learning Playground - Khơi dậy đam mê tiếng Anh cùng I-CLC">
<meta name="description" content="Sân chơi tiếng Anh dành riêng cho bé. 18 năm kinh nghiệm đào tạo tiếng Anh quốc tế. Phương pháp CCM & CLIL từ Trung tâm ngoại ngữ Liên Lục Địa. Bé vừa học vừa vui, tự tin chinh phục mọi kỹ năng tiếng Anh.">
<meta name="keywords" content="I-CLC, học tiếng Anh cho bé, tiếng Anh mầm non, luyện viết tiếng Anh, CCM CLIL, Cambridge, trung tâm ngoại ngữ Liên Lục Địa">
<meta name="author" content="I-CLC - Inter-Continental Language Center">
<meta name="robots" content="index, follow">
<meta name="language" content="Vietnamese">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://i-clc.edu.vn/">
<meta property="og:title" content="I-CLC Learning Playground - Khơi dậy đam mê tiếng Anh cùng I-CLC">
<meta property="og:description" content="Sân chơi tiếng Anh dành riêng cho bé. 18 năm kinh nghiệm đào tạo tiếng Anh quốc tế. Phương pháp CCM & CLIL từ Trung tâm ngoại ngữ Liên Lục Địa.">
<meta property="og:image" content="/assets/images/logo.png">
<meta property="og:site_name" content="I-CLC Learning Playground">
<meta property="og:locale" content="vi_VN">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://i-clc.edu.vn/">
<meta property="twitter:title" content="I-CLC Learning Playground - Khơi dậy đam mê tiếng Anh cùng I-CLC">
<meta property="twitter:description" content="Sân chơi tiếng Anh dành riêng cho bé. 18 năm kinh nghiệm đào tạo tiếng Anh quốc tế. Phương pháp CCM & CLIL từ Trung tâm ngoại ngữ Liên Lục Địa.">
<meta property="twitter:image" content="/assets/images/logo.png">
@endsection

{{-- @push('styles')
<link rel="stylesheet" href="/css/home.css">
@endpush --}}

@section('content')
<section class="hero">
    <div>
        <span class="tagline">🌟 Hành trình 18 năm đào tạo tiếng anh quốc tế</span>
        <h1 class="hero-title">Khơi dậy đam mê tiếng Anh cùng <span>I-CLC</span></h1>
        <p class="hero-desc">
            Các bài học được thiết kế theo phương pháp CCM & CLIL từ Trung tâm ngoại ngữ Liên Lục Địa.
            Bé vừa học vừa vui, tự tin chinh phục mọi kỹ năng tiếng Anh.
        </p>
        <div class="hero-actions">
            <a class="cta-btn" href="/lessons">Luyện tiếng Anh ngay</a>
            <a class="progress-cta" href="#contact">Tư vấn ngay</a>
        </div>
    </div>
    <div class="hero-card">
        <div class="hero-photo">
            <img src="/assets/images/home-kids-3.png" alt="I-CLC Kids">
        </div>
        <h3>Sân chơi tiếng Anh dành riêng cho bé</h3>
        <p>
            - Bộ sưu tập bài tập viết tương tác theo từng chủ đề học thuật.<br>
            - Thống kê tiến độ, sticker thưởng, lộ trình rõ ràng.
        </p>
    </div>
</section>

<section id="features" class="info-section">
    <div class="section-title"> Vì sao chọn I-CLC?</div>
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">💡</div>
            <div class="feature-title">Chương trình chuẩn quốc tế</div>
            <div class="feature-desc">Áp dụng phương pháp CCM & CLIL giúp bé hình thành tư duy ngôn ngữ tự nhiên và phản xạ nhanh.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🧠</div>
            <div class="feature-title">Nền tảng trực tuyến thông minh</div>
            <div class="feature-desc">AI gợi ý, highlight phần cần cải thiện, hiệu ứng khen thưởng giúp bé hứng thú khi luyện tập.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🎓</div>
            <div class="feature-title">Đội ngũ giáo viên tận tâm</div>
            <div class="feature-desc">Giáo viên bản ngữ & Việt Nam kết hợp, đồng hành trong mọi khóa học từ mầm non đến thiếu niên.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🏆</div>
            <div class="feature-title">Thành tích tự hào</div>
            <div class="feature-desc">18 năm kinh nghiệm, hơn 2400 gia đình tin chọn. Bé được khuyến khích thể hiện bản thân mỗi ngày.</div>
        </div>
    </div>
</section>


<section id="progress" class="info-section">
    <div class="progress-banner">
        <div>
            <h3>Mỗi bài luyện là một bước tiến ✨</h3>
            <p class="progress-desc">Hệ thống sẽ gửi sticker, pháo bông và feedback rõ ràng giúp bé biết mình giỏi ở đâu, cần cố gắng gì thêm.</p>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section id="contact" class="contact-section">
    <div class="section-title">📧 Đăng ký tư vấn miễn phí</div>
    <div class="contact-container">
        <div class="contact-info">
            <h3>Liên hệ với chúng tôi</h3>
            <p>Để được tư vấn chương trình học phù hợp nhất cho bé, vui lòng điền form bên cạnh hoặc liên hệ trực tiếp:</p>
            <div class="contact-details">
                <div class="contact-item">
                    <span class="contact-icon">
                        <img src="/assets/images/Phone_fill-1.png" alt="Phone" class="contact-icon-img">
                    </span>
                    <div>
                        <strong>Hotline</strong>
                        <p>091 772 9932</p>
                    </div>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">
                        <img src="/assets/images/Message_fill-1.png" alt="Email" class="contact-icon-img">
                    </span>
                    <div>
                        <strong>Email</strong>
                        <p>info@i-clc.edu.vn</p>
                    </div>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">
                        <img src="/assets/images/Pin_alt_fill-2.png" alt="Location" class="contact-icon-img">
                    </span>
                    <div>
                        <strong>Địa chỉ</strong>
                        <p>64 đường 85, Quận 7, TP. HCM</p>
                    </div>
                </div>
            </div>
        </div>
        <form class="contact-form" id="contactForm">
            <div class="form-group">
                <label for="contactName">Họ và tên phụ huynh *</label>
                <input type="text" id="contactName" name="name" required placeholder="Nguyễn Văn A">
            </div>
            <div class="form-group">
                <label for="contactPhone">Số điện thoại *</label>
                <input type="tel" id="contactPhone" name="phone" required placeholder="091 772 9932">
            </div>
            <div class="form-group">
                <label for="contactEmail">Email *</label>
                <input type="email" id="contactEmail" name="email" required placeholder="example@email.com">
            </div>
            <div class="form-group">
                <label for="contactChildAge">Độ tuổi của bé</label>
                <select id="contactChildAge" name="child_age">
                    <option value="">Chọn độ tuổi</option>
                    <option value="3-5">3-5 tuổi (Mầm non)</option>
                    <option value="6-8">6-8 tuổi (Tiểu học)</option>
                    <option value="9-12">9-12 tuổi (Thiếu niên)</option>
                    <option value="13+">13+ tuổi</option>
                </select>
            </div>
            <div class="form-group">
                <label for="contactMessage">Ghi chú (tùy chọn)</label>
                <textarea id="contactMessage" name="message" rows="4" placeholder="Bạn có thể chia sẻ thêm về nhu cầu học tập của bé..."></textarea>
            </div>
            <button type="submit" class="cta-btn">
                 Gửi đăng ký
            </button>
            <div class="form-message" id="formMessage"></div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
    <script>
        // Contact form handling
        const contactForm = document.getElementById('contactForm');
        const formMessage = document.getElementById('formMessage');
        
        if (contactForm) {
            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Đang gửi...';
                formMessage.style.display = 'none';
                
                try {
                    await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate API call
                    formMessage.className = 'form-message success';
                    formMessage.textContent = '✅ Cảm ơn bạn đã đăng ký! Chúng tôi sẽ liên hệ lại sớm nhất có thể.';
                    formMessage.style.display = 'block';
                    contactForm.reset();
                    formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } catch (error) {
                    formMessage.className = 'form-message error';
                    formMessage.textContent = '❌ Có lỗi xảy ra. Vui lòng thử lại sau hoặc liên hệ trực tiếp qua hotline.';
                    formMessage.style.display = 'block';
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }
    </script>
@endpush

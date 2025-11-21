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

@section('topbar')
<div class="topbar">
    <div class="topbar-wrapper">
        <div class="topbar-links">
            <a href="#">Câu lạc bộ tiếng Anh miễn phí</a>
            <a href="#">Thi thử Cambridge</a>
            <a href="#">Ebook miễn phí</a>
        </div>
        <div class="topbar-phone">
            <img src="/assets/images/Phone_fill-1.png" alt="Phone" class="topbar-phone-icon">
            <span>091 772 9932</span>
        </div>
    </div>
</div>
@endsection

@section('navigation')
        <a href="#features">Chương trình</a>
        <a href="#contact">Liên hệ</a>
    <a class="cta-btn" href="/writing">
        Luyện viết
        <span class="btn-kids-decoration">
            <img src="/assets/images/home-kids-1.png" alt="Kids" class="btn-kids-image">
        </span>
    </a>
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
            <a class="cta-btn" href="/writing">Luyện viết ngay</a>
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

@section('footer')
<footer>
    <div class="footer-main">
        <div class="footer-grid">
            <!-- Left Column - Company Info -->
            <div class="footer-column">
                <div class="footer-logo">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="I-CLC Logo" class="footer-logo-img">
                    <div class="logo-text">Continental Language Center</div>
                </div>
                <div class="company-name">
                    <strong>Trung Tâm Ngoại Ngữ Liên Lục Địa (I-CLC)</strong><br>
                    Inter-Continental Language Center
                </div>
                <div class="company-info">
                    <p>Được thành lập: 18/05/2016</p>
                    <p>Giấy phép hoạt động giáo dục số 3701/GDĐT-TC do Sở GD&ĐT TP. HCM cấp ngày 14/06/2016</p>
                    <p>Giấy phép dạy học trực tuyến số 34/QĐ-SGDĐT do Sở GD&ĐT TP. HCM cấp ngày 31/03/2021</p>
                    <p class="company-owner">Trực thuộc CÔNG TY TNHH THƯƠNG MẠI VÀ DỊCH VỤ TRỊ LINH</p>
                    <p>Địa chỉ ĐKKD: 1116A Quang Trung, P.8, Q. Gò Vấp, TP HCM</p>
                    <p>Mã số doanh nghiệp: 0304834511 do Sở kế hoạch & Đầu tư TPHCM cấp lần đầu ngày 29/01/2007</p>
                </div>
                <div class="online-badge">
                    <div class="badge-icon">✓</div>
                    <span>ĐÃ THÔNG BÁO BỘ CÔNG THƯƠNG</span>
                </div>
                <div class="social-links">
                    <a href="https://www.facebook.com/iclc1" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="social-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/iclc_edu/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="social-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <a href="https://zalo.me/343143743666884973" target="_blank" rel="noopener noreferrer" aria-label="Zalo" class="social-link">
                        <svg width="20" height="20" viewBox="0 0 48 48" fill="currentColor">
                            <path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm10.5 14.5h-8l8 11h-13v-3h8l-8-11h13v3z"/>
                        </svg>
                    </a>
                    <a href="https://www.youtube.com/@iclctrungtamngoaingu" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="social-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Middle Column - Menu -->
            <div class="footer-column">
                <div class="footer-title">MENU</div>
                <nav class="footer-menu">
                    <a href="/" class="footer-link">Trang chủ</a>
                    <a href="#features" class="footer-link">Chương trình học</a>
                    <a href="#" class="footer-link">Blog</a>
                    <a href="#" class="footer-link">Chính sách bảo vệ thông tin cá nhân</a>
                    <a href="#contact" class="footer-link">Liên hệ</a>
                    <a href="#" class="footer-link">Giới thiệu</a>
                    <a href="#" class="footer-link">Tài liệu</a>
                </nav>
            </div>

            <!-- Right Column - Contact -->
            <div class="footer-column">
                <div class="footer-title">LIÊN HỆ</div>
                <div class="contact-info-footer">
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">
                            <img src="/assets/images/Message_fill-1.png" alt="Email" class="contact-icon-img-footer">
                        </span>
                        <span>info@i-clc.edu.vn</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">
                            <img src="/assets/images/Phone_fill-1.png" alt="Phone" class="contact-icon-img-footer">
                        </span>
                        <span>091 772 9932</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">
                            <img src="/assets/images/Pin_alt_fill-2.png" alt="Location" class="contact-icon-img-footer">
                        </span>
                        <span>64 đường 85, Phường Tân Hưng, TP. HCM</span>
                    </div>
                </div>

                <div class="footer-title" style="margin-top: 30px;">VĂN PHÒNG ĐẠI DIỆN</div>
                <div class="contact-info-footer">
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">
                            <img src="/assets/images/Message_fill-1.png" alt="Email" class="contact-icon-img-footer">
                        </span>
                        <span>info@i-clc.edu.vn</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">
                            <img src="/assets/images/Phone_fill-1.png" alt="Phone" class="contact-icon-img-footer">
                        </span>
                        <span>(028) 38303012/ 13</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">
                            <img src="/assets/images/Pin_alt_fill-2.png" alt="Location" class="contact-icon-img-footer">
                        </span>
                        <span>438 Điện Biên Phủ, Phường Vườn Lài, TP. HCM</span>
                    </div>
                </div>

                <div class="footer-title" style="margin-top: 30px;">HỆ THỐNG CHI NHÁNH</div>
                <div class="branch-list">
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">
                            <img src="/assets/images/Pin_alt_fill-2.png" alt="Location" class="contact-icon-img-footer">
                        </span>
                        <span>64 đường 85, Phường Tân Hưng, TP. HCM</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">
                            <img src="/assets/images/Pin_alt_fill-2.png" alt="Location" class="contact-icon-img-footer">
                        </span>
                        <span>Lô số 12, Đường B2, KĐT Vĩnh Điềm Trung, Phường Tây Nha Trang, Khánh Hòa</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">
                            <img src="/assets/images/Pin_alt_fill-2.png" alt="Location" class="contact-icon-img-footer">
                        </span>
                        <span>8907 Warner Ave #135, Huntington Beach, CA 92647</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright">
        © {{ date('Y') }} I-CLC Learning Playground. Designed with love by the internship team.
    </div>
</footer>
@endsection

@section('scrollToTop')
<button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
    ↑
</button>
@endsection

@push('scripts')
<script>
    // Scroll to top button
    const scrollToTopBtn = document.getElementById('scrollToTop');
    
    if (scrollToTopBtn) {
        window.addEventListener('scroll', () => {
            scrollToTopBtn.classList.toggle('visible', window.scrollY > 300);
        });

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

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

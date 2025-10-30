@extends('layouts.app')

@section('meta')
<!-- Primary Meta Tags -->
<title>I-CLC Learning Playground - Khơi dậy đam mê tiếng Anh cùng I-CLC</title>
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
    <a href="#"> Câu lạc bộ tiếng Anh miễn phí</a>
    <a href="#"> Thi thử Cambridge</a>
    <a href="#"> Ebook miễn phí</a>
    <span>📞 091 772 9932</span>
</div>
@endsection

@section('navigation')
    <a href="#features">Chương trình</a>
    <a href="#lessons">Bài học</a>
    <a href="#contact">Liên hệ</a>
    <a class="cta-btn" href="/writing">
        Luyện viết
        <span class="btn-kids-decoration">
            <img src="/assets/images/home-kids-1.png" alt="Kids" class="btn-kids-image">
        </span>
    </a>
@endsection

@push('styles')
<link rel="stylesheet" href="/css/home.css">
@endpush

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
            <a class="cta-btn" href="/writing/question/1">Luyện viết ngay</a>
            <a class="progress-cta" href="#lessons">Tư vấn ngay</a>
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

<section id="lessons" class="lesson-strip">
    <div class="section-title">📘 Các hành trình học nổi bật</div>
    <div class="lesson-list" id="lessonList">
        <!-- Loading skeleton -->
        <div class="lesson-card-skeleton">
            <div class="skeleton-tag"></div>
            <div class="skeleton-img"></div>
            <div class="skeleton-title"></div>
            <div class="skeleton-desc"></div>
        </div>
        <div class="lesson-card-skeleton">
            <div class="skeleton-tag"></div>
            <div class="skeleton-img"></div>
            <div class="skeleton-title"></div>
            <div class="skeleton-desc"></div>
        </div>
        <div class="lesson-card-skeleton">
            <div class="skeleton-tag"></div>
            <div class="skeleton-img"></div>
            <div class="skeleton-title"></div>
            <div class="skeleton-desc"></div>
        </div>
    </div>
</section>

<section id="progress" class="info-section">
    <div class="progress-banner">
        <div>
            <h3>Mỗi bài luyện là một bước tiến ✨</h3>
            <p class="progress-desc">Hệ thống sẽ gửi sticker, pháo bông và feedback rõ ràng giúp bé biết mình giỏi ở đâu, cần cố gắng gì thêm.</p>
        </div>
        <a class="progress-cta" href="/writing">
            🚀 Luyện viết ngay hôm nay
        </a>
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
                    <span class="contact-icon">📞</span>
                    <div>
                        <strong>Hotline</strong>
                        <p>091 772 9932</p>
                    </div>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">📧</span>
                    <div>
                        <strong>Email</strong>
                        <p>info@i-clc.edu.vn</p>
                    </div>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">📍</span>
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
                    <a href="https://facebook.com/i-clc" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="social-link">
                        <span>f</span>
                    </a>
                    <a href="https://instagram.com/i-clc" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="social-link">
                        <span>📷</span>
                    </a>
                    <a href="https://zalo.me/0917729932" target="_blank" rel="noopener noreferrer" aria-label="Zalo" class="social-link">
                        <span>💬</span>
                    </a>
                    <a href="https://youtube.com/@i-clc" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="social-link">
                        <span>▶</span>
                    </a>
                </div>
            </div>

            <!-- Middle Column - Menu -->
            <div class="footer-column">
                <div class="footer-title">MENU</div>
                <nav class="footer-menu">
                    <a href="/" class="footer-link">Trang chủ</a>
                    <a href="#lessons" class="footer-link">Chương trình học</a>
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
                        <span class="contact-icon-footer">📧</span>
                        <span>info@i-clc.edu.vn</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">📞</span>
                        <span>091 772 9932</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">📍</span>
                        <span>64 đường 85, Phường Tân Hưng, TP. HCM</span>
                    </div>
                </div>

                <div class="footer-title" style="margin-top: 30px;">VĂN PHÒNG ĐẠI DIỆN</div>
                <div class="contact-info-footer">
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">📧</span>
                        <span>info@i-clc.edu.vn</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">📞</span>
                        <span>(028) 38303012/ 13</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">📍</span>
                        <span>438 Điện Biên Phủ, Phường Vườn Lài, TP. HCM</span>
                    </div>
                </div>

                <div class="footer-title" style="margin-top: 30px;">HỆ THỐNG CHI NHÁNH</div>
                <div class="branch-list">
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">📍</span>
                        <span>64 đường 85, Phường Tân Hưng, TP. HCM</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">📍</span>
                        <span>Lô số 12, Đường B2, KĐT Vĩnh Điềm Trung, Phường Tây Nha Trang, Khánh Hòa</span>
                    </div>
                    <div class="contact-item-footer">
                        <span class="contact-icon-footer">📍</span>
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

    // Load lessons dynamically
    async function loadLessons() {
        const lessonList = document.getElementById('lessonList');
        const levelMap = {
            'Beginner': { emoji: '🥇', color: 'rgba(46, 204, 113, 0.15)', textColor: 'var(--green)' },
            'Explorer': { emoji: '🥈', color: 'rgba(52, 152, 219, 0.15)', textColor: 'var(--blue)' },
            'Advanced': { emoji: '🥉', color: 'rgba(142, 68, 173, 0.15)', textColor: '#8e44ad' },
            'Intermediate': { emoji: '⭐', color: 'rgba(245, 193, 44, 0.15)', textColor: 'var(--yellow)' }
        };
        
        try {
            const response = await fetch('/api/lessons');
            const lessons = await response.json();
            
            if (!lessons || lessons.length === 0) {
                throw new Error('No lessons found');
            }
            
            lessonList.innerHTML = '';
            lessons.slice(0, 6).forEach((lesson, index) => {
                const levelInfo = levelMap[lesson.level] || { emoji: '📚', color: 'rgba(107, 44, 144, 0.15)', textColor: 'var(--purple)' };
                const imgUrl = lesson.img_url || `/assets/images/home-lesson-${(index % 3) + 1}.png`;
                
                // Get first question ID from exercises
                let firstQuestionId = null;
                const firstExercise = lesson.exercises?.find(e => e.questions?.length > 0) || lesson.exercises?.[0];
                firstQuestionId = firstExercise?.questions?.[0]?.id;
                
                const startUrl = firstQuestionId ? `/writing/question/${firstQuestionId}` : '/writing';
                const lessonCard = document.createElement('article');
                lessonCard.className = 'lesson-card';
                lessonCard.innerHTML = `
                    <div class="lesson-tag" style="background:${levelInfo.color}; color:${levelInfo.textColor};">
                        ${levelInfo.emoji} ${lesson.level || 'General'}
                    </div>
                    <img src="${imgUrl}" alt="${lesson.title}" loading="lazy">
                    <h4>${lesson.title}</h4>
                    <p>${lesson.description || 'Khám phá bài học thú vị này và phát triển kỹ năng tiếng Anh của bạn.'}</p>
                    <a class="progress-cta" href="${startUrl}">Bắt đầu</a>
                `;
                lessonList.appendChild(lessonCard);
            });
        } catch (error) {
            console.error('Error loading lessons:', error);
            lessonList.innerHTML = '<p style="text-align:center;color:var(--purple);padding:40px;">Không thể tải danh sách bài học. Vui lòng thử lại sau.</p>';
        }
    }

    // Load lessons on page load
    loadLessons();

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

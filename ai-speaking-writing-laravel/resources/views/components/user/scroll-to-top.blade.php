<button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
    ↑
</button>

@push('scripts')
    <script>
        const scrollToTopBtn = document.getElementById('scrollToTop');
        
        if (scrollToTopBtn) {
            window.addEventListener('scroll', () => {
                scrollToTopBtn.classList.toggle('visible', window.scrollY > 300);
            });

            scrollToTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    </script>
@endPush
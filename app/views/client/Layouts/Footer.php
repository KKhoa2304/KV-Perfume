<footer class="site-footer">
    <div class="site-footer-inner">
        <div class="footer-col footer-about">
            <h3>KV PERFUME - LUXURY FRAGRANCE BOUTIQUE</h3>

            <p>
                KV Perfume là cửa hàng nước hoa cao cấp, chuyên cung cấp các dòng hương
                chính hãng, sang trọng và phù hợp với nhiều phong cách cá nhân.
            </p>

            <p><strong>Địa chỉ:</strong> 613 Âu Cơ, Phường Phú Trung, Quận Tân Phú, TP. Hồ Chí Minh</p>
            <p><strong>Điện thoại:</strong> 0869 058 393</p>
            <p><strong>Email:</strong> kvperfume.shop@gmail.com</p>
            <p><strong>Giờ hoạt động:</strong> 09:00 - 21:00 các ngày trong tuần</p>
        </div>

        <div class="footer-col">
            <h3>CHÍNH SÁCH</h3>
            <a href="#">Chính sách đổi trả</a>
            <a href="#">Chính sách vận chuyển</a>
            <a href="#">Chính sách bảo mật</a>
            <a href="#">Phương thức thanh toán</a>
            <a href="#">Điều khoản dịch vụ</a>
        </div>

        <div class="footer-col footer-map">
            <h3>ĐỊA CHỈ MAPS</h3>
            <iframe
                src="https://www.google.com/maps?q=613%20Au%20Co%2C%20Tan%20Phu%2C%20Ho%20Chi%20Minh&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© <?php echo date('Y'); ?> KV PERFUME. All rights reserved.</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // ==========================================
    // 1. XỬ LÝ GIAO DIỆN GIỎ HÀNG (CART DRAWER)
    // ==========================================
    const openCartBtn = document.getElementById('openCartBtn');
    const closeCartBtn = document.getElementById('closeCartBtn');
    const cartDrawer = document.getElementById('cart-drawer');
    const cartOverlay = document.getElementById('cartOverlay');

    function openCart() {
        if (cartDrawer) cartDrawer.classList.add('open');
        if (cartOverlay) cartOverlay.classList.add('active');
    }

    function closeCart() {
        if (cartDrawer) cartDrawer.classList.remove('open');
        if (cartOverlay) cartOverlay.classList.remove('active');

        // UX Helper: Xóa cờ 'open_cart' khỏi URL để tránh việc giỏ hàng tự mở lại khi người dùng tải lại trang (F5)
        const url = new URL(window.location.href);
        if (url.searchParams.has('open_cart')) {
            url.searchParams.delete('open_cart');
            window.history.replaceState({}, '', url.toString());
        }
    }

    if (openCartBtn) openCartBtn.addEventListener('click', openCart);
    if (closeCartBtn) closeCartBtn.addEventListener('click', closeCart);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

    // ==========================================
    // 2. XỬ LÝ HIỆU ỨNG BANNER (HERO SLIDER)
    // ==========================================
    const slider = document.querySelector('[data-hero-slider]');

    if (slider) {
        const slides = Array.from(slider.querySelectorAll('[data-hero-slide]'));
        const dots = Array.from(slider.querySelectorAll('[data-hero-dot]'));
        const prevBtn = slider.querySelector('[data-hero-prev]');
        const nextBtn = slider.querySelector('[data-hero-next]');

        let currentIndex = 0;
        let timer = null;

        function showSlide(index) {
            if (slides.length === 0) return;

            currentIndex = (index + slides.length) % slides.length;

            slides.forEach(function (slide, i) {
                slide.classList.toggle('active', i === currentIndex);
            });

            dots.forEach(function (dot, i) {
                dot.classList.toggle('active', i === currentIndex);
            });
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }

        // Tự động chuyển slide sau mỗi 4.5 giây để trang chủ trông sinh động hơn
        function startAutoSlide() {
            stopAutoSlide();
            if (slides.length > 1) {
                timer = setInterval(nextSlide, 4500);
            }
        }

        function stopAutoSlide() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                nextSlide();
                startAutoSlide();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                prevSlide();
                startAutoSlide();
            });
        }

        dots.forEach(function (dot, index) {
            dot.addEventListener('click', function () {
                showSlide(index);
                startAutoSlide();
            });
        });

        // UX Helper: Tạm dừng chuyển ảnh tự động khi người dùng di chuột vào để họ kịp đọc thông tin
        slider.addEventListener('mouseenter', stopAutoSlide);
        slider.addEventListener('mouseleave', startAutoSlide);

        // Khởi chạy trạng thái ban đầu
        showSlide(0);
        startAutoSlide();
    }
});
</script>
</body>
</html>
<?php
// home.php - YONEX Homepage with Image Carousel (Original Image Proportions)
// Features: 5 slides, left/right buttons, clickable dots, smooth infinite loop
// Images use their original aspect ratio (not forced to fill)
?>
<section class="hero">
    <div class="hero-overlay"></div>
    
    <!-- 轮播容器 -->
    <div class="carousel-container">
        <div class="carousel-track" id="carouselTrack">
            <!-- 5张图片 - 第一张保留原图，其余4张占位，您可自行替换 -->
            <div class="carousel-slide">
                <img src="images/badminton-hero.jpg" alt="YONEX Badminton Hero">
            </div>
            <div class="carousel-slide">
                <img src="images/badminton-hero1.jpg" alt="YONEX Action Shot">
            </div>
            <div class="carousel-slide">
                <img src="images/badminton-hero2.jpg" alt="YONEX Tournament">
            </div>
            <div class="carousel-slide">
                <img src="images/badminton-hero3.jpg" alt="YONEX Champions">
            </div>
            <div class="carousel-slide">
                <img src="images/badminton-hero4.jpg" alt="YONEX Equipment">
            </div>
        </div>
        
        <!-- 左右切换按钮 -->
        <button class="carousel-btn carousel-btn-prev" id="prevBtn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <button class="carousel-btn carousel-btn-next" id="nextBtn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
        
        <!-- 底部小点指示器 -->
        <div class="carousel-dots" id="carouselDots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>
    
    
</section>

<div class="promo-wrapper">
    <!-- Astrox 100ZZ -->
    <section class="promo-section" data-product="astrox">
        <div class="promo-badge">New Release</div>
        <div class="promo-text">
            <div class="promo-category">
                <span class="category-dot"></span>
                <span class="category-name">Astrox Series</span>
            </div>
            <h2 class="promo-name">Astrox <span>100ZZ</span></h2>
            <p class="promo-desc">Experience the ultimate power and control. Designed for advanced players seeking rapid fire attacks and solid feel upon impact. The hyper-slim shaft reduces air resistance for an ultra-fast swing.</p>
            <div class="promo-meta">
                <span class="meta-tag">⚡ Rotational Generator System</span>
                <span class="meta-tag">🎯 Nanometric NEO</span>
            </div>
            <a href="?id=5" class="btn-outline">Learn More 
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="promo-image">
            <div class="image-glow"></div>
            <img src="images/astrox100zz_kurenai.webp" alt="Astrox 100 ZZ">
            <div class="product-tag">Pro Spec</div>
        </div>
    </section>

    <!-- Power Cushion 65Z -->
    <section class="promo-section" data-product="footwear">
        <div class="promo-badge">Bestseller</div>
        <div class="promo-text">
            <div class="promo-category">
                <span class="category-dot"></span>
                <span class="category-name">Professional Footwear</span>
            </div>
            <h2 class="promo-name">Power Cushion <span>65Z</span></h2>
            <p class="promo-desc">Stay light on your feet with max shock absorption. The signature Power Cushion+ technology converts impact energy into dynamic repulsive power for your next step on the court.</p>
            <div class="promo-meta">
                <span class="meta-tag">🛡️ Power Cushion+</span>
                <span class="meta-tag">🎨 Double Russel Mesh</span>
            </div>
            <a href="?id=22" class="btn-outline">Learn More 
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="promo-image">
            <div class="image-glow"></div>
            <img src="images/Poer_Cushion_65Z_Wide.webp" alt="Power Cushion 65 Z4">
            <div class="product-tag">Wide Fit</div>
        </div>
    </section>

    <!-- Pro Racquet Bag -->
    <section class="promo-section" data-product="bag">
        <div class="promo-badge">Tournament Ready</div>
        <div class="promo-text">
            <div class="promo-category">
                <span class="category-dot"></span>
                <span class="category-name">Tournament Bags</span>
            </div>
            <h2 class="promo-name">Pro <span>Racquet Bag</span></h2>
            <p class="promo-desc">Carry your gear in style. Features thermo-guard lining to protect your rackets from heat, separate compartments for shoes and wet apparel, crafted for the touring professional.</p>
            <div class="promo-meta">
                <span class="meta-tag">🌡️ Thermo-Guard Lining</span>
                <span class="meta-tag">📦 9 Racket Capacity</span>
            </div>
            <a href="?id=43" class="btn-outline">Learn More 
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="promo-image">
            <div class="image-glow"></div>
            <img src="images/Pro_Racquet_Bag_9Pcs_(White).webp" alt="Pro Tournament Bag">
            <div class="product-tag">Limited Ed.</div>
        </div>
    </section>

    <!-- Aerosensa 50 -->
    <section class="promo-section" data-product="shuttle">
        <div class="promo-badge">Official Match Ball</div>
        <div class="promo-text">
            <div class="promo-category">
                <span class="category-dot"></span>
                <span class="category-name">Feather Shuttlecocks</span>
            </div>
            <h2 class="promo-name">Aerosensa <span>50</span></h2>
            <p class="promo-desc">The official shuttlecock of the world's leading international tournaments. Meticulously engineered for distance stability under varying environmental conditions of play.</p>
            <div class="promo-meta">
                <span class="meta-tag">🏆 BWF Approved</span>
                <span class="meta-tag">🪶 Premium Goose Feather</span>
            </div>
            <a href="?id=31" class="btn-outline">Learn More 
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="promo-image">
            <div class="image-glow"></div>
            <img src="images/Aerosensa_50.webp" alt="Aerosensa 50">
            <div class="product-tag">Tournament Grade</div>
        </div>
    </section>
</div>

<style>
/* ============================================
   CAROUSEL STYLES - Images keep original proportions
   ============================================ */

.hero {
    position: relative;
    width: 100%;
    min-height: 79vh;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    overflow: hidden;
    padding: 0;
}

/* 轮播容器 - 背景色为深色，图片居中显示 */
.carousel-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
    background-color: #1a1a2e;
}

.carousel-track {
    display: flex;
    width: 100%;
    height: 100%;
    transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.carousel-slide {
    flex-shrink: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.carousel-slide img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;  /* 保持原始比例，不裁剪 */
    display: block;
    margin: 0 auto;
}

/* 左右切换按钮 */
.carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 20;
    transition: all 0.3s ease;
    color: white;
    opacity: 0.7;
}

.carousel-btn:hover {
    background: rgba(255, 255, 255, 0.45);
    opacity: 1;
    transform: translateY(-50%) scale(1.05);
}

.carousel-btn-prev {
    left: 24px;
}

.carousel-btn-next {
    right: 24px;
}

/* 底部小点指示器 */
.carousel-dots {
    position: absolute;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 12px;
    z-index: 20;
    padding: 8px 16px;
    background: rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(8px);
    border-radius: 40px;
}

.dot {
    width: 10px;
    height: 10px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.dot.active {
    background: var(--red, #d0021b);
    transform: scale(1.2);
    box-shadow: 0 0 8px rgba(208, 2, 27, 0.6);
}

.dot:hover {
    background: rgba(255, 255, 255, 0.9);
    transform: scale(1.1);
}

/* 黑色渐变遮罩 - 让文字更清晰，不覆盖图片主体内容 */
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(105deg, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0.3) 40%, rgba(0, 0, 0, 0.1) 100%);
    z-index: 1;
    pointer-events: none;
}

/* Hero Content - 保持在图片上方 */
.hero-content {
    position: relative;
    z-index: 10;
    max-width: 680px;
    padding: 0 80px;
    color: white;
    text-shadow: 0 2px 15px rgba(0, 0, 0, 0.4);
    animation: heroReveal 0.9s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes heroReveal {
    0% { opacity: 0; transform: translateY(40px); }
    100% { opacity: 1; transform: translateY(0); }
}

.hero-eyebrow {
    font-size: 0.7rem;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: var(--gold, #c9a84c);
    margin-bottom: 20px;
    font-weight: 700;
    display: inline-block;
    padding: 6px 16px;
    background: rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(8px);
    border-radius: 40px;
}

.hero-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 4.8rem;
    font-weight: 600;
    line-height: 1.08;
    margin-bottom: 24px;
    color: var(--white);
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
}

.hero-title span {
    color: var(--red);
    position: relative;
    display: inline-block;
}

.hero-desc {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.92);
    line-height: 1.7;
    max-width: 520px;
    margin-bottom: 40px;
}

/* Button Styles */
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: var(--red);
    color: var(--white);
    text-decoration: none;
    padding: 14px 38px;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    border-radius: 50px;
    transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    border: 2px solid var(--red);
    box-shadow: 0 8px 25px rgba(208, 2, 27, 0.35);
}

.btn-primary:hover {
    background: var(--red-hover);
    border-color: var(--red-hover);
    transform: translateY(-4px);
    gap: 18px;
    box-shadow: 0 18px 35px rgba(208, 2, 27, 0.45);
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    color: var(--charcoal);
    text-decoration: none;
    padding: 11px 28px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    border-radius: 50px;
    transition: all 0.35s ease;
    border: 2px solid var(--charcoal);
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.btn-outline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0%;
    height: 100%;
    background: var(--charcoal);
    transition: width 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    z-index: -1;
}

.btn-outline:hover::before {
    width: 100%;
}

.btn-outline:hover {
    color: var(--white);
    border-color: var(--charcoal);
    gap: 16px;
    transform: translateY(-2px);
}

/* Promo Section Styles */
.promo-wrapper {
    background: var(--offwhite);
}

.promo-section {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 80px;
    padding: 110px 10%;
    max-width: 1600px;
    margin: 0 auto;
    transition: all 0.4s ease;
    border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

.promo-section:nth-child(even) {
    flex-direction: row-reverse;
    background: var(--white);
}

.promo-badge {
    position: absolute;
    top: 30px;
    right: 10%;
    background: linear-gradient(135deg, var(--red), var(--red-hover));
    color: var(--white);
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    padding: 6px 18px;
    border-radius: 30px;
    text-transform: uppercase;
    box-shadow: 0 4px 12px rgba(208, 2, 27, 0.3);
    z-index: 5;
}

.promo-section:nth-child(even) .promo-badge {
    right: auto;
    left: 10%;
}

.promo-category {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.category-dot {
    width: 8px;
    height: 8px;
    background: var(--red);
    border-radius: 50%;
    display: inline-block;
}

.category-name {
    font-size: 0.7rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--red);
    font-weight: 700;
}

.promo-name {
    font-family: 'Cormorant Garamond', serif;
    font-size: 3.8rem;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 20px;
    line-height: 1.15;
    letter-spacing: -0.02em;
}

.promo-name span {
    color: var(--red);
    font-weight: 700;
}

.promo-desc {
    font-size: 1rem;
    color: var(--text-muted);
    line-height: 1.75;
    margin-bottom: 28px;
    max-width: 540px;
}

.promo-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 35px;
}

.meta-tag {
    font-size: 0.7rem;
    padding: 5px 14px;
    background: rgba(0, 0, 0, 0.04);
    border-radius: 30px;
    color: var(--text-muted);
    font-weight: 500;
    letter-spacing: 0.02em;
    transition: all 0.2s ease;
}

.meta-tag:hover {
    background: rgba(208, 2, 27, 0.1);
    color: var(--red);
}

.promo-image {
    flex: 1;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    perspective: 800px;
}

.image-glow {
    position: absolute;
    width: 90%;
    height: 90%;
    background: radial-gradient(circle, rgba(208, 2, 27, 0.12) 0%, transparent 70%);
    border-radius: 50%;
    filter: blur(40px);
    z-index: 0;
    transition: all 0.5s ease;
}

.promo-image img {
    width: 100%;
    max-width: 480px;
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 30px 40px -15px rgba(0, 0, 0, 0.25));
    transition: all 0.5s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    position: relative;
    z-index: 2;
}

.product-tag {
    position: absolute;
    bottom: 20px;
    left: 20px;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(8px);
    color: var(--white);
    font-size: 0.65rem;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 30px;
    letter-spacing: 0.05em;
    z-index: 3;
}

.promo-section:hover .image-glow {
    transform: scale(1.1);
    opacity: 0.8;
}

.promo-section:hover .promo-image img {
    transform: scale(1.03) translateY(-5px);
    filter: drop-shadow(0 35px 45px -15px rgba(0, 0, 0, 0.3));
}

.promo-text {
    flex: 1;
    transition: transform 0.4s ease;
}

/* Responsive */
@media (max-width: 1100px) {
    .hero-content {
        padding: 0 60px;
    }
    .hero-title {
        font-size: 4rem;
    }
    .promo-section {
        padding: 80px 6%;
        gap: 50px;
    }
    .promo-name {
        font-size: 3.2rem;
    }
}

@media (max-width: 900px) {
    .hero {
        min-height: 70vh;
    }
    .hero-content {
        padding: 0 30px;
        text-align: center;
        max-width: 100%;
    }
    .hero-desc {
        margin-left: auto;
        margin-right: auto;
    }
    .hero-title {
        font-size: 3rem;
    }
    .carousel-btn {
        width: 40px;
        height: 40px;
    }
    .carousel-btn-prev {
        left: 12px;
    }
    .carousel-btn-next {
        right: 12px;
    }
    .carousel-dots {
        bottom: 16px;
        gap: 8px;
    }
    .dot {
        width: 8px;
        height: 8px;
    }
    .hero-overlay {
        background: linear-gradient(105deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.4) 100%);
    }
    
    .promo-section,
    .promo-section:nth-child(even) {
        flex-direction: column !important;
        text-align: center;
        padding: 60px 24px;
        gap: 35px;
    }
    .promo-text {
        text-align: center;
    }
    .promo-desc {
        margin-left: auto;
        margin-right: auto;
    }
    .promo-meta {
        justify-content: center;
    }
    .promo-category {
        justify-content: center;
    }
    .promo-name {
        font-size: 2.6rem;
    }
    .promo-badge {
        position: relative;
        top: 0;
        right: auto;
        display: inline-block;
        margin-bottom: 20px;
        align-self: flex-start;
    }
    .promo-section:nth-child(even) .promo-badge {
        left: auto;
    }
    .promo-image img {
        max-width: 350px;
    }
}

@media (max-width: 480px) {
    .hero {
        min-height: 60vh;
    }
    .hero-title {
        font-size: 2.2rem;
    }
    .hero-eyebrow {
        font-size: 0.55rem;
        letter-spacing: 0.25em;
    }
    .hero-desc {
        font-size: 0.9rem;
    }
    .carousel-btn {
        width: 32px;
        height: 32px;
    }
    .carousel-btn svg {
        width: 18px;
        height: 18px;
    }
    .carousel-btn-prev {
        left: 8px;
    }
    .carousel-btn-next {
        right: 8px;
    }
    .carousel-dots {
        bottom: 12px;
        gap: 6px;
        padding: 5px 12px;
    }
    .dot {
        width: 7px;
        height: 7px;
    }
    .promo-name {
        font-size: 2rem;
    }
    .promo-desc {
        font-size: 0.9rem;
    }
    .promo-image img {
        max-width: 260px;
    }
}
</style>

<script>
// ============================================
// 图片轮播逻辑 - 流畅切换，无限循环
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('carouselTrack');
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    let currentIndex = 0;
    const totalSlides = slides.length;
    let autoPlayInterval;
    const AUTO_PLAY_DELAY = 5000; // 5秒自动切换
    
    // 更新轮播位置
    function updateCarousel(instant = false) {
        const slideWidth = slides[0].clientWidth;
        const newPosition = -currentIndex * slideWidth;
        
        if (instant) {
            track.style.transition = 'none';
            track.style.transform = `translateX(${newPosition}px)`;
            // 强制重绘后恢复过渡
            track.offsetHeight;
            track.style.transition = 'transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        } else {
            track.style.transform = `translateX(${newPosition}px)`;
        }
        
        // 更新小点状态
        dots.forEach((dot, index) => {
            if (index === currentIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
    
    // 切换到下一张
    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateCarousel();
        resetAutoPlay();
    }
    
    // 切换到上一张
    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        updateCarousel();
        resetAutoPlay();
    }
    
    // 跳转到指定索引
    function goToSlide(index) {
        if (index === currentIndex) return;
        currentIndex = index;
        updateCarousel();
        resetAutoPlay();
    }
    
    // 重置自动播放计时器
    function resetAutoPlay() {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
        }
        autoPlayInterval = setInterval(nextSlide, AUTO_PLAY_DELAY);
    }
    
    // 窗口大小改变时重新计算位置
    let resizeTimer;
    window.addEventListener('resize', function() {
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            updateCarousel(true);
        }, 100);
    });
    
    // 绑定按钮事件
    prevBtn.addEventListener('click', prevSlide);
    nextBtn.addEventListener('click', nextSlide);
    
    // 绑定小点点击事件
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => goToSlide(index));
    });
    
    // 鼠标悬停时暂停自动播放
    const carouselContainer = document.querySelector('.carousel-container');
    carouselContainer.addEventListener('mouseenter', () => {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
            autoPlayInterval = null;
        }
    });
    
    carouselContainer.addEventListener('mouseleave', () => {
        if (!autoPlayInterval) {
            autoPlayInterval = setInterval(nextSlide, AUTO_PLAY_DELAY);
        }
    });
    
    // 初始化轮播位置
    updateCarousel(true);
    
    // 启动自动播放
    autoPlayInterval = setInterval(nextSlide, AUTO_PLAY_DELAY);
    
    // 确保所有图片加载完成后重新调整位置
    let loadedCount = 0;
    slides.forEach(slide => {
        const img = slide.querySelector('img');
        if (img.complete) {
            loadedCount++;
        } else {
            img.addEventListener('load', () => {
                loadedCount++;
                if (loadedCount === totalSlides) {
                    updateCarousel(true);
                }
            });
        }
    });
    
    if (loadedCount === totalSlides) {
        updateCarousel(true);
    }
});
</script>
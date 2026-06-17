<?php
// home.php - YONEX Homepage with Image Carousel (Original Image Proportions)
// Features: 5 slides, left/right buttons, clickable dots, smooth infinite loop
// Images use their original aspect ratio (not forced to fill)
?>
<!-- ==========================================
     HERO SECTION (保留原样，未作任何改动)
     ========================================== -->
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

<!-- ==========================================
     PROMO SECTION (全新极简质感：蓝、白、黑)
     ========================================== -->
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
                <span class="meta-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Rotational Generator System
                </span>
                <span class="meta-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                    Nanometric NEO
                </span>
            </div>
            <a href="?id=5" class="btn-outline">Learn More 
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="promo-image">
            <div class="image-glow"></div>
            <img src="images/astrox100zz_kurenai.webp" alt="Astrox 100 ZZ">
            
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
                <span class="meta-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Power Cushion+
                </span>
                <span class="meta-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    Double Russel Mesh
                </span>
            </div>
            <a href="?id=22" class="btn-outline">Learn More 
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="promo-image">
            <div class="image-glow"></div>
            <img src="images/Poer_Cushion_65Z_Wide.webp" alt="Power Cushion 65 Z4">
            
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
                <span class="meta-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>
                    Thermo-Guard Lining
                </span>
                <span class="meta-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    9 Racket Capacity
                </span>
            </div>
            <a href="?id=43" class="btn-outline">Learn More 
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="promo-image">
            <div class="image-glow"></div>
            <img src="images/Pro_Racquet_Bag_9Pcs_(White).webp" alt="Pro Tournament Bag">
            
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
                <span class="meta-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15l-3 3-3-3m6 0v6M12 9V3M9 6h6"/></svg>
                    BWF Approved
                </span>
                <span class="meta-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="6.5"/></svg>
                    Premium Goose Feather
                </span>
            </div>
            <a href="?id=31" class="btn-outline">Learn More 
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="promo-image">
            <div class="image-glow"></div>
            <img src="images/Aerosensa_50.webp" alt="Aerosensa 50">
          
    </section>
</div>

<style>
/* ============================================
   CAROUSEL STYLES - Images keep original proportions
   (保留原有样式未作改动)
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
    object-fit: contain;  
    display: block;
    margin: 0 auto;
}

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

.carousel-btn-prev { left: 24px; }
.carousel-btn-next { right: 24px; }

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
    background: #0050d2;
    transform: scale(1.2);
    box-shadow: 0 0 8px rgba(0, 80, 210, 0.6);
}

.dot:hover {
    background: rgba(255, 255, 255, 0.9);
    transform: scale(1.1);
}

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


/* ============================================
   PROMO SECTION STYLES - 极简蓝白黑质感重构
   ============================================ */
:root {
    --p-white:      #ffffff;
    --p-offwhite:   #f8fafc;
    --p-black:      #0f172a;
    --p-blue:       #0050d2;
    --p-text-muted: #64748b;
    --p-border:     #e2e8f0;
}

.promo-wrapper {
    background: var(--p-offwhite);
    font-family: 'Montserrat', sans-serif;
}

.promo-section {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 80px;
    padding: 120px 10%;
    max-width: 1600px;
    margin: 0 auto;
    transition: all 0.4s ease;
    border-bottom: 1px solid var(--p-border);
    overflow: hidden;
}

.promo-section:nth-child(even) {
    flex-direction: row-reverse;
    background: var(--p-white);
}

.promo-badge {
    position: absolute;
    top: 35px;
    right: 10%;
    background: var(--p-blue);
    color: var(--p-white);
    font-family: 'Oswald', sans-serif;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    padding: 6px 18px;
    border-radius: 4px; /* 锐利倒角，增强高级感 */
    text-transform: uppercase;
    box-shadow: 0 4px 15px rgba(0, 80, 210, 0.2);
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
    width: 6px;
    height: 6px;
    background: var(--p-blue);
    border-radius: 50%;
    display: inline-block;
}

.category-name {
    font-size: 0.7rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--p-text-muted);
    font-weight: 600;
}

.promo-name {
    font-family: 'Cormorant Garamond', serif;
    font-size: 4rem;
    font-weight: 600;
    color: var(--p-black);
    margin-bottom: 20px;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.promo-name span {
    color: var(--p-blue);
    font-style: italic;
}

.promo-desc {
    font-size: 1.05rem;
    color: var(--p-text-muted);
    line-height: 1.8;
    margin-bottom: 35px;
    max-width: 540px;
}

.promo-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 40px;
}

.meta-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.75rem;
    padding: 8px 16px;
    background: var(--p-white);
    border: 1px solid var(--p-border);
    border-radius: 40px;
    color: var(--p-black);
    font-weight: 600;
    letter-spacing: 0.05em;
    transition: all 0.3s ease;
}

.promo-section:nth-child(even) .meta-tag {
    background: var(--p-offwhite);
}

.meta-tag:hover {
    background: rgba(0, 80, 210, 0.05);
    border-color: rgba(0, 80, 210, 0.3);
    color: var(--p-blue);
    transform: translateY(-2px);
}

.meta-tag svg {
    color: var(--p-blue);
}

/* 重新设计的悬停按钮：高级黑质感 */
.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    color: var(--p-black);
    text-decoration: none;
    padding: 13px 32px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    border-radius: 50px;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid var(--p-black);
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
    background: var(--p-black);
    transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: -1;
}

.btn-outline:hover::before {
    width: 100%;
}

.btn-outline:hover {
    color: var(--p-white);
    gap: 16px;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
}

.promo-image {
    flex: 1;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    perspective: 800px;
}

/* 蓝灰色的极简光晕 */
.image-glow {
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(0, 80, 210, 0.08) 0%, transparent 60%);
    border-radius: 50%;
    filter: blur(40px);
    z-index: 0;
    transition: all 0.6s ease;
}

.promo-image img {
    width: 100%;
    max-width: 480px;
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 25px 35px rgba(15, 23, 42, 0.15));
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1), filter 0.6s ease;
    position: relative;
    z-index: 2;
}

/* 产品小标签：改为黑底白字更显专业 */
.product-tag {
    position: absolute;
    bottom: 20px;
    left: 20px;
    background: var(--p-black);
    color: var(--p-white);
    font-family: 'Oswald', sans-serif;
    font-size: 0.7rem;
    font-weight: 500;
    padding: 6px 16px;
    border-radius: 4px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    z-index: 3;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.promo-section:hover .image-glow {
    transform: scale(1.1);
    background: radial-gradient(circle, rgba(0, 80, 210, 0.12) 0%, transparent 65%);
}

.promo-section:hover .promo-image img {
    transform: translateY(-8px);
    filter: drop-shadow(0 35px 45px rgba(15, 23, 42, 0.2));
}

.promo-text {
    flex: 1;
    transition: transform 0.4s ease;
}

/* Responsive (Promo Section) */
@media (max-width: 1100px) {
    .promo-section {
        padding: 90px 6%;
        gap: 50px;
    }
    .promo-name {
        font-size: 3.4rem;
    }
}

@media (max-width: 900px) {
    .carousel-btn { width: 40px; height: 40px; }
    .carousel-btn-prev { left: 12px; }
    .carousel-btn-next { right: 12px; }
    .carousel-dots { bottom: 16px; gap: 8px; }
    .dot { width: 8px; height: 8px; }
    
    .promo-section,
    .promo-section:nth-child(even) {
        flex-direction: column !important;
        text-align: center;
        padding: 70px 24px;
        gap: 40px;
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
        font-size: 2.8rem;
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
    .carousel-btn { width: 32px; height: 32px; }
    .carousel-btn svg { width: 18px; height: 18px; }
    .carousel-dots { bottom: 12px; gap: 6px; padding: 5px 12px; }
    .dot { width: 7px; height: 7px; }
    
    .promo-name {
        font-size: 2.2rem;
    }
    .promo-desc {
        font-size: 0.95rem;
    }
    .promo-image img {
        max-width: 260px;
    }
    .meta-tag {
        font-size: 0.7rem;
        padding: 6px 12px;
    }
}
</style>

<script>
// ============================================
// 图片轮播逻辑 - 流畅切换，无限循环
// (保留原有脚本完全未作改动)
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
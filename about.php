<?php
// about.php - YONEX About Page | Professional Version
// 注意：此文件通过 index.php?category=about 加载，头部和底部由 index.php 提供
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Discover YONEX's legacy since 1946 — innovation, craftsmanship, and excellence in badminton equipment. Learn about our mission, values, and journey.">
    <title>YONEX | About Us — Innovation Since 1946</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ========== RESET & GLOBAL VARIABLES ========== */
        /* 这里的变量已与 index.php 完全同步，确保没有色差 */
        :root {
            --white:     #ffffff;
            --offwhite:  #fafbfc;
            --lightgray: #eef2f5; 
            --midgray:   #cbd5e1;
            --charcoal:  #1e293b; /* 与 index.php 的 footer 颜色完全一致 */
            --dark:      #0f172a;
            --red:       #d0021b;
            --red-hover: #a80016;
            --text-main: #1e293b;
            --text-muted:#64748b;
            --border:    #e2e8f0;
            --shadow-sm: 0 4px 20px rgba(0,0,0,0.03);
            --shadow-hover: 0 12px 30px rgba(0,0,0,0.08);
            --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* 避免与 index.php 的全局样式冲突，做了轻微作用域限制 */
        .about-wrapper {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--white);
            color: var(--text-main);
            line-height: 1.6;
        }

        /* ========== TYPOGRAPHY ========== */
        .about-wrapper h1, .about-wrapper h2, .about-wrapper h3 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .about-wrapper .section-title {
            font-size: 2.8rem;
            font-weight: 600;
            text-align: center;
            color: var(--charcoal);
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .about-wrapper .section-title {
                font-size: 2.2rem;
            }
        }

        /* ========== HERO SECTION ========== */
        .about-hero {
            background: linear-gradient(105deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.3) 100%),
                        url('images/about-hero.jpg') no-repeat center 25%;
            background-size: cover;
            min-height: 79vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }

        .about-hero-content {
            max-width: 880px;
            padding: 0 2rem;
            animation: fadeUp 0.8s ease-out;
        }

        .about-hero-content h1 {
            font-size: 5rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 1.25rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .about-hero-content p {
            font-size: 1.3rem;
            font-weight: 400;
            color: rgba(255,255,255,0.92);
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.5;
            font-family: 'Montserrat', sans-serif;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ========== MAIN CONTAINER ========== */
        .about-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 6rem 2rem;
        }

        .about-wrapper .divider {
            width: 60px;
            height: 2px;
            background: var(--red);
            margin: 1.75rem auto 2.5rem;
        }

        /* ========== INTRO SECTION ========== */
        .intro-section {
            text-align: center;
            max-width: 840px;
            margin: 0 auto 6rem;
        }

        .intro-section h2 {
            font-size: 3rem;
            font-weight: 600;
            color: var(--charcoal);
        }

        .intro-section p {
            font-size: 1.15rem;
            color: var(--text-muted);
            line-height: 1.8;
            margin-top: 0.5rem;
        }

        /* ========== CARDS COMMON STYLE ========== */
        .timeline-item, .mv-card, .value-item {
            background: var(--white);
            border-radius: 4px;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            border-top: 2px solid transparent; 
        }

        .timeline-item:hover, .mv-card:hover, .value-item:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-top-color: var(--red);
        }

        /* ========== SVG ICON WRAPPER ========== */
        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--lightgray);
            color: var(--charcoal);
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .mv-card:hover .icon-wrapper,
        .value-item:hover .icon-wrapper {
            background: var(--red);
            color: var(--white);
            transform: scale(1.05);
        }

        /* ========== TIMELINE ========== */
        .history-section { margin-bottom: 7rem; }

        .timeline {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            margin-top: 3rem;
        }

        .timeline-item {
            padding: 2.5rem 2rem;
            width: calc(33.33% - 1.5rem);
            min-width: 260px;
        }

        .timeline-year {
            font-family: 'Oswald', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--red);
            letter-spacing: 0.02em;
            margin-bottom: 0.75rem;
            line-height: 1.2;
            display: inline-block;
        }

        .timeline-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 0.75rem;
        }

        .timeline-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ========== MISSION & VISION CARDS ========== */
        .mission-vision {
            display: flex;
            flex-wrap: wrap;
            gap: 2.5rem;
            margin-bottom: 7rem;
        }

        .mv-card {
            flex: 1;
            min-width: 280px;
            padding: 3.5rem 3rem;
            text-align: center;
        }

        .mv-card h3 {
            font-family: 'Montserrat', sans-serif !important;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .mv-card p {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* ========== VALUES SECTION ========== */
        .values-section {
            background: var(--offwhite);
            padding: 5rem 3rem;
            border-radius: 8px;
            margin-bottom: 6rem;
        }

        .values-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
            margin-top: 3rem;
        }

        .value-item {
            text-align: center;
            width: calc(25% - 1.5rem);
            min-width: 200px;
            padding: 2.5rem 1.5rem;
            background: var(--white);
        }

        .value-item h4 {
            font-family: 'Montserrat', sans-serif !important;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .value-item p {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ========== CONTACT SECTION (CTA) ========== */
        .contact-section {
            text-align: center;
            /* 这里的背景色与 index.php 的页脚无缝衔接 */
            background: var(--charcoal);
            padding: 5rem 2rem;
            border-radius: 8px;
            transition: var(--transition);
            background-image: radial-gradient(circle at 10% 20%, rgba(208,2,27,0.12) 0%, rgba(0,0,0,0) 80%);
        }

        .contact-section h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 1rem;
        }

        .contact-section p {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.05rem;
            color: rgba(255,255,255,0.75);
            margin-bottom: 2.5rem;
        }

        .contact-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--red);
            color: var(--white);
            text-decoration: none;
            padding: 14px 36px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            border-radius: 40px;
            transition: background 0.3s, transform 0.3s;
            border: none;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
        }

        .contact-btn:hover {
            background: var(--red-hover);
            transform: translateY(-2px);
        }
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 992px) {
            .timeline-item { width: calc(50% - 1rem); }
            .value-item { width: calc(50% - 1rem); }
        }

        @media (max-width: 768px) {
            .about-hero { min-height: 60vh; }
            .about-hero-content h1 { font-size: 3.2rem; }
            .about-hero-content p { font-size: 1rem; }
            .about-container { padding: 4rem 1.5rem; }
            .intro-section h2 { font-size: 2.4rem; }
            .timeline-item { width: 100%; }
            .mission-vision { flex-direction: column; }
            .values-section { padding: 4rem 1.5rem; }
            .contact-section h3 { font-size: 2rem; }
        }

        @media (max-width: 540px) {
            .value-item { width: 100%; }
            .about-wrapper .section-title { font-size: 2rem; }
        }
    </style>
</head>
<body>

<div class="about-wrapper">
    <div class="about-hero" role="img" aria-label="YONEX heritage showcase">
        <div class="about-hero-content">
            <h1>About YONEX</h1>
            <p>Innovation and tradition since 1946 — crafting excellence for champions worldwide.</p>
        </div>
    </div>

    <div class="about-container">
        <section class="intro-section">
            <h2>Our Story</h2>
            <div class="divider"></div>
            <p>
                Founded in 1946 in Niigata, Japan, YONEX has evolved from a modest manufacturer of wooden 
                floats into the world's most revered brand in badminton. Driven by an unwavering commitment 
                to innovation, quality, and performance, YONEX equipment has been the trusted choice of 
                champions across generations — elevating the sport for over seven decades.
            </p>
        </section>
        
        <section class="history-section">
            <h2 class="section-title">Our Journey</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-year">1946</div>
                    <div class="timeline-title">Foundation</div>
                    <div class="timeline-desc">YONEX founded in Niigata, Japan, initially producing wooden floats for fishing nets.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">1957</div>
                    <div class="timeline-title">First Badminton Racket</div>
                    <div class="timeline-desc">Production of the first badminton racket, marking the entry into sports equipment.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">1969</div>
                    <div class="timeline-title">ISOMETRIC™ Technology</div>
                    <div class="timeline-desc">Revolutionary square-shaped head expands the sweet spot, transforming badminton forever.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">1997</div>
                    <div class="timeline-title">Power Cushion®</div>
                    <div class="timeline-desc">Introduction of Power Cushion technology for superior shock absorption in footwear.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2013</div>
                    <div class="timeline-title">Nanometric Technology</div>
                    <div class="timeline-desc">Advanced nano-materials incorporated into racket construction for enhanced performance.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2024</div>
                    <div class="timeline-title">Global Leadership</div>
                    <div class="timeline-desc">Official partner of BWF World Tour and the preferred choice of world champions.</div>
                </div>
            </div>
        </section>
        
        <div class="mission-vision">
            <div class="mv-card">
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="6"/>
                        <circle cx="12" cy="12" r="2"/>
                    </svg>
                </div>
                <h3>Our Mission</h3>
                <p>
                    To contribute to society through technological innovation and the advancement 
                    of sports, providing athletes worldwide with the highest quality equipment 
                    to achieve their fullest potential.
                </p>
            </div>
            <div class="mv-card">
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                    </svg>
                </div>
                <h3>Our Vision</h3>
                <p>
                    To be the most trusted and innovative brand in badminton, continuously pushing 
                    the boundaries of performance and inspiring generations of players around the globe.
                </p>
            </div>
        </div>
        
        <div class="values-section">
            <h2 class="section-title">Core Values</h2>
            <div class="values-grid">
                <div class="value-item">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <h4>Innovation</h4>
                    <p>Continuous R&D of breakthrough technologies for competitive edge.</p>
                </div>
                <div class="value-item">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="7"/>
                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                        </svg>
                    </div>
                    <h4>Excellence</h4>
                    <p>Uncompromising quality and precision in every product, from grip to frame.</p>
                </div>
                <div class="value-item">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h4>Integrity</h4>
                    <p>Honest, transparent business practices that build lasting trust.</p>
                </div>
                <div class="value-item">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                    </div>
                    <h4>Global Spirit</h4>
                    <p>Connecting cultures through the passion and respect for badminton.</p>
                </div>
            </div>
        </div>

        <div class="contact-section">
            <h3>Experience Excellence</h3>
            <p>Ready to elevate your game with the world's leading badminton equipment?</p>
            <a href="?category=badminton" class="contact-btn">Explore Products</a>
        </div>
        
    </div>
</div>

</body>
</html>
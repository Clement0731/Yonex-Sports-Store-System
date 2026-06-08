<?php
// about.php - YONEX About Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        /* ========== About Page Styles ========== */
        
        .about-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.4)), 
                        url('images/about-hero.jpg') no-repeat center center;
            background-size: cover;
            height: 900px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .about-hero-content {
            color: var(--white);
            max-width: 800px;
            padding: 0 20px;
        }
        
        .about-hero-content h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4.5rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .about-hero-content p {
            font-size: 1.2rem;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        /* 主要容器 */
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 40px;
        }
        
        /* 介绍部分 */
        .intro-section {
            text-align: center;
            margin-bottom: 80px;
        }
        
        .intro-section h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.8rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 24px;
        }
        
        .intro-section p {
            font-size: 1.1rem;
            color: var(--text-muted);
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .divider {
            width: 80px;
            height: 3px;
            background: var(--red);
            margin: 30px auto;
        }
        
        /* 历史时间线 */
        .history-section {
            margin-bottom: 80px;
        }
        
        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            color: var(--charcoal);
            margin-bottom: 50px;
        }
        
        .timeline {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }
        
        .timeline-item {
            background: var(--offwhite);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 30px;
            width: calc(33.33% - 20px);
            min-width: 250px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .timeline-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .timeline-year {
            font-size: 2rem;
            font-weight: 800;
            color: var(--red);
            margin-bottom: 15px;
            font-family: 'Oswald', sans-serif;
        }
        
        .timeline-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 12px;
        }
        
        .timeline-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
        }
        
        /* 使命与愿景 */
        .mission-vision {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            margin-bottom: 80px;
        }
        
        .mv-card {
            flex: 1;
            min-width: 280px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px 30px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .mv-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .mv-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .mv-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 20px;
        }
        
        .mv-card p {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.7;
        }
        
        /* 核心价值观 */
        .values-section {
            background: var(--offwhite);
            padding: 60px 40px;
            border-radius: 24px;
            margin-bottom: 60px;
        }
        
        .values-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }
        
        .value-item {
            text-align: center;
            width: calc(25% - 23px);
            min-width: 180px;
            padding: 20px;
        }
        
        .value-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .value-item h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 10px;
        }
        
        .value-item p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }
        
        /* 联系信息 */
        .contact-section {
            text-align: center;
            background: var(--charcoal);
            padding: 60px 40px;
            border-radius: 24px;
            color: var(--white);
        }
        
        .contact-section h3 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .contact-section p {
            font-size: 1rem;
            margin-bottom: 30px;
            opacity: 0.8;
        }
        
        .contact-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--red);
            color: var(--white);
            text-decoration: none;
            padding: 12px 32px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border-radius: 40px;
            transition: background 0.3s;
        }
        
        .contact-btn:hover {
            background: var(--red-hover);
        }
        
        /* 响应式 */
        @media (max-width: 768px) {
            .about-hero {
                height: 350px;
            }
            
            .about-hero-content h1 {
                font-size: 2.8rem;
            }
            
            .about-container {
                padding: 60px 20px;
            }
            
            .intro-section h2 {
                font-size: 2rem;
            }
            
            .timeline-item {
                width: 100%;
            }
            
            .mission-vision {
                flex-direction: column;
            }
            
            .value-item {
                width: calc(50% - 15px);
            }
        }
        
        @media (max-width: 480px) {
            .value-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="about-hero">
    <div class="about-hero-content">
        <h1>About YONEX</h1>
        <p>Innovation and tradition since 1946 — crafting excellence for champions worldwide.</p>
    </div>
</div>

<div class="about-container">
    
    <!-- 介绍部分 -->
    <div class="intro-section">
        <h2>Our Story</h2>
        <div class="divider"></div>
        <p>
            Founded in 1946 in Niigata, Japan, YONEX has grown from a small manufacturer of wooden 
            floats into the world's leading brand of badminton equipment. With a relentless commitment 
            to innovation, quality, and performance, YONEX equipment has been trusted by the world's 
            greatest champions for over seven decades.
        </p>
    </div>
    
    <!-- 历史时间线 -->
    <div class="history-section">
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
                <div class="timeline-title">ISOMETRIC Technology</div>
                <div class="timeline-desc">Revolutionary ISOMETRIC head shape expands the sweet spot, changing badminton forever.</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">1997</div>
                <div class="timeline-title">Power Cushion</div>
                <div class="timeline-desc">Introduction of Power Cushion technology for superior shock absorption in footwear.</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2013</div>
                <div class="timeline-title">Nanometric Technology</div>
                <div class="timeline-desc">Advanced nanotechnology incorporated into racket construction for enhanced performance.</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2024</div>
                <div class="timeline-title">Global Leadership</div>
                <div class="timeline-desc">Official partner of BWF World Tour and preferred choice of world champions.</div>
            </div>
        </div>
    </div>
    
    <!-- 使命与愿景 -->
    <div class="mission-vision">
        <div class="mv-card">
            <div class="mv-icon">🎯</div>
            <h3>Our Mission</h3>
            <p>
                To contribute to society through technological innovation and the advancement 
                of sports, providing athletes worldwide with the highest quality equipment 
                to achieve their fullest potential.
            </p>
        </div>
        <div class="mv-card">
            <div class="mv-icon">🌟</div>
            <h3>Our Vision</h3>
            <p>
                To be the most trusted and innovative brand in badminton, continuously pushing 
                the boundaries of performance and inspiring generations of players around the globe.
            </p>
        </div>
    </div>
    
    <!-- 核心价值观 -->
    <div class="values-section">
        <h2 class="section-title">Core Values</h2>
        <div class="values-grid">
            <div class="value-item">
                <div class="value-icon">🔬</div>
                <h4>Innovation</h4>
                <p>Continuous research and development of cutting-edge technologies.</p>
            </div>
            <div class="value-item">
                <div class="value-icon">🏆</div>
                <h4>Excellence</h4>
                <p>Uncompromising quality and precision in every product.</p>
            </div>
            <div class="value-item">
                <div class="value-icon">🤝</div>
                <h4>Integrity</h4>
                <p>Honest and transparent business practices worldwide.</p>
            </div>
            <div class="value-item">
                <div class="value-icon">🌍</div>
                <h4>Global Spirit</h4>
                <p>Connecting people through the passion for badminton.</p>
            </div>
        </div>
    </div>
    
    <!-- 联系信息 -->
    <div class="contact-section">
        <h3>Get in Touch</h3>
        <p>Have questions about our products or services? We're here to help.</p>
        <a href="?category=service" class="contact-btn">
            Contact Us
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
    </div>
    
</div>

</body>
</html>
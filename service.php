<?php 
// 引入您的数据库连接
include 'db_connect.php'; 
?>
<!-- 
注：此文件由 index.php 加载，头部和底部由 index.php 提供。
已切换为“蓝、白、黑”高级质感主题。
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>YONEX | Professional Stringing Services</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ========== 全局变量 (蓝白黑质感主题) ========== */
        :root {
            --white:        #ffffff;
            --offwhite:     #f8fafc;
            --lightgray:    #f1f5f9; 
            --midgray:      #cbd5e1;
            --black:        #0f172a; /* 深邃的黑灰色，比纯黑更有质感 */
            --black-dark:   #020617; /* 极致深黑 */
            --blue:         #0050d2; /* 高级运动湛蓝 */
            --blue-hover:   #003db3; /* 悬停深蓝 */
            --text-main:    #1e293b;
            --text-muted:   #64748b;
            --border:       #e2e8f0;
            --shadow-sm:    0 4px 20px rgba(0, 0, 0, 0.04);
            --shadow-hover: 0 12px 30px rgba(0, 80, 210, 0.08); /* 阴影带有一丝极淡的蓝光 */
            --transition:   all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .service-wrapper {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--white);
            color: var(--text-main);
            line-height: 1.6;
        }

        /* ========== 字体排版 ========== */
        .service-wrapper h1, .service-wrapper h2, .service-wrapper h3 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
        }

        .section-title {
            font-size: 2.8rem;
            text-align: center;
            color: var(--black);
            margin-bottom: 1.5rem;
        }

        .divider {
            width: 60px;
            height: 2px;
            background: var(--blue);
            margin: 0 auto 3.5rem;
        }

        /* ========== HERO 区域 ========== */
        .service-hero {
            /* 使用黑蓝色调的渐变遮罩 */
            background: linear-gradient(105deg, rgba(2, 6, 23, 0.85) 0%, rgba(15, 23, 42, 0.5) 100%),
                        url('images/strings-bg.jpg') no-repeat center 40%; /* <-- 请替换Hero图片链接 */
            background-size: cover;
            min-height: 79vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }

        .service-hero-content {
            padding: 0 2rem;
            animation: fadeUp 0.8s ease-out;
        }

        .service-hero-content h1 {
            font-size: 4.5rem;
            color: var(--white);
            margin-bottom: 1rem;
            text-shadow: 0 2px 15px rgba(0,0,0,0.5);
            letter-spacing: 0.02em;
        }

        .service-hero-content p {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.85);
            font-weight: 400;
            letter-spacing: 0.05em;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ========== 页面主体容器 ========== */
        .service-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 5rem 2rem;
        }

        /* ========== STRING GUIDE 产品展示 ========== */
        .string-product {
            display: flex;
            align-items: stretch;
            gap: 4rem;
            margin-bottom: 6rem;
            background: var(--white);
            border-radius: 8px;
        }

        /* 交替布局（图右文左）提升高级感 */
        .string-product.reverse {
            flex-direction: row-reverse;
        }

        .string-img-wrap {
            flex: 1.2;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--offwhite);
            padding: 3rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .string-img-wrap:hover {
            box-shadow: var(--shadow-hover);
            transform: scale(1.02);
            border-color: rgba(0, 80, 210, 0.2);
        }

        .string-img-wrap img {
            width: 100%;
            max-width: 350px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.06));
        }

        .string-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .string-info h3 {
            font-family: 'Oswald', sans-serif !important;
            font-size: 2.8rem;
            color: var(--black);
            margin-bottom: 1rem;
            line-height: 1.1;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .string-desc {
            font-size: 1.05rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.7;
        }

        /* Specs 方块设计 */
        .specs-title {
            font-family: 'Oswald', sans-serif;
            font-size: 0.95rem;
            color: var(--black);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .specs-box {
            background: var(--offwhite);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 1.5rem;
            transition: var(--transition);
            border-left: 3px solid var(--blue); /* 边缘高亮改为蓝色 */
        }

        .specs-box:hover {
            box-shadow: var(--shadow-sm);
            background: var(--white);
        }

        .spec-item {
            display: flex;
            padding: 0.6rem 0;
            border-bottom: 1px dashed var(--border);
        }

        .spec-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .spec-label {
            width: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .spec-value {
            flex: 1;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* ========== TENSION 区域 (黑底蓝光) ========== */
        .tension-section {
            background: var(--black-dark); /* 深黑背景 */
            border-radius: 12px;
            padding: 6rem 3rem;
            text-align: center;
            margin-bottom: 6rem;
            color: var(--white);
            /* 顶部散发微弱的高级蓝光 */
            background-image: radial-gradient(circle at 50% 0%, rgba(0, 80, 210, 0.15) 0%, rgba(2, 6, 23, 1) 70%);
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .tension-section .section-title {
            color: var(--white);
        }

        .tension-grid {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 3.5rem;
        }

        .tension-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            padding: 3rem 2rem;
            flex: 1;
            min-width: 250px;
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }

        .tension-card:hover {
            background: rgba(255,255,255,0.06);
            transform: translateY(-5px);
            border-color: rgba(0, 80, 210, 0.4);
            box-shadow: 0 10px 30px rgba(0, 80, 210, 0.1);
        }

        .t-val {
            font-family: 'Oswald', sans-serif;
            font-size: 2.8rem;
            color: var(--white);
            margin-bottom: 0.5rem;
            line-height: 1;
            letter-spacing: 0.02em;
        }

        .t-desc {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .tension-note {
            margin-top: 3.5rem;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.4);
            font-style: italic;
        }

        /* ========== PRICE LIST 区域 ========== */
        .price-section {
            max-width: 800px;
            margin: 0 auto;
        }

        .price-list {
            background: var(--white);
            border-radius: 8px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.8rem 2.5rem;
            border-bottom: 1px solid var(--border);
            transition: var(--transition);
        }

        .price-row:last-child {
            border-bottom: none;
        }

        .price-row:hover {
            background: var(--offwhite);
            padding-left: 3rem;
        }

        .price-name {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--black);
        }

        .price-val {
            font-family: 'Oswald', sans-serif;
            font-size: 1.35rem;
            color: var(--blue); /* 金额显示为高质感蓝色 */
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        /* ========== 响应式 ========== */
        @media (max-width: 900px) {
            .string-product, .string-product.reverse {
                flex-direction: column;
                gap: 2.5rem;
            }
            .string-img-wrap img { max-width: 250px; }
            .service-hero-content h1 { font-size: 3.5rem; }
        }

        @media (max-width: 600px) {
            .service-container { padding: 3rem 1.5rem; }
            .tension-section { padding: 4rem 1.5rem; }
            .spec-item { flex-direction: column; }
            .spec-label { margin-bottom: 4px; color: var(--black); }
            .price-row { padding: 1.5rem; }
            .price-row:hover { padding-left: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="service-wrapper">
    <!-- Hero 区域 -->
    <div class="service-hero" role="img" aria-label="YONEX Stringing Service">
        <div class="service-hero-content">
            <h1>Stringing Service</h1>
            <p>Precision tensioning for maximum court performance.</p>
        </div>
    </div>

    <div class="service-container">
        
        <!-- SECTION 1: String Guide -->
        <h2 class="section-title">String Guide</h2>
        <div class="divider"></div>

        <!-- 1st String: BG 66UM -->
        <div class="string-product">
            <div class="string-img-wrap">
                <!-- 替换为您 BG 66UM 的图片链接 -->
                <img src="images/bg66um.webp" alt="BG 66UM">
            </div>
            <div class="string-info">
                <h3>BG 66 ULTIMAX</h3>
                <p class="string-desc">The BG66UM has a 0.65mm thin gauge and the perfect balance of maximum speed, control, and durability, making it the best choice for the world's top players.</p>
                
                <div class="specs-title">Specs</div>
                <div class="specs-box">
                    <div class="spec-item">
                        <span class="spec-label">Color</span>
                        <span class="spec-value">White</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Gauge</span>
                        <span class="spec-value">0.65 mm</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Length</span>
                        <span class="spec-value">10 m (33 ft)</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Core</span>
                        <span class="spec-value">High Polymer Nylon Multifilament</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Outer</span>
                        <span class="spec-value">Special Braided High Polymer Nylon</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Made In</span>
                        <span class="spec-value">Japan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2nd String: Placeholder 1 (布局翻转，提升质感) -->
        <div class="string-product reverse">
            <div class="string-img-wrap">
                <!-- 替换为您第二款球线的图片链接 -->
                <img src="images/eb63.webp" alt="String 2">
            </div>
            <div class="string-info">
                <h3>EXBOLT 63</h3>
                <p class="string-desc">QUICK REPULSION AND HIGH SOUND WITH THIN GAUGE</p>
                
                <div class="specs-title">Specs</div>
                <div class="specs-box">
                    <div class="spec-item">
                        <span class="spec-label">Color</span>
                        <span class="spec-value">White</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Gauge</span>
                        <span class="spec-value">0.63 mm</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Length</span>
                        <span class="spec-value">10 m (33 ft)</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Core</span>
                        <span class="spec-value">High-Intensity Nylon Multifilament</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Outer</span>
                        <span class="spec-value">Special Braided Forged Fiber</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Made In</span>
                        <span class="spec-value">Japan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3rd String: Placeholder 2 -->
        <div class="string-product">
            <div class="string-img-wrap">
                <!-- 替换为您第三款球线的图片链接 -->
                <img src="images/bgab.webp" alt="String 3">
            </div>
            <div class="string-info">
                <h3>AEROBITE</h3>
                <p class="string-desc">HYBRID COMBO FOR QUICK REPULSION AND PIERCING SPIN</p>
                
                <div class="specs-title">Specs</div>
                <div class="specs-box">
                    <div class="spec-item">
                        <span class="spec-label">Color</span>
                        <span class="spec-value">Red / White</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Gauge</span>
                        <span class="spec-value">Mains 0.67mm / Crosses 0.61mm</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Length</span>
                        <span class="spec-value">10.5m / mains-5.5m(18ft); crosses-5m(16ft)</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Core</span>
                        <span class="spec-value">mains - High-Intensity Nylon Multifilament
                                                  crosses - High-Intensity Nylon Multifilament</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Outer</span>
                        <span class="spec-value">Special Braided High Polymer Nylon</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Made In</span>
                        <span class="spec-value">Japan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Tension Recommendation (深黑底板 + 蓝光) -->
        <div class="tension-section">
            <h2 class="section-title">Tension Recommendation</h2>
            <div class="divider" style="background: rgba(255,255,255,0.15);"></div>
            
            <div class="tension-grid">
                <div class="tension-card">
                    <div class="t-val">22-24 lbs</div>
                    <div class="t-desc">Beginner / Maximum Power</div>
                </div>
                <div class="tension-card">
                    <div class="t-val">25-27 lbs</div>
                    <div class="t-desc">Intermediate / Best Balance</div>
                </div>
                <div class="tension-card">
                    <div class="t-val">28-30+ lbs</div>
                    <div class="t-desc">Pro / Maximum Control</div>
                </div>
            </div>
            <p class="tension-note">* Note: Higher tension requires better technique to avoid wrist strain and potential racket frame damage.</p>
        </div>

        <!-- SECTION 3: Current Price List -->
        <div class="price-section">
            <h2 class="section-title">Current Price List</h2>
            <div class="divider"></div>
            
            <div class="price-list">
                <?php
                // PHP 数据库抓取逻辑
                $services = $conn->query("SELECT * FROM service_options");
                if($services && $services->num_rows > 0) {
                    while($row = $services->fetch_assoc()) {
                        echo "<div class='price-row'>
                                <div class='price-name'>" . htmlspecialchars($row['option_name']) . " <span style='color:var(--text-muted); font-size:0.85em; margin-left:8px; font-weight:400;'>(" . ucfirst($row['service_type']) . ")</span></div>
                                <div class='price-val'>RM " . number_format($row['additional_price'], 2) . "</div>
                              </div>";
                    }
                } else {
                    echo "<div class='price-row' style='justify-content:center; color:var(--text-muted);'>No services available at the moment.</div>";
                }
                ?>
            </div>
        </div>

    </div>
</div>

</body>
</html>
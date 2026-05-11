<?php
// rackets.php - YONEX Rackets Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        /* ========== 背景图横幅 ========== */
        .racket-hero {
            background: url('/FYP 1/images/rackets-bg.jpg') no-repeat center center;
            background-size: cover;
            height: 700px;
            width: 100%;
        }

        /* ========== 产品展示区域 ========== */
        .all-product-container {
            padding: 60px 10%;
            background: var(--white);
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 40px;
            color: var(--charcoal);
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }

        /* 产品网格布局 */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: var(--offwhite);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: 0.3s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow);
        }

        .product-card img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .p-series {
            font-size: 0.75rem;
            color: var(--red);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .p-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 10px 0;
            color: var(--charcoal);
            height: 50px;
        }

        .p-price {
            font-weight: 800;
            color: var(--charcoal);
            font-size: 1.2rem;
            display: block;
            margin-bottom: 20px;
        }

        .btn-info {
            display: inline-block;
            padding: 10px 24px;
            background: var(--charcoal);
            color: var(--white);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 4px;
            transition: 0.3s;
        }

        .btn-info:hover {
            background: var(--red);
        }

        /* 响应式设计 */
        @media (max-width: 768px) {
            .racket-hero {
                height: 300px;
            }

            .all-product-container {
                padding: 60px 20px;
            }

            .section-title {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>

    <!-- ========== 背景图横幅 ========== -->
    <div class="racket-hero"></div>

    <!-- ========== 产品展示区域 ========== -->
    <section class="all-product-container">
        <h2 class="section-title">Rackets</h2>

        <div class="product-grid">
            <!-- 产品1: Astrox 100 ZZ -->
            <div class="product-card">
                <img src="/FYP 1/images/astrox100zz_kurenai.webp" alt="Astrox 100 ZZ">
                <p class="p-series">ASTROX SERIES</p>
                <h3 class="p-name">ASTROX 100 ZZ</h3>
                <span class="p-price">RM 1,099.00</span>
                <a href="?category=rackets&product=100zz" class="btn-info">View Info</a>
            </div>

            <!-- 产品2: Astrox 88D Pro -->
            <div class="product-card">
                <img src="/FYP 1/images/Astrox_88D.webp" alt="Astrox 88D Pro">
                <p class="p-series">ASTROX SERIES</p>
                <h3 class="p-name">ASTROX 88D PRO</h3>
                <span class="p-price">RM 949.00</span>
                <a href="?category=rackets&product=astrox88dpro" class="btn-info">View Info</a>
            </div>

            <!-- 产品3: Nanoflare 800 Pro -->
            <div class="product-card">
                <img src="/FYP 1/images/Nanoflare_800.webp" alt="Nanoflare 800 Pro">
                <p class="p-series">NANOFLARE SERIES</p>
                <h3 class="p-name">NANOFLARE 800 PRO</h3>
                <span class="p-price">RM 899.00</span>
                <a href="?category=rackets&product=nanoflare800pro" class="btn-info">View Info</a>
            </div>

            <!-- 产品4: Arcsaber 11 Pro -->
            <div class="product-card">
                <img src="/FYP 1/images/Arcsaber_11_Pro.webp" alt="Arcsaber 11 Pro">
                <p class="p-series">ARCSABER SERIES</p>
                <h3 class="p-name">ARCSABER 11 PRO</h3>
                <span class="p-price">RM 849.00</span>
                <a href="?category=rackets&product=arcsaber11pro" class="btn-info">View Info</a>
            </div>
        </div>
    </section>

</body>
</html>
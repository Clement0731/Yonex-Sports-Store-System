<?php
// rackets.php - YONEX Rackets Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        /* ========== 背景图横幅 ========== */
        .racket-hero {
            background: url('/FYP/images/rackets-bg.jpg') no-repeat center center;
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
                <img src="/FYP/Yonex-Sports-Store-System/images/astrox100zz_kurenai.webp" alt="Astrox 100 ZZ">
                <p class="p-series">ASTROX SERIES</p>
                <h3 class="p-name">ASTROX 100 ZZ</h3>
                <span class="p-price">RM 1,099.00</span>
                <a href="?category=rackets&product=astrox100zz" class="btn-info">View Info</a>
            </div>

            <!-- 产品2: Astrox 88D  -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Astrox_88D.webp" alt="Astrox 88D ">
                <p class="p-series">ASTROX SERIES</p>
                <h3 class="p-name">ASTROX 88D </h3>
                <span class="p-price">RM 1099.00</span>
                <a href="?category=rackets&product=astrox88d" class="btn-info">View Info</a>
            </div>

            <!-- 产品3: Nanoflare 800  -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Nanoflare_800.webp" alt="Nanoflare 800 ">
                <p class="p-series">NANOFLARE SERIES</p>
                <h3 class="p-name">NANOFLARE 800 </h3>
                <span class="p-price">RM 899.00</span>
                <a href="?category=rackets&product=nanoflare800" class="btn-info">View Info</a>
            </div>

           
             <!-- 产品4: Nanoflare 1000Z -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Nanoflare_1000_Z.webp" alt="Nanoflare 1000Z">
                <p class="p-series">NANOFLARE SERIES</p>
                <h3 class="p-name">NANOFLARE 1000Z</h3>
                <span class="p-price">RM 1099.00</span>
                <a href="?category=rackets&product=nanoflare1000z" class="btn-info">View Info</a>
            </div>

             <!-- 产品5: Duora Z Strike -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Duora_Z-Strike.webp" alt="Duora Z Strike">
                <p class="p-series">DUORA SERIES</p>
                <h3 class="p-name">DUORA Z STRIKE</h3>
                <span class="p-price">RM 999.00</span>
                <a href="?category=rackets&product=duorazstrike" class="btn-info">View Info</a>
            </div>

             <!-- 产品6: Arcsaber 11  -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Arcsaber_11_Pro.webp" alt="Arcsaber 11 ">
                <p class="p-series">ARCSABER SERIES</p>
                <h3 class="p-name">ARCSABER 11 </h3>
                <span class="p-price">RM 999.00</span>
                <a href="?category=rackets&product=arcsaber11" class="btn-info">View Info</a>
            </div>

           <!-- 产品7: Astrox 99  -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Astrox_99_Pro.webp" alt="Astrox 99 Pro">
                <p class="p-series">ASTROX SERIES</p>
                <h3 class="p-name">ASTROX 99 </h3>
                <span class="p-price">RM 899.00</span>
                <a href="?category=rackets&product=astrox99" class="btn-info">View Info</a>
            </div>

            <!-- 产品8: Arcsaber 7  -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Arcsaber_7_Play.webp" alt="Arcsaber 7 ">
                <p class="p-series">ARCSABER SERIES</p>
                <h3 class="p-name">ARCSABER 7 </h3>
                <span class="p-price">RM 899.00</span>
                <a href="?category=rackets&product=arcsaber7" class="btn-info">View Info</a>
            </div>

        </div>
    </section>

</body>
</html>
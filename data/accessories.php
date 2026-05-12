<?php
// accessories.php - YONEX Accessories Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .accessories-hero {
            background: url('/FYP/Yonex-Sports-Store-System/images/accessories-bg.jpg') no-repeat center center;
            background-size: cover;
            height: 700px;
            width: 100%;
        }

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

        @media (max-width: 768px) {
            .accessories-hero {
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

    <div class="accessories-hero"></div>

    <section class="all-product-container">
        <h2 class="section-title">Accessories</h2>

        <div class="product-grid">
            <!-- 产品1: Sport Towel -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Sport_Towel.webp" alt="Sport Towel">
                <p class="p-series">TOWEL SERIES</p>
                <h3 class="p-name">SPORT TOWEL</h3>
                <span class="p-price">RM 39.00</span>
                <a href="?category=accessories&product=sport_towel" class="btn-info">View Info</a>
            </div>

            <!-- 产品2: Dry Super Grap -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Dry_Super_Grap(3wraps).webp" alt="Dry Super Grap(3wraps)">
                <p class="p-series">OVERGRIPS SERIES</p>
                <h3 class="p-name">DRY SUPER GRAP (3WRAPS)</h3>
                <span class="p-price">RM 18.00</span>
                <a href="?category=accessories&product=dry_super_grap" class="btn-info">View Info</a>
            </div>

            <!-- 产品3: Wristband -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Yonex_Sports_Wristband(White).webp" alt="Wristband">
                <p class="p-series">WRISTBAND SERIES</p>
                <h3 class="p-name">WRISTBAND</h3>
                <span class="p-price">RM 39.00</span>
                <a href="?category=accessories&product=wristband" class="btn-info">View Info</a>
            </div>

            <!-- 产品4: Headband -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Headband.webp" alt="Headband">
                <p class="p-series">HEADBAND SERIES</p>
                <h3 class="p-name">HEADBAND</h3>
                <span class="p-price">RM 12.00</span>
                <a href="?category=accessories&product=headband" class="btn-info">View Info</a>
            </div>
        </div>
    </section>

</body>
</html>
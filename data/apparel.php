<?php
// apparel.php - YONEX Apparel Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .apparel-hero {
            background: url('/FYP/Yonex-Sports-Store-System/images/apparel-bg.jpg') no-repeat center center;
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
            .apparel-hero {
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

    <div class="apparel-hero"></div>

    <section class="all-product-container">
        <h2 class="section-title">Apparel</h2>

        <div class="product-grid">
            <!-- 产品1: T-shirt -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/T-shirt(black).webp" alt="T-shirt(black)">
                <p class="p-series">T-SHIRT SERIES</p>
                <h3 class="p-name">T-SHIRT (black)</h3>
                <span class="p-price">RM 120.00</span>
                <a href="?category=apparel&product=tshirt_black" class="btn-info">View Info</a>
            </div>

            <!-- 产品2: T-shirt -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/T-shirt(white).webp" alt="T-shirt(white)">
                <p class="p-series">T-SHIRT SERIES</p>
                <h3 class="p-name">T-SHIRT (white)</h3>
                <span class="p-price">RM 120.00</span>
                <a href="?category=apparel&product=tshirt_white" class="btn-info">View Info</a>
            </div>

            <!-- 产品3: Training Shorts -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Yonex_Training_Shorts_(White).webp" alt="Training Shorts">
                <p class="p-series">TRAINING SERIES</p>
                <h3 class="p-name">TRAINING SHORTS</h3>
                <span class="p-price">RM 89.00</span>
                <a href="?category=apparel&product=training_shorts" class="btn-info">View Info</a>
            </div>

            <!-- 产品4: Warm-Up Jacket -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Yonex_Warm-Up_Jacket(Clear Mint).webp" alt="Warm-Up Jacket">
                <p class="p-series">TOURNAMENT SERIES</p>
                <h3 class="p-name">WARM-UP JACKET</h3>
                <span class="p-price">RM 290.00</span>
                <a href="?category=apparel&product=warm_up_jacket" class="btn-info">View Info</a>
            </div>
        </div>
    </section>

</body>
</html>
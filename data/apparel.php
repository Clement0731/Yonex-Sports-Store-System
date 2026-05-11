<?php
// apparel.php - YONEX Apparel Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .apparel-hero {
            background: url('/FYP 1/images/apparel-bg.jpg') no-repeat center center;
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
            <!-- 产品1: Tournament Shirt -->
            <div class="product-card">
                <img src="/FYP 1/images/apparel-shirt.webp" alt="Tournament Shirt">
                <p class="p-series">TOURNAMENT SERIES</p>
                <h3 class="p-name">TOURNAMENT SHIRT</h3>
                <span class="p-price">RM 179.00</span>
                <a href="#" class="btn-info">View Info</a>
            </div>

            <!-- 产品2: Training Shorts -->
            <div class="product-card">
                <img src="/FYP 1/images/apparel-shorts.webp" alt="Training Shorts">
                <p class="p-series">TRAINING SERIES</p>
                <h3 class="p-name">TRAINING SHORTS</h3>
                <span class="p-price">RM 129.00</span>
                <a href="#" class="btn-info">View Info</a>
            </div>

            <!-- 产品3: Team Jacket -->
            <div class="product-card">
                <img src="/FYP 1/images/apparel-jacket.webp" alt="Team Jacket">
                <p class="p-series">TEAM SERIES</p>
                <h3 class="p-name">TEAM JACKET</h3>
                <span class="p-price">RM 249.00</span>
                <a href="#" class="btn-info">View Info</a>
            </div>

            <!-- 产品4: Compression Tights -->
            <div class="product-card">
                <img src="/FYP 1/images/apparel-tights.webp" alt="Compression Tights">
                <p class="p-series">PERFORMANCE SERIES</p>
                <h3 class="p-name">COMPRESSION TIGHTS</h3>
                <span class="p-price">RM 159.00</span>
                <a href="#" class="btn-info">View Info</a>
            </div>
        </div>
    </section>

</body>
</html>
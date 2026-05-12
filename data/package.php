<?php
// package.php - YONEX Package Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .package-hero {
            background: url('/FYP/Yonex-Sports-Store-System/images/package-bg.jpg') no-repeat center center;
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
            .package-hero {
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

    <div class="package-hero"></div>

    <section class="all-product-container">
        <h2 class="section-title">Package</h2>

        <div class="product-grid">
            <!-- 套餐1: Beginner Set -->
            <div class="product-card">
                <img src="/FYP/images/package-beginner.webp" alt="Beginner Set">
                <p class="p-series">BEGINNER PACKAGE</p>
                <h3 class="p-name">BEGINNER SET</h3>
                <span class="p-price">RM 499.00</span>
                <a href="#" class="btn-info">View Info</a>
            </div>

            <!-- 套餐2: Intermediate Set -->
            <div class="product-card">
                <img src="/FYP/images/package-intermediate.webp" alt="Intermediate Set">
                <p class="p-series">INTERMEDIATE PACKAGE</p>
                <h3 class="p-name">INTERMEDIATE SET</h3>
                <span class="p-price">RM 899.00</span>
                <a href="#" class="btn-info">View Info</a>
            </div>

            <!-- 套餐3: Pro Set -->
            <div class="product-card">
                <img src="/FYP/images/package-pro.webp" alt="Pro Set">
                <p class="p-series">PRO PACKAGE</p>
                <h3 class="p-name">PRO SET</h3>
                <span class="p-price">RM 1,499.00</span>
                <a href="#" class="btn-info">View Info</a>
            </div>

            <!-- 套餐4: Tournament Set -->
            <div class="product-card">
                <img src="/FYP/images/package-tournament.webp" alt="Tournament Set">
                <p class="p-series">TOURNAMENT PACKAGE</p>
                <h3 class="p-name">TOURNAMENT SET</h3>
                <span class="p-price">RM 1,999.00</span>
                <a href="#" class="btn-info">View Info</a>
            </div>
        </div>
    </section>

</body>
</html>
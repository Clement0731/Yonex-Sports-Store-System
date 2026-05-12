<?php
// bags.php - YONEX Bags Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .bags-hero {
            background: url('/FYP/Yonex-Sports-Store-System/images/bags-bg.jpg') no-repeat center center;
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
            .bags-hero {
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

    <div class="bags-hero"></div>

    <section class="all-product-container">
        <h2 class="section-title">Bags</h2>

        <div class="product-grid">
            <!-- 产品1: Pro Tournament Bag -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Pro_Racquet_Bag_9Pcs_(White).webp" alt="Pro Tournament Bag">
                <p class="p-series">PRO SERIES</p>
                <h3 class="p-name">PRO TOURNAMENT BAG (9PCS)</h3>
                <span class="p-price">RM 499.00</span>
                <a href="?category=bags&product=pro_tournament_bag" class="btn-info">View Info</a>
            </div>

            <!-- 产品2: Expert Tournament Bag -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Expert_Racquet_Bag.webp" alt="Expert Tournament Bag">
                <p class="p-series">EXPERT SERIES</p>
                <h3 class="p-name">EXPERT TOURNAMENT BAG (6PCS)</h3>
                <span class="p-price">RM 199.00</span>
                <a href="?category=bags&product=expert_tournament_bag" class="btn-info">View Info</a>
            </div>

            <!-- 产品3: Expert Backpack -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Expert_Backpack_2Pcs_(Blue).webp" alt="Expert Backpack">
                <p class="p-series">EXPERT SERIES</p>
                <h3 class="p-name">EXPERT BACKPACK (2PCS)</h3>
                <span class="p-price">RM 169.00</span>
                <a href="?category=bags&product=expert_backpack" class="btn-info">View Info</a>
            </div>

        </div>
    </section>

</body>
</html>
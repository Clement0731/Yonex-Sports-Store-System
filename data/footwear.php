<?php
// footwear.php - YONEX Footwear Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .footwear-hero {
            background: url('/FYP/Yonex-Sports-Store-System/images/footwear-bg.jpg') no-repeat center center;
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
            .footwear-hero {
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

    <div class="footwear-hero"></div>

    <section class="all-product-container">
        <h2 class="section-title">Footwear</h2>

        <div class="product-grid">
            <!-- 产品1: Power Cushion 65 Z 4 -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Poer_Cushion_65Z_Wide.webp" alt="Power Cushion 65 Z4">
                <p class="p-series">POWER CUSHION SERIES</p>
                <h3 class="p-name">POWER CUSHION 65 Z4</h3>
                <span class="p-price">RM 599.00</span>
                <a href="?category=footwear&product=power_cushion_65z4" class="btn-info">View Info</a>
            </div>

            <!-- 产品2: SUBAXIA GT -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/SUBAXIA_GT.webp" alt="SUBAXIA GT">
                <p class="p-series">SUBAXIA SERIES</p>
                <h3 class="p-name">SUBAXIA GT </h3>
                <span class="p-price">RM 799.00</span>
                <a href="?category=footwear&product=subaxia_gt" class="btn-info">View Info</a>
            </div>

            <!-- 产品3: Eclipsion Z  -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Power_Cushion_Eclipsion_Z.webp" alt="Power Cushion Eclipsion Z">
                <p class="p-series">ECLIPSION SERIES</p>
                <h3 class="p-name">POWER CUSHION ECLIPSION Z</h3>
                <span class="p-price">RM 699.00</span>
                <a href="?category=footwear&product=eclipsion_z" class="btn-info">View Info</a>
            </div>

            <!-- 产品4: Power Cushion AerusZ -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Power_Cushion_AerusZ_Wide(blue).webp" alt="Power Cushion AerusZ ">
                <p class="p-series">AERUS SERIES</p>
                <h3 class="p-name">POWER CUSHION AERUS Z </h3>
                <span class="p-price">RM 599.00</span>
                <a href="?category=footwear&product=power_cushion_aerusz" class="btn-info">View Info</a>
            </div>
        </div>
    </section>

</body>
</html>
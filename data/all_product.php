<style>
    /* Badminton 页面背景图 */
    .badminton-hero {
        background: url('images/badminton bg.jpg') no-repeat center center;
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
        height: 50px; /* 统一高度对齐 */
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
</style>

<div class="badminton-hero"></div>

<section class="all-product-container">
    <h2 class="section-title">All Product</h2>

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

             <!-- 产品1: Aerosensa 50 -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Aerosensa_50.webp" alt="Aerosensa 50">
                <p class="p-series">AEROSENSA SERIES</p>
                <h3 class="p-name">AEROSENSA 50</h3>
                <span class="p-price">RM 85.00</span>
                <a href="?category=shuttlecocks&product=aerosensa_50" class="btn-info">View Info</a>
            </div>

            <!-- 产品2: Crosswind 70 -->
            <div class="product-card">
                <img src="/FYP/Yonex-Sports-Store-System/images/Crosswind_70.webp" alt="Crosswind 70">
                <p class="p-series">NYLON SERIES</p>
                <h3 class="p-name">CROSSWIND 70</h3>
                <span class="p-price">RM 28.00</span>
                <a href="?category=shuttlecocks&product=crosswind_70" class="btn-info">View Info</a>
            </div>

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
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
        <div class="product-card">
            <img src="/FYP/Yonex-Sports-Store-System/images/astrox100zz_kurenai.webp" alt="Astrox 100 ZZ">
            <p class="p-series">ASTROX SERIES</p>
            <h3 class="p-name">ASTROX 100 ZZ</h3>
            <span class="p-price">RM 1,099.00</span>
            <a href="#" class="btn-info">View Info</a>
        </div>

        <div class="product-card">
            <img src="https://www.yonex.com/media/catalog/product/n/a/nanoflare1000z_l_1.png" alt="Nanoflare 1000Z">
            <p class="p-series">NANOFLARE SERIES</p>
            <h3 class="p-name">NANOFLARE 1000 Z</h3>
            <span class="p-price">RM 1,099.00</span>
            <a href="#" class="btn-info">View Info</a>
        </div>

        <div class="product-card">
            <img src="https://www.yonex.com/media/catalog/product/s/h/shb65z3m_w_1.png" alt="Power Cushion 65Z">
            <p class="p-series">FOOTWEAR</p>
            <h3 class="p-name">POWER CUSHION 65 Z 4</h3>
            <span class="p-price">RM 599.00</span>
            <a href="#" class="btn-info">View Info</a>
        </div>

        <div class="product-card">
            <img src="https://www.yonex.com/media/catalog/product/a/s/as-50_1.png" alt="Aerosensa 50">
            <p class="p-series">SHUTTLECOCKS</p>
            <h3 class="p-name">AEROSENSA 50</h3>
            <span class="p-price">RM 165.00</span>
            <a href="#" class="btn-info">View Info</a>
        </div>
    </div>
</section>
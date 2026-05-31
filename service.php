<style>
    /* Service 页面专属头部 */
    .service-hero {
        background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)), url('images/badminton-hero.jpg') no-repeat center center;
        background-size: cover;
        padding: 120px 20px;
        text-align: center;
        color: var(--white);
    }
    
    .service-hero h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 4rem;
        margin-bottom: 20px;
    }
    
    .service-hero p {
        font-size: 1.1rem;
        color: var(--midgray);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* 服务卡片容器 */
    .services-container {
        padding: 80px 10%;
        background: var(--offwhite);
        display: flex;
        flex-direction: column;
        gap: 60px;
    }

    .service-card {
        display: flex;
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        align-items: center;
    }
    
    /* 让第二张卡片左右反转，增加设计感 */
    .service-card:nth-child(even) {
        flex-direction: row-reverse;
    }

    .service-image {
        flex: 1;
        min-height: 400px;
        background-color: var(--lightgray);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .service-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
    }

    /* 找不到图片时的替代文字 */
    .img-placeholder {
        color: var(--text-muted);
        font-weight: 600;
        letter-spacing: 2px;
    }

    .service-content {
        flex: 1;
        padding: 50px;
    }

    .service-tag {
        color: var(--red);
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-size: 0.85rem;
        margin-bottom: 15px;
        display: block;
    }

    .service-content h2 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 2.5rem;
        color: var(--charcoal);
        margin-bottom: 25px;
        line-height: 1.2;
    }

    .service-content p {
        color: var(--text-muted);
        line-height: 1.8;
        margin-bottom: 25px;
        font-size: 1.05rem;
    }

    .service-features {
        list-style: none;
        margin-bottom: 35px;
    }

    .service-features li {
        position: relative;
        padding-left: 30px;
        margin-bottom: 12px;
        color: var(--charcoal);
        font-weight: 500;
    }

    .service-features li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: var(--red);
        font-weight: bold;
        font-size: 1.1rem;
    }
    
    .btn-service {
        display: inline-block;
        padding: 12px 32px;
        background: var(--charcoal);
        color: var(--white);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        border-radius: 4px;
        transition: background 0.3s;
    }
    
    .btn-service:hover {
        background: var(--red);
    }

    /* 手机端自适应 */
    @media (max-width: 900px) {
        .service-card, .service-card:nth-child(even) {
            flex-direction: column;
        }
        .service-image {
            width: 100%;
            min-height: 250px;
        }
        .service-content {
            padding: 30px;
        }
    }
</style>

<div class="service-hero">
    <h1>Premium Services</h1>
    <p>Elevate your game with our professional customization services. From precise racket stringing to personalized apparel.</p>
</div>

<div class="services-container">
    
    <!-- 第一项服务：专业穿线 -->
    <div class="service-card">
        <div class="service-image">
            <div class="img-placeholder">IMAGE: STRINGING</div>
            <!-- 如果你有穿线的图片，可以放进 images 文件夹，然后在这里改名字 -->
            <img src="images/stringing_service.jpg" alt="Racket Stringing Service" onerror="this.style.opacity='0'">
        </div>
        <div class="service-content">
            <span class="service-tag">Professional Equipment Care</span>
            <h2>Custom Stringing & Tension</h2>
            <p>Maximize your racket's potential with our professional stringing service. Whether you need explosive power, sharp control, or durability, we help you find the perfect string and tension match.</p>
            <ul class="service-features">
                <li>Wide selection of YONEX strings (BG66 Ultimax, EXBOLT, AEROBITE, etc.)</li>
                <li>Precise tension adjustment (20 lbs to 35+ lbs)</li>
                <li>Electronic stringing machine for consistent results</li>
            </ul>
            <a href="?category=rackets" class="btn-service">Shop Rackets</a>
        </div>
    </div>

    <!-- 第二项服务：衣服印字 -->
    <div class="service-card">
        <div class="service-image">
            <div class="img-placeholder">IMAGE: APPAREL PRINTING</div>
            <!-- 如果你有印字的图片，可以放进 images 文件夹，然后在这里改名字 -->
            <img src="images/apparel_printing.jpg" alt="Apparel Printing Service" onerror="this.style.opacity='0'">
        </div>
        <div class="service-content">
            <span class="service-tag">Make It Yours</span>
            <h2>Apparel Customization</h2>
            <p>Stand out on the court just like the pros. We offer high-quality name and logo printing services for all YONEX apparel, t-shirts, and warm-up jackets purchased in-store.</p>
            <ul class="service-features">
                <li>Custom name printing on the back of shirts and jackets</li>
                <li>Durable, sweat-resistant heat transfer materials</li>
                <li>Official tournament standard fonts available</li>
            </ul>
            <a href="?category=apparel" class="btn-service">Browse Apparel</a>
        </div>
    </div>

</div>
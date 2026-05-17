<?php
// sport_towel.php - Sport Towel Product Detail Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        /* ========== 产品详情页面样式 ========== */
        
        .product-detail-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 40px;
        }
        
        /* 面包屑导航 */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 40px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .breadcrumb a:hover {
            color: var(--red);
        }
        
        .breadcrumb span {
            color: var(--charcoal);
            font-weight: 600;
        }
        
        .breadcrumb .separator {
            color: var(--midgray);
        }
        
        /* 产品主要区域 */
        .product-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 80px;
        }
        
        /* 左侧产品图片 */
        .product-image-section {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--offwhite);
            border-radius: 16px;
            padding: 40px;
            min-height: 600px;
            position: sticky;
            top: 100px;
        }
        
        .product-image-section img {
            width: 100%;
            height: auto;
            max-height: 650px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        .product-image-section img:hover {
            transform: scale(1.05);
        }
        
        /* 右侧产品信息 */
        .product-info-section {
            display: flex;
            flex-direction: column;
        }
        
        .product-series-badge {
            display: inline-block;
            background: var(--red);
            color: var(--white);
            padding: 6px 16px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            border-radius: 4px;
            margin-bottom: 16px;
            width: fit-content;
        }
        
        .product-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 12px;
            line-height: 1.1;
        }
        
        .product-subtitle {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.6;
        }
        
        .product-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--charcoal);
            margin-bottom: 30px;
        }
        
        .product-price .currency {
            font-size: 1.5rem;
            color: var(--text-muted);
        }
        
        /* 库存选择 */
        .quantity-selector {
            margin-bottom: 30px;
        }
        
        .quantity-selector-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--charcoal);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 12px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0;
            width: fit-content;
            border: 2px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .quantity-btn {
            width: 44px;
            height: 44px;
            border: none;
            background: var(--offwhite);
            cursor: pointer;
            font-size: 1.2rem;
            color: var(--charcoal);
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn:hover {
            background: var(--lightgray);
        }
        
        .quantity-input {
            width: 70px;
            height: 44px;
            border: none;
            border-left: 2px solid var(--border);
            border-right: 2px solid var(--border);
            text-align: center;
            font-size: 1rem;
            font-weight: 600;
            color: var(--charcoal);
        }
        
        /* 购物保障 */
        .shopping-guarantee {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding: 16px 20px;
            background: var(--offwhite);
            border-radius: 8px;
            flex-wrap: wrap;
        }
        
        .guarantee-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .guarantee-icon {
            color: #22c55e;
            font-size: 1.2rem;
        }
        
        /* 按钮组 */
        .button-group {
            display: flex;
            gap: 16px;
            margin-bottom: 40px;
        }
        
        .btn-add-to-cart {
            flex: 1;
            padding: 16px 32px;
            background: var(--charcoal);
            color: var(--white);
            border: 2px solid var(--charcoal);
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
        }
        
        .btn-add-to-cart:hover {
            background: var(--dark);
        }
        
        .btn-buy-now {
            flex: 1;
            padding: 16px 32px;
            background: var(--red);
            color: var(--white);
            border: 2px solid var(--red);
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
        }
        
        .btn-buy-now:hover {
            background: #b71c1c;
        }
        
        /* 分隔线 */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 40px 0;
        }
        
        /* 产品详细信息 */
        .product-details-section {
            padding: 60px 0;
        }
        
        .details-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--charcoal);
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .detail-card {
            background: var(--offwhite);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        
        .detail-card h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 15px;
        }
        
        .detail-card p {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.7;
        }
        
        .specs-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .specs-table tr {
            border-bottom: 1px solid var(--border);
        }
        
        .specs-table td {
            padding: 14px 16px;
            font-size: 0.95rem;
        }
        
        .specs-table td:first-child {
            font-weight: 700;
            color: var(--charcoal);
            width: 40%;
        }
        
        .specs-table td:last-child {
            color: var(--text-muted);
        }
        
        /* 响应式 */
        @media (max-width: 768px) {
            .product-detail-container {
                padding: 40px 20px;
            }
            
            .product-main {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .product-image-section {
                min-height: 400px;
                padding: 20px;
                position: static;
            }
            
            .product-name {
                font-size: 2.5rem;
            }
            
            .product-price {
                font-size: 2rem;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="product-detail-container">
    
    <!-- 面包屑导航 -->
    <div class="breadcrumb">
        <a href="?category=home">Home</a>
        <span class="separator">/</span>
        <a href="?category=accessories">Accessories</a>
        <span class="separator">/</span>
        <span>Sport Towel</span>
    </div>
    
    <!-- 产品主要区域 -->
    <div class="product-main">
        
        <!-- 左侧：产品图片 -->
        <div class="product-image-section">
            <img src="/FYP/Yonex-Sports-Store-System/images/Sport_Towel.webp" alt="Sport Towel">
        </div>
        
        <!-- 右侧：产品信息 -->
        <div class="product-info-section">
            
            <!-- 系列标签 -->
            <span class="product-series-badge">TOWEL SERIES</span>
            
            <!-- 产品名称 -->
            <h1 class="product-name">Sport Towel</h1>
            
            <!-- 产品简介 -->
            <p class="product-subtitle">
                High-absorbency microfiber sport towel. Perfect for wiping away sweat during 
                intense matches and training sessions.
            </p>
            
            <!-- 价格 -->
            <div class="product-price">
                <span class="currency">RM</span> 39.00
            </div>
            
            <!-- 库存数量选择 -->
            <div class="quantity-selector">
                <p class="quantity-selector-label">Quantity</p>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="changeQuantity(-1)">−</button>
                    <input type="text" class="quantity-input" id="quantity" value="1" readonly>
                    <button class="quantity-btn" onclick="changeQuantity(1)">+</button>
                </div>
            </div>
            
            <!-- 购物保障 -->
            <div class="shopping-guarantee">
                <div class="guarantee-item">
                    <span class="guarantee-icon">✓</span>
                    Free Shipping
                </div>
                <div class="guarantee-item">
                    <span class="guarantee-icon">✓</span>
                    Authentic Product
                </div>
                <div class="guarantee-item">
                    <span class="guarantee-icon">✓</span>
                    30-Day Return
                </div>
                <div class="guarantee-item">
                    <span class="guarantee-icon">✓</span>
                    High Absorbency
                </div>
            </div>
            
            <!-- 按钮组 -->
            <div class="button-group">
                <a href="#" class="btn-add-to-cart">Add to Cart</a>
                <a href="#" class="btn-buy-now">Buy Now</a>
            </div>
            
        </div>
        
    </div>
    
    <!-- 分隔线 -->
    <hr class="divider">
    
    <!-- 产品详细信息 -->
    <div class="product-details-section">
        <h2 class="details-title">Product Details</h2>
        
        <div class="details-grid">
            
            <!-- 技术特点 -->
            <div class="detail-card">
                <h4>Key Features</h4>
                <p>
                    <strong>High Absorbency</strong> - Microfiber material quickly absorbs 
                    sweat to keep you dry during matches.<br><br>
                    <strong>Soft & Gentle</strong> - Gentle on skin, perfect for frequent use 
                    during training and competition.<br><br>
                    <strong>Compact Size</strong> - Easy to carry in your bag, always ready 
                    when you need it.
                </p>
            </div>
            
            <!-- 完整规格 -->
            <div class="detail-card">
                <h4>Specifications</h4>
                <table class="specs-table">
                    <tr>
                        <td>Series</span></td>
                        <td>Towel Series</span></td>
                    </tr>
                    <tr>
                        <td>Material</span></td>
                        <td>Microfiber</span></td>
                    </tr>
                    <tr>
                        <td>Size</span></td>
                        <td>Approx. 40cm x 80cm</span></td>
                    </tr>
                    <tr>
                        <td>Color</span></td>
                        <td>White / Red / Blue</span></td>
                    </tr>
                    <tr>
                        <td>Application</span></td>
                        <td>Training / Competition</span></td>
                    </tr>
                </table>
            </div>
            
            <!-- 产品描述 -->
            <div class="detail-card">
                <h4>Description</h4>
                <p>
                    The YONEX Sport Towel is an essential accessory for every badminton player. 
                    Made from high-quality microfiber material, it offers exceptional absorbency 
                    to keep you dry and comfortable during intense matches.
                    <br><br>
                    Soft and gentle on the skin, this towel is perfect for frequent use during 
                    training sessions and tournaments. Its compact size makes it easy to carry 
                    in your bag, ensuring you're always prepared to wipe away sweat and stay focused on your game.
                </p>
            </div>
            
        </div>
    </div>
    
</div>

<script>
    // 数量加减
    function changeQuantity(delta) {
        const quantityInput = document.getElementById('quantity');
        let currentValue = parseInt(quantityInput.value);
        let newValue = currentValue + delta;
        if (newValue < 1) newValue = 1;
        if (newValue > 10) newValue = 10;
        quantityInput.value = newValue;
    }
</script>

</body>
</html>
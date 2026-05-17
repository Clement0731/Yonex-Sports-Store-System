<?php
// arcsaber11.php - Arcsaber 11 Pro Product Detail Page
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
        
        /* 规格选择 */
        .spec-selector {
            margin-bottom: 30px;
        }
        
        .spec-selector-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--charcoal);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 12px;
        }
        
        .spec-options {
            display: flex;
            gap: 12px;
        }
        
        .spec-option {
            padding: 12px 28px;
            border: 2px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 0.95rem;
            background: var(--white);
            color: var(--charcoal);
        }
        
        .spec-option:hover {
            border-color: var(--charcoal);
        }
        
        .spec-option.active {
            border-color: var(--charcoal);
            background: var(--charcoal);
            color: var(--white);
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
        <a href="?category=rackets">Rackets</a>
        <span class="separator">/</span>
        <span>Arcsaber 11 Pro</span>
    </div>
    
    <!-- 产品主要区域 -->
    <div class="product-main">
        
        <!-- 左侧：产品图片 -->
        <div class="product-image-section">
            <img src="/FYP/Yonex-Sports-Store-System/images/Arcsaber_11_Pro.webp" alt="Arcsaber 11 Pro">
        </div>
        
        <!-- 右侧：产品信息 -->
        <div class="product-info-section">
            
            <!-- 系列标签 -->
            <span class="product-series-badge">ARCSABER SERIES</span>
            
            <!-- 产品名称 -->
            <h1 class="product-name">Arcsaber 11 Pro</h1>
            
            <!-- 产品简介 -->
            <p class="product-subtitle">
                Precision control and powerful arcs. The enhanced version of the legendary 
                Arcsaber 11, designed for all-around performance.
            </p>
            
            <!-- 价格 -->
            <div class="product-price">
                <span class="currency">RM</span> 999.00
            </div>
            
            <!-- 规格选择 -->
            <div class="spec-selector">
                <p class="spec-selector-label">Weight / Grip Size</p>
                <div class="spec-options">
                    <div class="spec-option active" onclick="selectSpec(this, '3U')">
                        3U (88g) / G5
                    </div>
                    <div class="spec-option" onclick="selectSpec(this, '4U')">
                        4U (83g) / G5
                    </div>
                </div>
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
                    1 Year Warranty
                </div>
                <div class="guarantee-item">
                    <span class="guarantee-icon">✓</span>
                    30-Day Return
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
                <h4>Key Technologies</h4>
                <p>
                    <strong>Enhanced Arcsaber Frame</strong> - Provides a perfect balance of power 
                    and control with a unique arc-shaped frame design.<br><br>
                    <strong>Super Slim Shaft</strong> - Reduces air resistance for faster swings 
                    while maintaining excellent control.<br><br>
                    <strong>ISOMETRIC</strong> - Enlarged sweet spot for consistent accuracy 
                    across the string bed.
                </p>
            </div>
            
            <!-- 完整规格 -->
            <div class="detail-card">
                <h4>Specifications</h4>
                <table class="specs-table">
                    <tr>
                        <td>Series</td>
                        <td>Arcsaber</td>
                    </tr>
                    <tr>
                        <td>Flex</td>
                        <td>Medium Stiff</td>
                    </tr>
                    <tr>
                        <td>Balance</td>
                        <td>Even Balance</td>
                    </tr>
                    <tr>
                        <td>Frame Material</td>
                        <td>HM Graphite, Super HMG</td>
                    </tr>
                    <tr>
                        <td>Shaft Material</td>
                        <td>HM Graphite, Ultra PEF</td>
                    </tr>
                    <tr>
                        <td>String Tension</td>
                        <td>19-27 lbs</td>
                    </tr>
                    <tr>
                        <td>Color</td>
                        <td>Dark Navy / Red</td>
                    </tr>
                </table>
            </div>
            
            <!-- 产品描述 -->
            <div class="detail-card">
                <h4>Description</h4>
                <p>
                    The Arcsaber 11 Pro is the successor to the legendary Arcsaber 11, one of 
                    the most beloved rackets in YONEX history. It delivers a perfect balance of 
                    power, control, and feel for all-around players.
                    <br><br>
                    Featuring a super slim shaft and enhanced arc frame design, this racket 
                    generates sharp arc shots with precision placement. Ideal for intermediate 
                    to advanced players seeking consistent performance.
                </p>
            </div>
            
        </div>
    </div>
    
</div>

<script>
    // 规格选择
    function selectSpec(element, spec) {
        document.querySelectorAll('.spec-option').forEach(option => {
            option.classList.remove('active');
        });
        element.classList.add('active');
        console.log('Selected spec:', spec);
    }
    
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
<?php
include 'db_connect.php'; // 引入数据库连接

// 检查网址里有没有传 ID 过来
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // 去数据库里寻找这个 ID 的产品资料
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // 把找到的产品资料存进 $row
    } else {
        die("数据库里找不到该产品！");
    }
} else {
    die("Product not specified. (没有指定产品ID)");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['name']; ?> - Yonex Store</title>
    
    <link rel="stylesheet" href="style.css">
    
    <style>
        .custom-service-box {
            background-color: var(--offwhite);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 25px;
        }
        .service-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--red);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            border-bottom: 1px dashed var(--midgray);
            padding-bottom: 8px;
        }
        .service-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 8px;
            margin-top: 15px;
        }
        .service-input {
            width: 100%;
            padding: 10px 12px;
            font-family: inherit;
            font-size: 0.9rem;
            color: var(--charcoal);
            border: 1px solid var(--border);
            border-radius: 4px;
            background-color: var(--white);
            transition: border-color 0.3s;
        }
        .service-input:focus {
            outline: none;
            border-color: var(--charcoal);
        }
        /* 印字参考照片的样式（如果你不用下拉框的话，这个样式会生效） */
        .printing-example-img {
            width: 100%;
            max-width: 280px; 
            border-radius: 6px; 
            border: 1px solid var(--border);
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>

<div class="product-detail-container">
    
    <div class="breadcrumb">
        <a href="index.php">Home</a>
        <span class="separator">/</span>
        <a href="<?php echo strtolower($row['category']); ?>.php"><?php echo $row['category']; ?></a>
        <span class="separator">/</span>
        <span><?php echo $row['name']; ?></span>
    </div>
    
    <div class="product-main">
        
        <div class="product-image-section">
            <img src="images/<?php echo basename($row['image_url']); ?>" alt="<?php echo $row['name']; ?>">
        </div>
        
        <div class="product-info-section">
            
            <span class="product-series-badge"><?php echo $row['series']; ?> SERIES</span>
            
            <h1 class="product-name"><?php echo $row['name']; ?></h1>
            
            <p class="product-subtitle">
                <?php echo $row['subtitle']; ?>
            </p>
            
            <div class="product-price">
                <span class="currency">RM</span> <?php echo $row['price']; ?>
            </div>
            
            <?php if (strtolower($row['category']) == 'rackets') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Weight / Grip Size</p>
                <div class="spec-options">
                    <div class="spec-option active" onclick="selectSpec(this, '3U')">3U (88g) / G5</div>
                    <div class="spec-option" onclick="selectSpec(this, '4U')">4U (83g) / G5</div>
                </div>
            </div>
            
            <div class="custom-service-box">
                <h4 class="service-title">Stringing Service (穿线服务)</h4>
                
                <label for="string_type" class="service-label" style="margin-top: 0;">String Type (线种):</label>
                <select name="string_type" id="string_type" class="service-input">
                    <option value="unstrung">Unstrung (空拍) - RM 0.00</option>
                    <option value="bg66um">BG66 Ultimax (+ RM 35.00)</option>
                    <option value="exbolt63">EXBOLT 63 (+ RM 40.00)</option>
                    <option value="aerobite">AEROBITE (+ RM 42.00)</option>
                </select>

                <label for="tension" class="service-label">Tension (磅数):</label>
                <select name="tension" id="tension" class="service-input">
                    <option value="none">No Tension (不穿线)</option>
                    <option value="24">24 lbs (Beginner / 初学)</option>
                    <option value="26">26 lbs (Intermediate / 进阶)</option>
                    <option value="28">28 lbs (Advanced / 高级)</option>
                </select>
            </div>
            <?php } ?>

            <?php if (strtolower($row['category']) == 'footwear') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Shoe Size (EU)</p>
                <div class="spec-options">
                    <div class="spec-option active" onclick="selectSpec(this, '40')">40</div>
                    <div class="spec-option" onclick="selectSpec(this, '41')">41</div>
                    <div class="spec-option" onclick="selectSpec(this, '42')">42</div>
                    <div class="spec-option" onclick="selectSpec(this, '43')">43</div>
                </div>
            </div>
            <?php } ?>

            <?php if (strtolower($row['category']) == 'shuttlecocks') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Speed</p>
                <div class="spec-options">
                    <div class="spec-option" onclick="selectSpec(this, '76')">76 (Slow)</div>
                    <div class="spec-option active" onclick="selectSpec(this, '77')">77 (Medium)</div>
                    <div class="spec-option" onclick="selectSpec(this, '78')">78 (Fast)</div>
                </div>
            </div>
            <?php } ?>

            <?php if (strtolower($row['category']) == 'bags') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Capacity</p>
                <div class="spec-options">
                    <div class="spec-option active" onclick="selectSpec(this, '2 Rackets')">2 Rackets</div>
                    <div class="spec-option" onclick="selectSpec(this, '6 Rackets')">6 Rackets</div>
                    <div class="spec-option" onclick="selectSpec(this, '9 Rackets')">9 Rackets</div>
                </div>
            </div>
            <?php } ?>

            <?php if (strtolower($row['category']) == 'apparel') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Size (Unisex)</p>
                <div class="spec-options">
                    <div class="spec-option" onclick="selectSpec(this, 'S')">S</div>
                    <div class="spec-option active" onclick="selectSpec(this, 'M')">M</div>
                    <div class="spec-option" onclick="selectSpec(this, 'L')">L</div>
                    <div class="spec-option" onclick="selectSpec(this, 'XL')">XL</div>
                </div>
            </div>
            
            <div class="custom-service-box">
                <h4 class="service-title">Name Customization (专属印字)</h4>
                
                <label for="custom_name" class="service-label" style="margin-top: 0;">Print Name (印字内容 - 可选):</label>
                <input type="text" id="custom_name" name="custom_name" class="service-input" placeholder="e.g. LIN DAN (+ RM 20.00)" maxlength="15">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">* Max 15 characters. Leave blank if not required.</p>
                
                <!-- 💡 这里是自带动画效果的下拉折叠框 -->
                <details style="margin-top: 15px;">
                    <summary style="cursor: pointer; color: var(--red); font-weight: 700; font-size: 0.85rem; outline: none; padding: 5px 0;">
                        ▶ View Example (点击展开效果参考)
                    </summary>
                    <img src="images/example name.jpg" alt="Printing Example" style="width: 100%; max-width: 320px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 12px; display: block; box-shadow: 0 4px 12px rgba(0,0,0,0.05);" onerror="this.style.display='none'">
                </details>
            </div>
            <?php } ?>
            
            <div class="quantity-selector">
                <p class="quantity-selector-label">Quantity</p>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="changeQuantity(-1)">−</button>
                    <input type="text" class="quantity-input" id="quantity" value="1" readonly>
                    <button class="quantity-btn" onclick="changeQuantity(1)">+</button>
                </div>
            </div>
            
            <div class="shopping-guarantee">
                <div class="guarantee-item">
                    <span class="guarantee-icon">✓</span> Free Shipping
                </div>
                <div class="guarantee-item">
                    <span class="guarantee-icon">✓</span> Authentic Product
                </div>
                <div class="guarantee-item">
                    <span class="guarantee-icon">✓</span> 30-Day Return
                </div>
            </div>
            
            <div class="button-group">
                <a href="#" class="btn-add-to-cart">Add to Cart</a>
                <a href="#" class="btn-buy-now">Buy Now</a>
            </div>
            
        </div>
    </div>
    
    <hr class="divider">
    
    <div class="product-details-section">
        <h2 class="details-title">Product Details</h2>
        
        <div class="details-grid">
            
            <div class="detail-card">
                <h4>Key Technologies / Features</h4>
                <p>
                    Experience the pinnacle of Yonex engineering with the <strong><?php echo $row['name']; ?></strong>. 
                    Designed for maximum performance and durability to match your playing style on the court.
                </p>
            </div>
            
            <div class="detail-card">
                <h4>Specifications</h4>
                <table class="specs-table">
                    <tr>
                        <td>Category</td>
                        <td><?php echo $row['category']; ?></td>
                    </tr>
                    <tr>
                        <td>Series</td>
                        <td><?php echo $row['series']; ?></td>
                    </tr>
                    
                    <?php if(!empty($row['racket_flex'])) { ?>
                    <tr>
                        <td>Flex</td>
                        <td><?php echo $row['racket_flex']; ?></td>
                    </tr>
                    <?php } ?>
                    
                    <?php if(!empty($row['racket_balance'])) { ?>
                    <tr>
                        <td>Balance</td>
                        <td><?php echo $row['racket_balance']; ?></td>
                    </tr>
                    <?php } ?>
                    
                    <?php if(!empty($row['frame_material'])) { ?>
                    <tr>
                        <td>Frame Material</td>
                        <td><?php echo $row['frame_material']; ?></td>
                    </tr>
                    <?php } ?>
                    
                    <?php if(!empty($row['shaft_material'])) { ?>
                    <tr>
                        <td>Shaft Material</td>
                        <td><?php echo $row['shaft_material']; ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            
            <div class="detail-card">
                <h4>Description</h4>
                <p>
                    The <strong><?php echo $row['name']; ?></strong> is an excellent addition to your badminton gear. 
                    <?php echo $row['subtitle']; ?> 
                    <br><br>
                    Whether you are an aggressive attacker or a defensive tactician, Yonex equipment ensures consistent accuracy and power when you need it most.
                </p>
            </div>
            
        </div>
    </div>
    
</div>

<script>
    // 规格选择通用函数
    function selectSpec(element, spec) {
        // 找到当前点击元素所在的 spec-options 容器里的所有选项，移除 active
        const siblings = element.parentElement.querySelectorAll('.spec-option');
        siblings.forEach(option => {
            option.classList.remove('active');
        });
        // 给当前点击的加上 active
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
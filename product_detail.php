<?php
include 'db_connect.php'; 

// 1. 获取产品基本资料
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); 
    } else {
        die("数据库里找不到该产品！");
    }
} else {
    die("Product not specified. (没有指定产品ID)");
}

$current_category = strtolower($row['category']);

// 2. 动态获取 Service Options (穿线、磅数)
$string_options = [];
$tension_options = [];
$service_sql = "SELECT * FROM service_options ORDER BY id ASC";
$service_res = $conn->query($service_sql);
if ($service_res && $service_res->num_rows > 0) {
    while($opt = $service_res->fetch_assoc()) {
        if ($opt['service_type'] == 'string') $string_options[] = $opt;
        if ($opt['service_type'] == 'tension') $tension_options[] = $opt;
    }
}

// 💡 3. 新增：动态获取 Product Specs (尺码、重量)
$spec_options = [];
$spec_sql = "SELECT spec_value FROM product_specs WHERE category = '$current_category' ORDER BY id ASC";
$spec_res = $conn->query($spec_sql);
if ($spec_res && $spec_res->num_rows > 0) {
    while($s = $spec_res->fetch_assoc()) {
        $spec_options[] = $s['spec_value'];
    }
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
        .custom-service-box { background-color: var(--offwhite); border: 1px solid var(--border); border-radius: 8px; padding: 20px; margin-top: 20px; margin-bottom: 25px; }
        .service-title { font-size: 0.9rem; font-weight: 700; color: var(--red); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; border-bottom: 1px dashed var(--midgray); padding-bottom: 8px; }
        .service-label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--charcoal); margin-bottom: 8px; margin-top: 15px; }
        .service-input { width: 100%; padding: 10px 12px; font-family: inherit; font-size: 0.9rem; color: var(--charcoal); border: 1px solid var(--border); border-radius: 4px; background-color: var(--white); transition: border-color 0.3s; }
        .service-input:focus { outline: none; border-color: var(--charcoal); }
    </style>
</head>
<body>

<div class="product-detail-container">
    
    <div class="breadcrumb">
        <a href="index.php">Home</a>
        <span class="separator">/</span>
        <a href="<?php echo $current_category; ?>.php"><?php echo $row['category']; ?></a>
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
            <p class="product-subtitle"><?php echo $row['subtitle']; ?></p>
            <div class="product-price"><span class="currency">RM</span> <?php echo $row['price']; ?></div>
            
            <?php if ($current_category == 'rackets') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Weight / Grip Size</p>
                <div class="spec-options">
                    <?php 
                    $first = true;
                    foreach($spec_options as $val) {
                        $active = $first ? 'active' : '';
                        echo '<div class="spec-option '.$active.'" onclick="selectSpec(this, \''.$val.'\')">'.$val.'</div>';
                        $first = false;
                    }
                    ?>
                </div>
            </div>
            
            <div class="custom-service-box">
                <h4 class="service-title">Stringing Service (穿线服务)</h4>
                <label for="string_type" class="service-label" style="margin-top: 0;">String Type (线种):</label>
                <select name="string_type" id="string_type" class="service-input">
                    <?php foreach($string_options as $opt) { ?>
                        <option value="<?php echo htmlspecialchars($opt['option_name']); ?>">
                            <?php echo htmlspecialchars($opt['option_name']); ?> 
                            <?php echo ($opt['additional_price'] != 0) ? '(+ RM ' . number_format($opt['additional_price'], 2) . ')' : '- RM 0.00'; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="tension" class="service-label">Tension (磅数):</label>
                <select name="tension" id="tension" class="service-input">
                    <?php foreach($tension_options as $opt) { ?>
                        <option value="<?php echo htmlspecialchars($opt['option_name']); ?>">
                            <?php echo htmlspecialchars($opt['option_name']); ?>
                            <?php echo ($opt['additional_price'] != 0) ? '(+ RM ' . number_format($opt['additional_price'], 2) . ')' : ''; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <?php } ?>

            <?php if ($current_category == 'footwear') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Shoe Size (EU)</p>
                <div class="spec-options">
                    <?php 
                    $first = true;
                    foreach($spec_options as $val) {
                        $active = $first ? 'active' : '';
                        echo '<div class="spec-option '.$active.'" onclick="selectSpec(this, \''.$val.'\')">'.$val.'</div>';
                        $first = false;
                    }
                    ?>
                </div>
            </div>
            <?php } ?>

            <?php if ($current_category == 'apparel') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Size (Unisex)</p>
                <div class="spec-options">
                    <?php 
                    $first = true;
                    foreach($spec_options as $val) {
                        $active = $first ? 'active' : '';
                        echo '<div class="spec-option '.$active.'" onclick="selectSpec(this, \''.$val.'\')">'.$val.'</div>';
                        $first = false;
                    }
                    ?>
                </div>
            </div>
            
            <div class="custom-service-box">
                <h4 class="service-title">Name Customization (专属印字)</h4>
                <label for="custom_name" class="service-label" style="margin-top: 0;">Print Name (印字内容 - 可选):</label>
                <input type="text" id="custom_name" name="custom_name" class="service-input" placeholder="e.g. LIN DAN (+ RM 20.00)" maxlength="15">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">* Max 15 characters. Leave blank if not required.</p>
                
                <details style="margin-top: 15px;">
                    <summary style="cursor: pointer; color: var(--red); font-weight: 700; font-size: 0.85rem; outline: none; padding: 5px 0;">
                        ▶ View Example (点击展开效果参考)
                    </summary>
                    <img src="images/example name.jpg" alt="Printing Example" style="width: 100%; max-width: 320px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 12px; display: block; box-shadow: 0 4px 12px rgba(0,0,0,0.05);" onerror="this.style.display='none'">
                </details>
            </div>
            <?php } ?>

            <?php if ($current_category == 'bags') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Capacity</p>
                <div class="spec-options">
                    <div class="spec-option active" onclick="selectSpec(this, '2 Rackets')">2 Rackets</div>
                    <div class="spec-option" onclick="selectSpec(this, '6 Rackets')">6 Rackets</div>
                    <div class="spec-option" onclick="selectSpec(this, '9 Rackets')">9 Rackets</div>
                </div>
            </div>
            <?php } ?>

            <?php if ($current_category == 'shuttlecocks') { ?>
            <div class="spec-selector">
                <p class="spec-selector-label">Speed</p>
                <div class="spec-options">
                    <div class="spec-option" onclick="selectSpec(this, '76')">76 (Slow)</div>
                    <div class="spec-option active" onclick="selectSpec(this, '77')">77 (Medium)</div>
                    <div class="spec-option" onclick="selectSpec(this, '78')">78 (Fast)</div>
                </div>
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
                <div class="guarantee-item"><span class="guarantee-icon">✓</span> Free Shipping</div>
                <div class="guarantee-item"><span class="guarantee-icon">✓</span> Authentic Product</div>
                <div class="guarantee-item"><span class="guarantee-icon">✓</span> 30-Day Return</div>
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
                <p>Experience the pinnacle of Yonex engineering with the <strong><?php echo $row['name']; ?></strong>. Designed for maximum performance and durability to match your playing style on the court.</p>
            </div>
            <div class="detail-card">
                <h4>Specifications</h4>
                <table class="specs-table">
                    <tr><td>Category</td><td><?php echo $row['category']; ?></td></tr>
                    <tr><td>Series</td><td><?php echo $row['series']; ?></td></tr>
                    <?php if(!empty($row['racket_flex'])) { ?><tr><td>Flex</td><td><?php echo $row['racket_flex']; ?></td></tr><?php } ?>
                    <?php if(!empty($row['racket_balance'])) { ?><tr><td>Balance</td><td><?php echo $row['racket_balance']; ?></td></tr><?php } ?>
                    <?php if(!empty($row['frame_material'])) { ?><tr><td>Frame Material</td><td><?php echo $row['frame_material']; ?></td></tr><?php } ?>
                    <?php if(!empty($row['shaft_material'])) { ?><tr><td>Shaft Material</td><td><?php echo $row['shaft_material']; ?></td></tr><?php } ?>
                </table>
            </div>
            <div class="detail-card">
                <h4>Description</h4>
                <p>The <strong><?php echo $row['name']; ?></strong> is an excellent addition to your badminton gear. <?php echo $row['subtitle']; ?> <br><br>Whether you are an aggressive attacker or a defensive tactician, Yonex equipment ensures consistent accuracy and power when you need it most.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function selectSpec(element, spec) {
        const siblings = element.parentElement.querySelectorAll('.spec-option');
        siblings.forEach(option => option.classList.remove('active'));
        element.classList.add('active');
        console.log('Selected spec:', spec);
    }
    
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
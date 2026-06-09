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
$spec_sql = "SELECT DISTINCT spec_value FROM product_variants WHERE product_id = $id AND stock_quantity > 0 ORDER BY id ASC";
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
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="selected_spec" id="selected_spec" value="<?php echo !empty($spec_options) ? $spec_options[0] : ''; ?>">
                <input type="hidden" name="quantity" id="form_quantity" value="1">

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
                    <h4 class="service-title">Stringing Service</h4>
                    <label for="string_type" class="service-label" style="margin-top: 0;">String Type:</label>
                    <select name="string_option_id" id="string_type" class="service-input">
                        <option value="">No Stringing</option>
                        <?php foreach($string_options as $opt) { ?>
                            <option value="<?php echo $opt['id']; ?>">
                                <?php echo htmlspecialchars($opt['option_name']); ?> 
                                <?php echo ($opt['additional_price'] != 0) ? '(+ RM ' . number_format($opt['additional_price'], 2) . ')' : '- RM 0.00'; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label for="tension" class="service-label">Tension:</label>
                    <select name="tension_option_id" id="tension" class="service-input">
                        <option value="">Not Required</option>
                        <?php foreach($tension_options as $opt) { ?>
                            <option value="<?php echo $opt['id']; ?>">
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
                <?php } ?>

                <?php if ($current_category == 'bags') { ?>
                <div class="spec-selector">
                    <p class="spec-selector-label">Capacity</p>
                    <div class="spec-options">
                        <div class="spec-option active" onclick="selectSpec(this, '2 Rackets')">2 Rackets</div>
                        <div class="spec-option" onclick="selectSpec(this, '6 Rackets')">6 Rackets</div>
                        <div class="spec-option" onclick="selectSpec(this, '9 Rackets')">9 Rackets</div>
                        <script> document.getElementById('selected_spec').value = '2 Rackets'; </script>
                    </div>
                </div>
                <?php } ?>

                <?php if ($current_category == 'accessories') { ?>
                <div class="spec-selector">
                    <p class="spec-selector-label">Color</p>
                    <div class="spec-options">
                        <?php 
                        $first = true;
                        foreach($spec_options as $val) {
                            $active = $first ? 'active' : '';
                            echo '<div class="spec-option '.$active.'" onclick="selectSpec(this, \''.$val.'\')">'.$val.'</div>';
                            if($first) {
                                // 页面加载时，把第一个颜色自动填入隐藏的表单里
                                echo "<script> document.addEventListener('DOMContentLoaded', () => { document.getElementById('selected_spec').value = '$val'; }); </script>";
                            }
                            $first = false;
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>
                
                <div class="quantity-selector">
                    <p class="quantity-selector-label">Quantity</p>
                    <div class="quantity-controls">
                        <button type="button" class="quantity-btn" onclick="changeQuantity(-1)">−</button>
                        <input type="text" class="quantity-input" id="quantity_display" value="1" readonly>
                        <button type="button" class="quantity-btn" onclick="changeQuantity(1)">+</button>
                    </div>
                </div>
                
                <div class="shopping-guarantee">
                    <div class="guarantee-item"><span class="guarantee-icon">✓</span> Free Shipping</div>
                    <div class="guarantee-item"><span class="guarantee-icon">✓</span> Authentic Product</div>
                    <div class="guarantee-item"><span class="guarantee-icon">✓</span> 30-Day Return</div>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-add-to-cart" style="border:none; cursor:pointer; font-family:inherit; font-size:16px;">Add to Cart</button>
                    <button type="button" class="btn-buy-now" style="border:none; cursor:pointer; font-family:inherit; font-size:16px;" onclick="alert('Proceeding to checkout...');">Buy Now</button>
                </div>
            </form>
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
        
        // 更新隐藏的 input 值，提交表单时会发给数据库
        document.getElementById('selected_spec').value = spec;
        console.log('Selected spec updated to:', spec);
    }
    
    function changeQuantity(delta) {
        const displayInput = document.getElementById('quantity_display');
        const formInput = document.getElementById('form_quantity');
        
        let currentValue = parseInt(displayInput.value);
        let newValue = currentValue + delta;
        if (newValue < 1) newValue = 1;
        if (newValue > 10) newValue = 10;
        
        displayInput.value = newValue;
        // 同步更新隐藏表单里的数量
        formInput.value = newValue;
    }
</script>

</body>
</html>
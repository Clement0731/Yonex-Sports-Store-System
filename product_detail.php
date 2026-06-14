<?php
include 'db_connect.php'; 

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); 
    } else {
        die("Product not found in database.");
    }
} else {
    die("Product not specified.");
}

$current_category = strtolower($row['category']);
$url_category = urlencode(strtolower(str_replace(' ', '_', $row['category'])));

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

// Fetch dynamic variants from the database (Sizes, Colors, Capacities)
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
    <title><?php echo htmlspecialchars($row['name']); ?> - Yonex Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Breadcrumb Upgraded Styles */
        .breadcrumb {
            margin-bottom: 30px;
            font-size: 1.1rem; 
            font-weight: 500;
            color: var(--text-muted);
            display: flex;
            align-items: center;
        }
        .breadcrumb a {
            color: var(--charcoal);
            text-decoration: none;
            transition: color 0.3s ease, background-color 0.3s ease;
            font-weight: 700; 
            padding: 4px 8px;
            border-radius: 4px;
        }
        .breadcrumb a:hover {
            color: var(--white);
            background-color: var(--red); 
        }
        .breadcrumb .separator {
            margin: 0 12px;
            color: var(--midgray);
            font-weight: 400;
        }
        .breadcrumb span:last-child {
            color: var(--text-muted);
            padding: 4px 8px;
        }

        /* Other details styles */
        .custom-service-box { background-color: var(--offwhite); border: 1px solid var(--border); border-radius: 8px; padding: 20px; margin-top: 20px; margin-bottom: 25px; }
        .service-title { font-size: 0.9rem; font-weight: 700; color: var(--red); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; border-bottom: 1px dashed var(--midgray); padding-bottom: 8px; }
        .service-label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--charcoal); margin-bottom: 8px; margin-top: 15px; }
        .service-input { width: 100%; padding: 10px 12px; font-family: inherit; font-size: 0.9rem; color: var(--charcoal); border: 1px solid var(--border); border-radius: 4px; background-color: var(--white); transition: border-color 0.3s; }
        .service-input:focus { outline: none; border-color: var(--charcoal); }
        .service-input:disabled { background-color: #e9ecef; cursor: not-allowed; color: #9ca3af; }
    </style>
</head>
<body>

<div class="product-detail-container">
    
    <div class="breadcrumb">
        <a href="index.php?category=home">Home</a>
        <span class="separator">/</span>
        <a href="index.php?category=<?php echo $url_category; ?>"><?php echo htmlspecialchars($row['category']); ?></a>
        <span class="separator">/</span>
        <span><?php echo htmlspecialchars($row['name']); ?></span>
    </div>
    
    <div class="product-main">
        <div class="product-image-section">
            <img src="images/<?php echo basename($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
        </div>
        
        <div class="product-info-section">
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="selected_spec" id="selected_spec" value="<?php echo !empty($spec_options) ? htmlspecialchars($spec_options[0]) : ''; ?>">
                <input type="hidden" name="quantity" id="form_quantity" value="1">
                
                <input type="hidden" name="action_type" id="action_type" value="cart">

                <span class="product-series-badge"><?php echo htmlspecialchars($row['series']); ?> SERIES</span>
                <h1 class="product-name"><?php echo htmlspecialchars($row['name']); ?></h1>
                <p class="product-subtitle"><?php echo htmlspecialchars($row['subtitle']); ?></p>
                <div class="product-price"><span class="currency">RM</span> <?php echo number_format($row['price'], 2); ?></div>
                
                <?php if ($current_category == 'rackets') { ?>
                <div class="spec-selector">
                    <p class="spec-selector-label">Weight / Grip Size</p>
                    <div class="spec-options">
                        <?php 
                        $first = true;
                        foreach($spec_options as $val) {
                            $active = $first ? 'active' : '';
                            echo '<div class="spec-option '.$active.'" onclick="selectSpec(this, \''.htmlspecialchars($val).'\')">'.htmlspecialchars($val).'</div>';
                            if($first) {
                                echo "<script> document.addEventListener('DOMContentLoaded', () => { document.getElementById('selected_spec').value = '".htmlspecialchars($val)."'; }); </script>";
                            }
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
                        <option value="" id="default_tension_opt">Not Required</option>
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
                            echo '<div class="spec-option '.$active.'" onclick="selectSpec(this, \''.htmlspecialchars($val).'\')">'.htmlspecialchars($val).'</div>';
                            if($first) {
                                echo "<script> document.addEventListener('DOMContentLoaded', () => { document.getElementById('selected_spec').value = '".htmlspecialchars($val)."'; }); </script>";
                            }
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
                            echo '<div class="spec-option '.$active.'" onclick="selectSpec(this, \''.htmlspecialchars($val).'\')">'.htmlspecialchars($val).'</div>';
                            if($first) {
                                echo "<script> document.addEventListener('DOMContentLoaded', () => { document.getElementById('selected_spec').value = '".htmlspecialchars($val)."'; }); </script>";
                            }
                            $first = false;
                        }
                        ?>
                    </div>
                </div>

                <?php 
                // Smart exclusion: Check if the product name contains "short", "pant", or "skirt"
                $is_bottom = (stripos($row['name'], 'short') !== false || stripos($row['name'], 'pant') !== false || stripos($row['name'], 'skirt') !== false);
                
                // Only show printing service if it is NOT a bottom wear
                if (!$is_bottom) { 
                ?>
                <div class="custom-service-box">
                    <h4 class="service-title">Name Printing Service</h4>
                    <label class="service-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 0;">
                        <input type="checkbox" id="enable_printing" name="enable_printing" value="yes" onchange="togglePrinting()" style="width: 18px; height: 18px; cursor: pointer;"> 
                        Add Custom Name Printing (+ RM 15.00)
                    </label>
                    
                    <div id="printing_input_div" style="display: none; margin-top: 15px;">
                        <label for="custom_name" class="service-label" style="margin-top: 0;">Enter Name (Max 5 Letters):</label>
                        <input type="text" name="custom_name" id="custom_name" class="service-input" maxlength="5" placeholder="e.g. LIN D" style="text-transform: uppercase; font-weight: bold; letter-spacing: 2px;" pattern="[A-Za-z\s]+">
                        <small style="color: #64748b; font-size: 11px; margin-top: 5px; display: block;">* Only English alphabets and spaces allowed. Max 5 characters.</small>
                    </div>
                </div>
                <?php } ?>
                <?php } ?>

                <?php if ($current_category == 'bags') { ?>
                <div class="spec-selector">
                    <p class="spec-selector-label">Color / Variant</p>
                    <div class="spec-options">
                        <?php 
                        $first = true;
                        foreach($spec_options as $val) {
                            $active = $first ? 'active' : '';
                            echo '<div class="spec-option '.$active.'" onclick="selectSpec(this, \''.htmlspecialchars($val).'\')">'.htmlspecialchars($val).'</div>';
                            if($first) {
                                echo "<script> document.addEventListener('DOMContentLoaded', () => { document.getElementById('selected_spec').value = '".htmlspecialchars($val)."'; }); </script>";
                            }
                            $first = false;
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>

                <?php if ($current_category == 'accessories' || (!in_array($current_category, ['rackets', 'footwear', 'apparel', 'bags']))) { ?>
                <div class="spec-selector">
                    <p class="spec-selector-label">Variant</p>
                    <div class="spec-options">
                        <?php 
                        $first = true;
                        foreach($spec_options as $val) {
                            $active = $first ? 'active' : '';
                            echo '<div class="spec-option '.$active.'" onclick="selectSpec(this, \''.htmlspecialchars($val).'\')">'.htmlspecialchars($val).'</div>';
                            if($first) {
                                echo "<script> document.addEventListener('DOMContentLoaded', () => { document.getElementById('selected_spec').value = '".htmlspecialchars($val)."'; }); </script>";
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
                    <div class="guarantee-item"><span class="guarantee-icon">✓</span> 100% Authentic Product</div>
                    <div class="guarantee-item"><span class="guarantee-icon">✓</span> Authorized Dealer</div>
                    <div class="guarantee-item"><span class="guarantee-icon">✓</span> Secure Checkout</div>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-add-to-cart" style="border:none; cursor:pointer; font-family:inherit; font-size:16px;" onclick="document.getElementById('action_type').value='cart';">Add to Cart</button>
                    
                    <button type="submit" class="btn-buy-now" style="border:none; cursor:pointer; font-family:inherit; font-size:16px;" onclick="document.getElementById('action_type').value='buy_now';">Buy Now</button>
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
                <p>Experience the pinnacle of Yonex engineering with the <strong><?php echo htmlspecialchars($row['name']); ?></strong>. Designed for maximum performance and durability to match your playing style on the court.</p>
            </div>
            <div class="detail-card">
                <h4>Specifications</h4>
                <table class="specs-table">
                    <tr><td>Category</td><td><?php echo htmlspecialchars($row['category']); ?></td></tr>
                    <tr><td>Series</td><td><?php echo htmlspecialchars($row['series']); ?></td></tr>
                    <?php if(!empty($row['racket_flex'])) { ?><tr><td>Flex</td><td><?php echo htmlspecialchars($row['racket_flex']); ?></td></tr><?php } ?>
                    <?php if(!empty($row['racket_balance'])) { ?><tr><td>Balance</td><td><?php echo htmlspecialchars($row['racket_balance']); ?></td></tr><?php } ?>
                    <?php if(!empty($row['frame_material'])) { ?><tr><td>Frame Material</td><td><?php echo htmlspecialchars($row['frame_material']); ?></td></tr><?php } ?>
                    <?php if(!empty($row['shaft_material'])) { ?><tr><td>Shaft Material</td><td><?php echo htmlspecialchars($row['shaft_material']); ?></td></tr><?php } ?>
                </table>
            </div>
            <div class="detail-card">
                <h4>Description</h4>
                <p>The <strong><?php echo htmlspecialchars($row['name']); ?></strong> is an excellent addition to your badminton gear. <?php echo htmlspecialchars($row['subtitle']); ?> <br><br>Whether you are an aggressive attacker or a defensive tactician, Yonex equipment ensures consistent accuracy and power when you need it most.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function selectSpec(element, spec) {
        const siblings = element.parentElement.querySelectorAll('.spec-option');
        siblings.forEach(option => option.classList.remove('active'));
        element.classList.add('active');
        
        document.getElementById('selected_spec').value = spec;
    }
    
    function changeQuantity(delta) {
        const displayInput = document.getElementById('quantity_display');
        const formInput = document.getElementById('form_quantity');
        
        let currentValue = parseInt(displayInput.value);
        let newValue = currentValue + delta;
        if (newValue < 1) newValue = 1;
        if (newValue > 10) newValue = 10;
        
        displayInput.value = newValue;
        formInput.value = newValue;
    }

    // 衣服印字服务专属切换逻辑
    function togglePrinting() {
        const checkbox = document.getElementById('enable_printing');
        const inputDiv = document.getElementById('printing_input_div');
        const inputField = document.getElementById('custom_name');
        
        if (checkbox && checkbox.checked) {
            inputDiv.style.display = 'block';
            inputField.setAttribute('required', 'required'); 
        } else {
            inputDiv.style.display = 'none';
            inputField.removeAttribute('required');
            inputField.value = ''; 
        }
    }

    // 球拍穿线服务逻辑
    document.addEventListener('DOMContentLoaded', function() {
        const stringTypeSelect = document.getElementById('string_type');
        const tensionSelect = document.getElementById('tension');
        const defaultTensionOpt = document.getElementById('default_tension_opt');

        if (stringTypeSelect && tensionSelect) {
            function updateTensionState() {
                if (stringTypeSelect.value === "") {
                    tensionSelect.disabled = true; 
                    defaultTensionOpt.disabled = false; 
                    defaultTensionOpt.textContent = "Not Required"; 
                    tensionSelect.value = ""; 
                    tensionSelect.removeAttribute('required'); 
                    
                    Array.from(tensionSelect.options).forEach(opt => {
                        opt.disabled = false;
                    });
                } else {
                    tensionSelect.disabled = false; 
                    defaultTensionOpt.textContent = "-- Please Select Tension --"; 
                    defaultTensionOpt.disabled = true; 
                    
                    tensionSelect.setAttribute('required', 'required'); 
                    
                    if(tensionSelect.value === "") {
                        tensionSelect.value = ""; 
                    }

                    Array.from(tensionSelect.options).forEach(opt => {
                        if (opt.text.toLowerCase().includes('no tension') && opt.value !== "") {
                            opt.disabled = true; 
                            if(tensionSelect.value === opt.value) {
                                tensionSelect.value = ""; 
                            }
                        } else if (opt.value !== "") {
                            opt.disabled = false;
                        }
                    });
                }
            }
            
            updateTensionState();
            stringTypeSelect.addEventListener('change', updateTensionState);
        }
    });
</script>

</body>
</html>
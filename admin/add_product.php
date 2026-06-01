<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['prod_name']);
    $subtitle = $conn->real_escape_string($_POST['subtitle']);
    $category = $conn->real_escape_string($_POST['category']);
    $tag = $conn->real_escape_string($_POST['tag']);
    $series_name = $conn->real_escape_string($_POST['series_name']);
    $price = $_POST['price'];
    $qty = (int)$_POST['stock_qty'];

    $racket_flex = $conn->real_escape_string($_POST['racket_flex']);
    $racket_balance = $conn->real_escape_string($_POST['racket_balance']);
    $string_tension = $conn->real_escape_string($_POST['string_tension']);
    $frame_material = $conn->real_escape_string($_POST['frame_material']);
    $shaft_material = $conn->real_escape_string($_POST['shaft_material']);
    
    $color = $conn->real_escape_string($_POST['color']);
    $description = $conn->real_escape_string($_POST['description']);
    $key_technologies = $conn->real_escape_string($_POST['key_technologies']);
    
    $image_file = "default.png";
    
    if (isset($_FILES["prod_image"]) && $_FILES["prod_image"]["error"] == 0) {
        $target_dir = "../images/"; 
        $file_extension = pathinfo($_FILES["prod_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        if (move_uploaded_file($_FILES["prod_image"]["tmp_name"], $target_file)) {
            $image_file = '/FYP/Yonex-Sports-Store-System/images/' . $new_filename; 
        }
    }

    $sql = "INSERT INTO products (name, subtitle, category, series, price, tag, image_url, racket_flex, racket_balance, string_tension, color, frame_material, shaft_material, description, key_technologies) 
            VALUES ('$name', '$subtitle', '$category', '$series_name', '$price', '$tag', '$image_file', '$racket_flex', '$racket_balance', '$string_tension', '$color', '$frame_material', '$shaft_material', '$description', '$key_technologies')";
    
    if ($conn->query($sql) === TRUE) {
        $new_product_id = $conn->insert_id; 
        $sql_variant = "INSERT INTO product_variants (product_id, spec_type, spec_value, stock_quantity) VALUES ($new_product_id, 'Standard', 'Standard', '$qty')";
        $conn->query($sql_variant);
        header("Location: manage_product.php");
        exit();
    } else {
        $error = "Error adding product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Full Product Details</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-row .form-group { flex: 1; margin-bottom: 0; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; }
        .section-title { margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 5px; color: #1e293b; }
        .dynamic-specs-note { font-size: 13px; color: #eab308; margin-bottom: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1>Add New Product Details</h1>
        </div>
        <div class="table-box" style="max-width: 900px;">
            <?php if(isset($error)) { echo "<p class='error-msg' style='color:red;'>$error</p>"; } ?>
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                
                <h3 class="section-title">1. Basic Information</h3>
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label>Product Name</label>
                        <input type="text" name="prod_name" required>
                    </div>
                    <div class="form-group">
                        <label>Series</label>
                        <input type="text" name="series_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Product Subtitle (Short description under the name)</label>
                    <textarea name="subtitle" rows="2" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="category" required onchange="changeCategory()" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="">-- Select Category --</option>
                            <?php
                             // 去数据库抓取所有分类
                                    $cat_dropdown_query = $conn->query("SELECT category_name FROM categories ORDER BY id ASC");
                                    if ($cat_dropdown_query && $cat_dropdown_query->num_rows > 0) {
                                    while($cat_row = $cat_dropdown_query->fetch_assoc()) {
                                    $c_name = htmlspecialchars($cat_row['category_name']);
                                    echo "<option value='{$c_name}'>{$c_name}</option>";
                                    }
                                 }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tag</label>
                        <select name="tag">
                            <option value="">None</option>
                            <option value="HOT">HOT</option>
                            <option value="NEW">NEW</option>
                            <option value="SALE">SALE</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price (RM)</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Total Stock</label>
                        <input type="number" name="stock_qty" required>
                    </div>
                </div>

                <h3 class="section-title">2. Product Specifications</h3>
                <p class="dynamic-specs-note">💡 The fields below automatically change based on the Category selected above.</p>
                
                <div class="form-row">
                    <div class="form-group">
                        <label id="lbl_spec1">Flex (e.g., Stiff, Medium)</label>
                        <input type="text" name="racket_flex">
                    </div>
                    <div class="form-group">
                        <label id="lbl_spec2">Balance (e.g., Head Heavy)</label>
                        <input type="text" name="racket_balance">
                    </div>
                    <div class="form-group">
                        <label id="lbl_spec3">String Tension (e.g., 20-28 lbs)</label>
                        <input type="text" name="string_tension">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label id="lbl_spec4">Frame Material</label>
                        <input type="text" name="frame_material">
                    </div>
                    <div class="form-group">
                        <label id="lbl_spec5">Shaft Material</label>
                        <input type="text" name="shaft_material">
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color">
                    </div>
                </div>

                <h3 class="section-title">3. Descriptions & Technologies</h3>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>Key Technologies / Features</label>
                    <textarea name="key_technologies" rows="4"></textarea>
                </div>

                <h3 class="section-title">4. Product Image</h3>
                <div class="form-group">
                    <input type="file" name="prod_image" accept="image/*" required>
                </div>
                <br><br>
                <button type="submit" class="btn btn-add" style="font-size:16px; padding:10px 30px;">Save Product</button>
                <a href="manage_product.php" class="btn" style="background:#cbd5e1; color:#1e293b; margin-left:10px; text-decoration:none; padding:10px 30px; border-radius:4px;">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        const specTemplates = {
            'Rackets': ['Flex (e.g., Stiff)', 'Balance (e.g., Head Heavy)', 'String Tension (lbs)', 'Frame Material', 'Shaft Material'],
            'Footwear': ['Upper Material', 'Midsole', 'Outsole', 'Shoe Width (e.g., Standard)', 'Weight (Optional)'],
            'Shuttlecocks': ['Speed (e.g., 77 speed)', 'Application (e.g., Tournament)', 'Quantity per Tube', 'Skirt Material (e.g., Goose Feather)', 'Base Material (e.g., Cork)'],
            'Apparel': ['Material (e.g., 100% Polyester)', 'Fit Type (e.g., Regular Fit)', 'Gender (e.g., Unisex)', 'Fabric Tech (e.g., Quick Dry)', 'Extra Detail'],
            'Bags': ['Compartments (e.g., 2 Main)', 'Capacity (e.g., 6 Rackets)', 'Straps (e.g., Double)', 'Outer Material', 'Inner Material'],
            'Accessories': ['Type (e.g., Towel, Grip)', 'Dimensions / Size', 'Features / Feel', 'Material', 'Application / Qty']
        };

        function updateSpecLabels() {
            const category = document.getElementById('categorySelector').value;
            const labels = specTemplates[category] || specTemplates['Rackets'];

            document.getElementById('lbl_spec1').innerText = labels[0];
            document.getElementById('lbl_spec2').innerText = labels[1];
            document.getElementById('lbl_spec3').innerText = labels[2];
            document.getElementById('lbl_spec4').innerText = labels[3];
            document.getElementById('lbl_spec5').innerText = labels[4];
        }

        window.onload = updateSpecLabels;
    </script>
</body>
</html>
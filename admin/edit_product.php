<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql_get = "SELECT p.*, IFNULL(SUM(v.stock_quantity), 0) as total_stock 
                FROM products p 
                LEFT JOIN product_variants v ON p.id = v.product_id 
                WHERE p.id = $id GROUP BY p.id";
    $result = $conn->query($sql_get);
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        header("Location: manage_product.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['product_id'];
    
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
    $color = $conn->real_escape_string($_POST['color']);
    $frame_material = $conn->real_escape_string($_POST['frame_material']);
    $shaft_material = $conn->real_escape_string($_POST['shaft_material']);
    $description = $conn->real_escape_string($_POST['description']);
    $key_technologies = $conn->real_escape_string($_POST['key_technologies']);

    $image_update_sql = ""; 
    if (isset($_FILES["prod_image"]) && $_FILES["prod_image"]["error"] == 0) {
        $target_dir = "../images/"; 
        $file_extension = pathinfo($_FILES["prod_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        if (move_uploaded_file($_FILES["prod_image"]["tmp_name"], $target_file)) {
            $full_path = '/FYP/Yonex-Sports-Store-System/images/' . $new_filename;
            $image_update_sql = ", image_url = '$full_path'"; 
        }
    }

    $sql = "UPDATE products SET 
            name = '$name', subtitle = '$subtitle', category = '$category', tag = '$tag', series = '$series_name', price = '$price',
            racket_flex = '$racket_flex', racket_balance = '$racket_balance', string_tension = '$string_tension',
            color = '$color', frame_material = '$frame_material', shaft_material = '$shaft_material',
            description = '$description', key_technologies = '$key_technologies'
            $image_update_sql 
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $check_var = $conn->query("SELECT id FROM product_variants WHERE product_id = $id LIMIT 1");
        if($check_var->num_rows > 0) {
            $var_id = $check_var->fetch_assoc()['id'];
            $conn->query("UPDATE product_variants SET stock_quantity = $qty WHERE id = $var_id");
        } else {
            $conn->query("INSERT INTO product_variants (product_id, spec_type, spec_value, stock_quantity) VALUES ($id, 'Standard', 'Standard', $qty)");
        }
        header("Location: manage_product.php");
        exit();
    } else {
        $error = "Error updating product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product Details</title>
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
            <h1>Edit Product Details</h1>
        </div>
        <div class="table-box" style="max-width: 900px;">
            <?php if(isset($error)) { echo "<p class='error-msg' style='color:red;'>$error</p>"; } ?>
            
            <form action="edit_product.php?id=<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
                <h3 class="section-title">1. Basic Information</h3>
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label>Product Name</label>
                        <input type="text" name="prod_name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Series</label>
                        <input type="text" name="series_name" value="<?= htmlspecialchars($product['series']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Product Subtitle</label>
                    <textarea name="subtitle" rows="2" required><?= htmlspecialchars($product['subtitle'] ?? '') ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="categorySelector" onchange="updateSpecLabels()" required>
                            <option value="Rackets" <?= $product['category'] == 'Rackets' ? 'selected' : '' ?>>Rackets</option>
                            <option value="Footwear" <?= $product['category'] == 'Footwear' ? 'selected' : '' ?>>Footwear</option>
                            <option value="Shuttlecocks" <?= $product['category'] == 'Shuttlecocks' ? 'selected' : '' ?>>Shuttlecocks</option>
                            <option value="Apparel" <?= $product['category'] == 'Apparel' ? 'selected' : '' ?>>Apparel</option>
                            <option value="Bags" <?= $product['category'] == 'Bags' ? 'selected' : '' ?>>Bags</option>
                            <option value="Accessories" <?= $product['category'] == 'Accessories' ? 'selected' : '' ?>>Accessories</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tag</label>
                        <select name="tag">
                            <option value="" <?= empty($product['tag']) ? 'selected' : '' ?>>None</option>
                            <option value="HOT" <?= $product['tag'] == 'HOT' ? 'selected' : '' ?>>HOT</option>
                            <option value="NEW" <?= $product['tag'] == 'NEW' ? 'selected' : '' ?>>NEW</option>
                            <option value="SALE" <?= $product['tag'] == 'SALE' ? 'selected' : '' ?>>SALE</option>
                            <option value="DISCOUNT" <?= $product['tag'] == 'DISCOUNT' ? 'selected' : '' ?>>DISCOUNT</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price (RM)</label>
                        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Total Stock</label>
                        <input type="number" name="stock_qty" value="<?= $product['total_stock'] ?>" required>
                    </div>
                </div>

                <h3 class="section-title">2. Product Specifications</h3>
                <p class="dynamic-specs-note">💡 The labels below automatically adapt to the chosen Category.</p>

                <div class="form-row">
                    <div class="form-group">
                        <label id="lbl_spec1">Spec 1</label>
                        <input type="text" name="racket_flex" value="<?= htmlspecialchars($product['racket_flex']) ?>">
                    </div>
                    <div class="form-group">
                        <label id="lbl_spec2">Spec 2</label>
                        <input type="text" name="racket_balance" value="<?= htmlspecialchars($product['racket_balance']) ?>">
                    </div>
                    <div class="form-group">
                        <label id="lbl_spec3">Spec 3</label>
                        <input type="text" name="string_tension" value="<?= htmlspecialchars($product['string_tension']) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label id="lbl_spec4">Spec 4</label>
                        <input type="text" name="frame_material" value="<?= htmlspecialchars($product['frame_material']) ?>">
                    </div>
                    <div class="form-group">
                        <label id="lbl_spec5">Spec 5</label>
                        <input type="text" name="shaft_material" value="<?= htmlspecialchars($product['shaft_material']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" value="<?= htmlspecialchars($product['color']) ?>">
                    </div>
                </div>

                <h3 class="section-title">3. Descriptions & Technologies</h3>
                <div class="form-group">
                    <label>Description (Supports HTML like &lt;br&gt; for new lines)</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Key Technologies</label>
                    <textarea name="key_technologies" rows="4"><?= htmlspecialchars($product['key_technologies']) ?></textarea>
                </div>

                <h3 class="section-title">4. Product Image</h3>
                <div class="form-group">
                    <img src="<?= !empty($product['image_url']) ? '../images/'.basename($product['image_url']) : '' ?>" style="max-width: 150px; display: block; margin-bottom: 10px; border: 1px solid #ccc;">
                    <label>Upload New Image (Leave empty to keep current)</label>
                    <input type="file" name="prod_image" accept="image/*">
                </div>
                <br><br>
                <button type="submit" class="btn btn-add" style="background:#c9a84c; font-size:16px; padding:10px 30px;">Save All Changes</button>
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
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM products WHERE PRODUCT_ID = $id");
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        header("Location: manage_product.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['product_id'];
    $name = $_POST['prod_name'];
    $category = $_POST['category'];
    $tag = $_POST['tag'];
    $series_name = $_POST['series_name'];
    $price = $_POST['price'];
    $qty = $_POST['stock_qty'];

    $image_update_sql = ""; 
    if (isset($_FILES["prod_image"]) && $_FILES["prod_image"]["error"] == 0) {
        $target_dir = "../images/"; 
        $file_extension = pathinfo($_FILES["prod_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["prod_image"]["tmp_name"], $target_file)) {
            $image_update_sql = ", IMAGE_FILE = '$new_filename'"; 
        }
    }

    $sql = "UPDATE products SET 
            PROD_NAME = '$name', 
            CATEGORY = '$category', 
            TAG = '$tag', 
            SERIES_NAME = '$series_name', 
            PRICE = '$price', 
            STOCK_QTY = '$qty' 
            $image_update_sql 
            WHERE PRODUCT_ID = $id";

            if ($conn->query($sql) === TRUE) {
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
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1>Edit Product</h1>
        </div>
        <div class="table-box" style="max-width: 600px;">
            <?php if(isset($error)) { echo "<p class='error-msg' style='color:red;'>$error</p>"; } ?>
            
            <form action="edit_product.php?id=<?= $product['PRODUCT_ID'] ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $product['PRODUCT_ID'] ?>">
                
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="prod_name" value="<?= $product['PROD_NAME'] ?>" required>
                </div>
                
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="Rackets" <?= $product['CATEGORY'] == 'Rackets' ? 'selected' : '' ?>>Rackets</option>
                            <option value="Footwear" <?= $product['CATEGORY'] == 'Footwear' ? 'selected' : '' ?>>Footwear</option>
                            <option value="Shuttlecocks" <?= $product['CATEGORY'] == 'Shuttlecocks' ? 'selected' : '' ?>>Shuttlecocks</option>
                            <option value="Bags" <?= $product['CATEGORY'] == 'Bags' ? 'selected' : '' ?>>Bags</option>
                            <option value="Apparel" <?= $product['CATEGORY'] == 'Apparel' ? 'selected' : '' ?>>Apparel</option>
                            <option value="Accessories" <?= $product['CATEGORY'] == 'Accessories' ? 'selected' : '' ?>>Accessories</option>
                            <option value="Strings" <?= $product['CATEGORY'] == 'Strings' ? 'selected' : '' ?>>Strings</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Product Tag</label>
                        <select name="tag">
                            <option value="" <?= $product['TAG'] == '' ? 'selected' : '' ?>>None</option>
                            <option value="HOT" <?= $product['TAG'] == 'HOT' ? 'selected' : '' ?>>HOT</option>
                            <option value="NEW" <?= $product['TAG'] == 'NEW' ? 'selected' : '' ?>>NEW</option>
                            <option value="SALE" <?= $product['TAG'] == 'SALE' ? 'selected' : '' ?>>SALE</option>
                            <option value="DISCOUNT" <?= $product['TAG'] == 'DISCOUNT' ? 'selected' : '' ?>>DISCOUNT</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Series Name </label>
                    <input type="text" name="series_name" value="<?= $product['SERIES_NAME'] ?>" required>
                </div>

                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Price (RM)</label>
                        <input type="number" step="0.01" name="price" value="<?= $product['PRICE'] ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock_qty" value="<?= $product['STOCK_QTY'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Update Product Image (Optional)</label>
                    <br>
                    <small style="color:#666;">Current Image: <b><?= $product['IMAGE_FILE'] ?></b> (Leave empty if no change)</small>
                    <input type="file" name="prod_image" accept="image/*" style="margin-top: 5px;">
                </div>
                <br>
                <button type="submit" class="btn btn-add" style="background:#c9a84c;">Update Product</button>
                <a href="manage_product.php" class="btn" style="background:#cbd5e1; color:#1e293b; margin-left:10px; text-decoration:none; padding:8px 15px; border-radius:4px;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
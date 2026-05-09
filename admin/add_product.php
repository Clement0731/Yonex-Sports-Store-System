<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['prod_name'];
    $price = $_POST['price'];
    $qty = $_POST['stock_qty'];
    $category = $_POST['category'];
    $specs = $_POST['specs'];
    
    $image_path = "";
    if (isset($_FILES["prod_image"]) && $_FILES["prod_image"]["error"] == 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["prod_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["prod_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    $sql = "INSERT INTO PRODUCTS (PROD_NAME, PRICE, STOCK_QTY, CATEGORY, SPECS, IMAGE_PATH) 
            VALUES ('$name', '$price', '$qty', '$category', '$specs', '$image_path')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_product.php");
        exit();
    } else {
        $error = "Error adding product!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1>Add New Product</h1>
        </div>
        <div class="table-box" style="max-width: 600px;">
            <?php if(isset($error)) { echo "<p class='error-msg'>$error</p>"; } ?>
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="prod_name" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="Rackets">Rackets</option>
                        <option value="Footwear">Footwear</option>
                        <option value="Shuttlecocks">Shuttlecocks</option>
                        <option value="Bags">Bags</option>
                        <option value="Apparel">Apparel</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Strings">Strings</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Available Specs / Sizes / Series</label>
                    <input type="text" name="specs" placeholder="e.g., 4U/G5 OR Unisex M OR ASTROX Series" required>
                </div>
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Price (RM)</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock_qty" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="prod_image" accept="image/*" required>
                </div>
                <br>
                <button type="submit" class="btn btn-add">Save Product</button>
                <a href="manage_product.php" class="btn" style="background:#cbd5e1; color:#1e293b; margin-left:10px;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
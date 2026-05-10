<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if(isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $conn->query("DELETE FROM PRODUCTS WHERE PRODUCT_ID = $del_id");
    header("Location: manage_product.php");
    exit();
}

$where_clause = "";
$page_title = "All Products";
if (isset($_GET['category'])) {
    $cat = $conn->real_escape_string($_GET['category']);
    $where_clause = "WHERE CATEGORY = '$cat'";
    $page_title = $cat . " Products";
}
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE PROD_NAME LIKE '%$search%' OR SERIES_NAME LIKE '%$search%'";
    $page_title = "Search Results: '" . htmlspecialchars($search) . "'";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1><?php echo $page_title; ?></h1>
            <a href="add_product.php" class="btn btn-add">+ Add New Product</a>
        </div>
        
        <div style="background: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    
    <form action="manage_product.php" method="GET" style="display: flex; gap: 10px; align-items: center; margin: 0;">
        <input type="text" name="search" placeholder="Search product or series..." 
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
               style="padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
        
        <button type="submit" class="btn btn-add" style="padding: 10px 20px;">Search</button>
        
        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
            <a href="manage_product.php" class="btn" style="background:#cbd5e1; color:#1e293b; text-decoration:none; padding: 10px 20px;">Clear</a>
        <?php endif; ?>
    </form>

    <span style="color: #666; font-size: 14px;">
        Use keyword to find products quickly
    </span>
</div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Series</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $products = $conn->query("SELECT * FROM PRODUCTS $where_clause ORDER BY PRODUCT_ID DESC");
                    if ($products->num_rows > 0) {
                        while($row = $products->fetch_assoc()) {
                            $img_src = !empty($row['IMAGE_FILE']) ? '../images/' . $row['IMAGE_FILE'] : 'https://via.placeholder.com/50';
                                echo "<tr>
                                    <td><img src='".$img_src."' style='width: 150px; height: 150px; object-fit: contain; border-radius: 4px;'></td>
                                    <td style='font-weight:bold;'>".$row['PROD_NAME']."</td>
                                    <td>".$row['CATEGORY']."</td>
                                    <td style='font-size:12px; color:#666;'>".$row['SERIES_NAME']."</td>
                                    <td>".$row['STOCK_QTY']."</td>
                                    <td>RM ".number_format($row['PRICE'], 2)."</td>
                                    <td>
                                        <a href='edit_product.php?id=".$row['PRODUCT_ID']."' class='btn' style='background:#c9a84c; color:#fff; text-decoration:none; padding:6px 12px; border-radius:4px; margin-right:5px; font-size: 14px;'>Edit</a>
                                        <a href='manage_product.php?delete=".$row['PRODUCT_ID']."' class='btn btn-delete' style='padding:6px 12px; font-size: 14px;'>Delete</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No products found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
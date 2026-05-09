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
            <a href="add_product.php" class="btn btn-add">+ Add Product</a>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Specs</th>
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
                            $img_src = !empty($row['IMAGE_PATH']) ? $row['IMAGE_PATH'] : 'https://via.placeholder.com/50';
                            echo "<tr>
                                    <td><img src='".$img_src."' style='width: 50px; height: 50px; object-fit: cover; border-radius: 4px;'></td>
                                    <td style='font-weight:bold;'>".$row['PROD_NAME']."</td>
                                    <td>".$row['CATEGORY']."</td>
                                    <td style='font-size:12px; color:#666;'>".$row['SPECS']."</td>
                                    <td>".$row['STOCK_QTY']."</td>
                                    <td>RM ".number_format($row['PRICE'], 2)."</td>
                                    <td>
                                        <a href='manage_product.php?delete=".$row['PRODUCT_ID']."' class='btn btn-delete'>Delete</a>
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
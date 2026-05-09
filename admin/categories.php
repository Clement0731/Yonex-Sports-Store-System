<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$categories = ['Rackets', 'Footwear', 'Shuttlecocks', 'Bags', 'Apparel', 'Accessories', 'Strings'];
$counts = [];

foreach ($categories as $cat) {
    $q = $conn->query("SELECT COUNT(*) as cnt FROM PRODUCTS WHERE CATEGORY = '$cat'");
    $row = $q->fetch_assoc();
    $counts[$cat] = $row['cnt'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; }
        .cat-card { background: white; padding: 40px 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-align: center; text-decoration: none; display: flex; flex-direction: column; justify-content: center; align-items: center; transition: 0.3s; height: 160px; border: 1px solid #eef2f5; }
        .cat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); border-color: #d0021b; }
        .cat-card h3 { color: #1e293b; margin-bottom: 12px; font-size: 20px; font-weight: 800; }
        .cat-card p { font-size: 14px; color: #d0021b; font-weight: 700; background: #fff0f2; padding: 6px 16px; border-radius: 20px; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1>Product Categories</h1>
            <a href="add_product.php" class="btn btn-add" style="width: auto;">+ Add New Product</a>
        </div>
        <div class="cat-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="manage_product.php?category=<?php echo urlencode($cat); ?>" class="cat-card">
                <h3><?php echo strtoupper($cat); ?></h3>
                <p><?php echo $counts[$cat]; ?> Items</p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
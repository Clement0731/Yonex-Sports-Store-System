<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$categories = ['Rackets', 'Footwear', 'Shuttlecocks', 'Bags', 'Apparel', 'Accessories'];
$counts = [];

foreach ($categories as $cat) {
    $q = $conn->query("SELECT COUNT(*) as cnt FROM PRODUCTS WHERE CATEGORY = '$cat'");
    $row = $q->fetch_assoc();
    $counts[$cat] = $row['cnt'];
}
$cat_images = [
    'Rackets' => '../images/RBG.jpg',       
    'Footwear' => '../images/FWBG.jpg',
    'Shuttlecocks' => '../images/SBG.jpg',   
    'Bags' => '../images/BBG.jpg',
    'Apparel' => '../images/ABG.jpg',
    'Accessories' => '../images/ACCBG.jpg'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cat-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 30px; 
            margin-top: 20px;
        }
        .cat-card { 
            position: relative; 
            height: 700px; 
            border-radius: 12px; 
            overflow: hidden; 
            text-decoration: none; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
            display: flex; 
            flex-direction: column; 
            justify-content: flex-end;
            padding: 25px; 
            border: none;
        }
        .cat-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 15px 30px rgba(0,0,0,0.2); 
        }
        .cat-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            z-index: 1;
            transition: transform 0.6s ease;
        }
    
        .cat-card:hover .cat-bg {
            transform: scale(1.1);
        }

        .cat-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%);
            z-index: 2;
        }
    
        .cat-content {
            position: relative;
            z-index: 3;
            color: white;
        }
        .cat-content h3 { 
            font-size: 26px; 
            font-weight: 900; 
            margin-bottom: 8px; 
            letter-spacing: 2px; 
            color: white;
        }
        .cat-content p { 
            background: #e60012; 
            color: white; 
            padding: 6px 14px; 
            border-radius: 20px; 
            font-size: 14px; 
            font-weight: bold; 
            display: inline-block; 
            margin: 0;
        }
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
                <div class="cat-bg" style="background-image: url('<?php echo $cat_images[$cat]; ?>');"></div>
                <div class="cat-overlay"></div>
                <div class="cat-content">
            <h3><?php echo strtoupper($cat); ?></h3>
            <p><?php echo $counts[$cat]; ?> Items</p>
        </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
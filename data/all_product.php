<?php
// 1. 引入你之前写好的统一数据库连接文件（保持代码干净）
include 'db_connect.php'; 

// 2. 获取所有产品！注意这里没有 WHERE 条件，直接拿全表
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Yonex Products</title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="badminton-hero"></div>

    <section class="all-product-container">
        <h2 class="section-title">All Products</h2>

        <div class="product-grid">
            
            <?php
            // 5. 让 PHP 开始干活，去数据库里循环拿数据
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
            ?>
            
            <div class="product-card">
                
                <img src="images/<?php echo basename($row['image_url']); ?>" alt="<?php echo $row['name']; ?>">
                
                <p class="p-series"><?php echo $row['series']; ?></p>
                <h3 class="p-name"><?php echo $row['name']; ?></h3>
                <span class="p-price">RM <?php echo $row['price']; ?></span>
                
                <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn-info">View Info</a>
                
            </div>

            <?php
                } // 结束循环
            } else {
                echo "<p>商店里还没有任何产品。</p>"; // 如果后台清空了，这里会显示
            }
            ?>

        </div>
    </section>

</body>
</html>
<?php
include 'db_connect.php';
$sql = "SELECT * FROM products WHERE category = 'Accessories'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yonex Accessories</title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="accessories-hero"></div>

    <section class="all-product-container">
        <h2 class="section-title">Accessories</h2>

        <div class="product-grid">
            
            <?php
            // 开始循环：如果有数据，就不断重复输出下面的 div
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
                } // 结束 while 循环
            } else {
                echo "<p>暂无相关产品。</p>"; 
            }
            ?>
            
        </div>
    </section>

</body>
</html>
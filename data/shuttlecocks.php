<?php
include 'db_connect.php'; // 引入数据库连接

// 1. 从数据库获取所有分类为 Shuttlecocks (羽毛球) 的产品
$sql = "SELECT * FROM products WHERE category = 'Shuttlecocks'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yonex Shuttlecocks</title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="shuttlecocks-hero"></div>

    <section class="all-product-container">
        <h2 class="section-title">Shuttlecocks</h2>

        <div class="product-grid">
            
            <?php
            // 4. 循环输出羽毛球产品
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
            ?>
            
            <div class="product-card">
                <img src="images/<?php echo basename($row['image_url']); ?>" alt="<?php echo $row['name']; ?>">
                <p class="p-series"><?php echo $row['series']; ?> SERIES</p>
                <h3 class="p-name"><?php echo $row['name']; ?></h3>
                <span class="p-price">RM <?php echo $row['price']; ?></span>
                
                <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn-info">View Info</a>
            </div>

            <?php
                }
            } else {
                echo "<p>暂无相关羽毛球产品。</p>";
            }
            ?>

        </div>
    </section>

</body>
</html>
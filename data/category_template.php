<?php
// 接收 index.php 传过来的名字，去数据库拿商品
$sql = "SELECT * FROM PRODUCTS WHERE CATEGORY = '$current_category_name'";
$result = $conn->query($sql);

// 顺便去 categories 表里，把这个分类的专属背景大图拿出来
$cat_bg_query = $conn->query("SELECT image_url FROM categories WHERE category_name = '$current_category_name'");
$cat_bg = ($cat_bg_query && $cat_bg_query->num_rows > 0) ? $cat_bg_query->fetch_assoc()['image_url'] : '';
?>

<style>
    .cat-hero {
        /* 自动读取后台上传的图片，加上一层半透明的黑色滤镜，让字更清楚 */
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('admin/<?php echo $cat_bg ? basename($cat_bg) : "default-bg.jpg"; ?>') center/cover;
        padding: 120px 60px 80px;
        color: white;
        text-align: center;
        border-bottom: 5px solid var(--red);
    }
    .cat-hero h1 { font-family: 'Cormorant Garamond', serif; font-size: 3.5rem; text-transform: uppercase; letter-spacing: 2px; }
    .cat-hero p { color: var(--gold); font-weight: bold; margin-top: 10px; letter-spacing: 1px; }
    
    .all-product-container { padding: 60px 10%; background: var(--offwhite); }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 40px; }
    
    .product-card { background: white; border-radius: 8px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.04); text-align: center; transition: 0.3s; position: relative; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
    
    .product-img-box { height: 220px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
    .product-img-box img { max-height: 100%; max-width: 100%; object-fit: contain; }
    
    .p-series { font-size: 0.75rem; color: var(--text-muted); font-weight: bold; letter-spacing: 1px; text-transform: uppercase; display: block; margin-bottom: 8px; }
    .p-name { margin: 0 0 15px; font-size: 1.2rem; color: var(--charcoal); }
    .p-price { color: var(--red); font-weight: 800; font-size: 1.1rem; display: block; margin-bottom: 20px; }
    
    .btn-info { display: block; width: 100%; padding: 12px; background: var(--charcoal); color: white; text-decoration: none; border-radius: 4px; font-size: 0.85rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
    .btn-info:hover { background: var(--red); }
</style>

<div class="cat-hero">
    <h1><?php echo htmlspecialchars($current_category_name); ?></h1>
    <p>YONEX EXCLUSIVE COLLECTION</p>
</div>

<section class="all-product-container">
    <div class="product-grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
        ?>
        <div class="product-card">
            <div class="product-img-box">
                <img src="admin/images/<?php echo basename($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            </div>
            <span class="p-series"><?php echo htmlspecialchars($row['series'] ?? 'YONEX'); ?> SERIES</span>
            <h3 class="p-name"><?php echo htmlspecialchars($row['name']); ?></h3>
            <span class="p-price">RM <?php echo number_format($row['price'], 2); ?></span>
            <a href="?id=<?php echo $row['id']; ?>" class="btn-info">View Details</a>
        </div>
        <?php
            }
        } else {
            // 如果该分类下没有商品，显示优雅的提示
            echo "<div style='grid-column: 1 / -1; text-align: center; padding: 60px 0;'>";
            echo "<h3 style='font-size: 24px; color: var(--charcoal); font-family: \"Cormorant Garamond\", serif;'>Coming Soon</h3>";
            echo "<p style='margin-top: 10px; color: var(--text-muted);'>Currently no products in ".htmlspecialchars($current_category_name).".</p>";
            echo "</div>";
        }
        ?>
    </div>
</section>
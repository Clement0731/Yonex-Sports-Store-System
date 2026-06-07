<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);

// 💡 提取参数：判断当前是否在产品管理相关的页面
$is_product_page = in_array($current_page, ['manage_product.php', 'add_product.php', 'edit_product.php']);
$current_cat_url = isset($_GET['category']) ? $_GET['category'] : '';
?>
<div class="sidebar" style="background: url('../images/SDBG.jpg') no-repeat left top !important; background-size: 100% 100% !important; padding: 40px 15px 20px 80px !important;">
    
    <h2 style="font-size: 18px; margin-bottom: 30px; border-bottom: 2px solid rgba(255,255,255,0.3); padding-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Admin Panel</h2>
    
    <nav>
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
        
        <a href="categories.php" class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">Categories</a>
        
        <div class="sidebar-dropdown">
            <a href="manage_product.php" class="dropdown-btn <?php echo $is_product_page ? 'active' : ''; ?>" onclick="toggleSubMenu(event)">
                All Products
                <span class="arrow" id="dropdown-arrow"><?php echo $is_product_page ? '▼' : '▶'; ?></span>
            </a>
            
            <div class="submenu-container" id="product-submenu" style="display: <?php echo $is_product_page ? 'block' : 'none'; ?>;">
                <a href="manage_product.php" class="sub-item <?php if($is_product_page && $current_cat_url == '') echo 'sub-active'; ?>">◆ View All</a>
                
                <?php
                // 动态去数据库拿分类名字，自动生成子菜单！
                if (isset($conn)) {
                    $sidebar_cats = $conn->query("SELECT category_name FROM categories ORDER BY id ASC");
                    if ($sidebar_cats && $sidebar_cats->num_rows > 0) {
                        while($scat = $sidebar_cats->fetch_assoc()) {
                            $s_name = $scat['category_name'];
                            // 智能判断当前属于哪个子分类，高亮显示
                            $sub_active = ($is_product_page && $current_cat_url == $s_name) ? 'sub-active' : '';
                            echo '<a href="manage_product.php?category='.urlencode($s_name).'" class="sub-item '.$sub_active.'">◆ '.htmlspecialchars($s_name).'</a>';
                        }
                    }
                }
                ?>
            </div>
        </div>
        <a href="admin_service.php" class="<?php echo ($current_page == 'admin_service.php') ? 'active' : ''; ?>">Services</a>

        <a href="admin_specs.php" class="<?php echo ($current_page == 'admin_specs.php') ? 'active' : ''; ?>">Specs</a>
        
        <a href="manage_customer.php" class="<?php echo ($current_page == 'manage_customer.php') ? 'active' : ''; ?>">Manage Customers</a>
        
        <a href="order.php" class="<?php echo ($current_page == 'order.php') ? 'active' : ''; ?>">Orders</a>
        
        <a href="admin_profile.php" class="<?php echo ($current_page == 'admin_profile.php') ? 'active' : ''; ?>" style="margin-top: 250px; color: #ffffff;">Profile</a>
        
        <a href="logout.php" style="color: #ffaaaa;">Sign Out</a>
    </nav>
</div>

<style>
.sidebar-dropdown { width: 100%; margin-bottom: 8px; }
.dropdown-btn { display: flex !important; justify-content: space-between; align-items: center; margin-bottom: 0 !important; }

/* 半透明黑色背景，完美融入图片背景 */
.submenu-container { background-color: rgba(0, 0, 0, 0.25); padding: 8px 0; border-radius: 0 0 6px 6px; margin-bottom: 8px; }

.sub-item { 
    padding: 8px 15px 8px 30px !important; 
    font-size: 13px !important; 
    color: #cccccc !important; 
    border-left: 3px solid transparent !important; 
    transition: 0.3s; 
    display: block;
    margin-bottom: 0 !important;
    border-radius: 0 !important;
    text-decoration: none;
}
.sub-item:hover { color: #ffffff !important; background-color: rgba(255,255,255,0.1); border-left: 3px solid rgba(255,255,255,0.5) !important; }

/* 激活状态：变成红色左边框并加深背景 */
.sub-active { 
    color: #ffffff !important; 
    font-weight: bold; 
    border-left: 3px solid #e60012 !important; 
    background-color: rgba(0,0,0,0.4); 
}
.arrow { font-size: 10px; transition: transform 0.3s; opacity: 0.7; }
</style>

<script>
function toggleSubMenu(e) {
    var submenu = document.getElementById('product-submenu');
    var arrow = document.getElementById('dropdown-arrow');
    
    if (submenu.style.display === 'none' || submenu.style.display === '') {
        submenu.style.display = 'block';
        arrow.innerHTML = '▼';
    } else {
        submenu.style.display = 'none';
        arrow.innerHTML = '▶';
    }
}
</script>
<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);

$is_product_page = in_array($current_page, ['manage_product.php', 'add_product.php', 'edit_product.php']);
$current_cat_url = isset($_GET['category']) ? $_GET['category'] : '';

// 💡 【新增核心逻辑】：去数据库查询当前登录管理员的 ROLE
$admin_role = 'Staff'; // 默认最低权限
if (isset($_SESSION['admin_id']) && isset($conn)) {
    $sess_id = $_SESSION['admin_id'];
    $role_query = $conn->query("SELECT ROLE FROM admin WHERE USER_ID = '$sess_id'");
    if ($role_query && $role_query->num_rows > 0) {
        $role_row = $role_query->fetch_assoc();
        $admin_role = $role_row['ROLE'];
    }
}
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
                if (isset($conn)) {
                    $sidebar_cats = $conn->query("SELECT category_name FROM categories ORDER BY id ASC");
                    if ($sidebar_cats && $sidebar_cats->num_rows > 0) {
                        while($scat = $sidebar_cats->fetch_assoc()) {
                            $s_name = $scat['category_name'];
                            $sub_active = ($is_product_page && $current_cat_url == $s_name) ? 'sub-active' : '';
                            echo '<a href="manage_product.php?category='.urlencode($s_name).'" class="sub-item '.$sub_active.'">◆ '.htmlspecialchars($s_name).'</a>';
                        }
                    }
                }
                ?>
            </div>
        </div>
        <a href="admin_service.php" class="<?php echo ($current_page == 'admin_service.php') ? 'active' : ''; ?>">Services</a>
        
        <a href="manage_customer.php" class="<?php echo ($current_page == 'manage_customer.php') ? 'active' : ''; ?>">Manage Customers</a>
        
        <a href="order.php" class="<?php echo ($current_page == 'order.php') ? 'active' : ''; ?>">Orders</a>

        <a href="report.php" class="<?php echo ($current_page == 'report.php') ? 'active' : ''; ?>">Reports Analytics</a>
        
        <?php if ($admin_role === 'Superadmin'): ?>
            <a href="manage_admin.php" class="<?php echo ($current_page == 'manage_admin.php') ? 'active' : ''; ?>" style="color: #fca5a5; border-left-color: #fca5a5;">★ Manage Admins</a>
        <?php endif; ?>

        <a href="admin_profile.php" class="<?php echo ($current_page == 'admin_profile.php') ? 'active' : ''; ?>" style="margin-top: 150px; color: #ffffff;">Profile</a>
        
        <a href="logout.php" style="color: #ffaaaa;">Sign Out</a>
    </nav>
</div>

<style>
.sidebar-dropdown { width: 100%; margin-bottom: 8px; }
.dropdown-btn { display: flex !important; justify-content: space-between; align-items: center; margin-bottom: 0 !important; }
.submenu-container { background-color: rgba(0, 0, 0, 0.25); padding: 8px 0; border-radius: 0 0 6px 6px; margin-bottom: 8px; }
.sub-item { padding: 8px 15px 8px 30px !important; font-size: 13px !important; color: #cccccc !important; border-left: 3px solid transparent !important; transition: 0.3s; display: block; margin-bottom: 0 !important; border-radius: 0 !important; text-decoration: none; }
.sub-item:hover { color: #ffffff !important; background-color: rgba(255,255,255,0.1); border-left: 3px solid rgba(255,255,255,0.5) !important; }
.sub-active { color: #ffffff !important; font-weight: bold; border-left: 3px solid #e60012 !important; background-color: rgba(0,0,0,0.4); }
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
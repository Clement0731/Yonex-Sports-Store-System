<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<div class="sidebar" style="background: url('../images/SDBG.jpg') no-repeat left top !important; background-size: 100% 100% !important; padding: 40px 15px 20px 80px !important;">
    
    <h2 style="font-size: 18px; margin-bottom: 30px; border-bottom: 2px solid rgba(255,255,255,0.3); padding-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Admin Panel</h2>
    
    <nav>
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
        
        <a href="categories.php" class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">Categories</a>
        
        <a href="manage_product.php" class="<?php echo (in_array($current_page, ['manage_product.php', 'add_product.php', 'edit_product.php'])) ? 'active' : ''; ?>">All Products</a>
        
        <a href="Service.php" class="<?php echo ($current_page == 'Service.php') ? 'active' : ''; ?>">Services</a>
        
        <a href="manage_customer.php" class="<?php echo ($current_page == 'manage_customer.php') ? 'active' : ''; ?>">Manage Customers</a>
        
        <a href="order.php" class="<?php echo ($current_page == 'order.php') ? 'active' : ''; ?>">Orders</a>
        
        <a href="admin_profile.php" class="<?php echo ($current_page == 'admin_profile.php') ? 'active' : ''; ?>" style="margin-top: 250px; color: #ffffff;">Profile</a>
        
        <a href="logout.php" style="color: #ffaaaa;">Sign Out</a>
    </nav>
</div>
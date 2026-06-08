<?php
session_start();
$conn = new mysqli("localhost", "root", "", "yonex_db");

$is_logged_in = false;
$username = "Guest (Not Logged In)";
$bio = "Log in to enjoy full member services.";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $is_logged_in = true;
        $username = !empty($user['username']) ? htmlspecialchars($user['username']) : "Yonex Member";
        $bio = !empty($user['bio']) ? htmlspecialchars($user['bio']) : "This user is lazy and left nothing here.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yonex Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --yonex-blue: #003366; --bg-color: #f4f7f9; }
        body { background-color: var(--bg-color); margin: 0; font-family: 'Inter', sans-serif; }
        .profile-wrapper { width: 100%; max-width: 480px; margin: 20px auto; padding-bottom: 30px; }
        .profile-card { background: #ffffff; margin: 0 12px 20px 12px; border-radius: 20px; padding: 24px; display: flex; align-items: center; gap: 16px; box-shadow: 0 8px 24px rgba(0, 51, 102, 0.08); }
        .avatar-main { width: 70px; height: 70px; background-color: var(--yonex-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .avatar-main i { color: #ffffff; font-size: 30px; }
        .user-info h2 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px 0; }
        .user-info p { font-size: 13px; color: #718096; margin: 0; }
        .menu-group { background: #ffffff; margin: 0 12px 16px 12px; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .menu-item { display: flex; justify-content: space-between; align-items: center; padding: 20px; text-decoration: none; color: #333; transition: background 0.2s; }
        .menu-item:hover { background: #f8fbff; }
        .menu-left { display: flex; align-items: center; gap: 15px; font-weight: 600; font-size: 15px; }
        .menu-left i { color: var(--yonex-blue); width: 22px; text-align: center; }
        .menu-right { color: #cbd5e0; }
        .logout-btn { color: #e53e3e !important; }
        .logout-btn i { color: #e53e3e !important; }
    </style>
</head>
<body>

<div class="profile-wrapper">
    <a href="<?php echo $is_logged_in ? 'edit_profile.php' : 'login_page.php'; ?>" style="text-decoration: none; display: block;">
        <div class="profile-card">
            <div class="avatar-main"><i class="fas fa-user"></i></div>
            <div class="user-info">
                <h2><?php echo $username; ?></h2>
                <p><?php echo $bio; ?></p>
            </div>
            <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
        </div>
    </a>

    <div class="menu-group">
        <a href="../index.php?category=home" class="menu-item">
            <div class="menu-left"><i class="fas fa-home"></i> <span>Back to Store Home</span></div>
            <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
        </a>
        <?php if ($is_logged_in): ?>
            <a href="../payment/order_history.php" class="menu-item">
                <div class="menu-left"><i class="fas fa-shopping-bag"></i> <span>My Orders</span></div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="../payment/shopping_cart.php" class="menu-item">
                <div class="menu-left"><i class="fas fa-shopping-cart"></i> <span>My Cart</span></div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
        <?php endif; ?>
    </div>

    <?php if ($is_logged_in): ?>
        <div class="menu-group">
            <a href="edit_profile.php" class="menu-item">
                <div class="menu-left"><i class="fas fa-user-cog"></i> <span>Edit Profile Information</span></div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="manage_addresses.php" class="menu-item">
                <div class="menu-left"><i class="fas fa-map-marker-alt"></i> <span>Manage Addresses</span></div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="change_password.php" class="menu-item">
                <div class="menu-left"><i class="fas fa-lock"></i> <span>Change Password</span></div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>
    <?php endif; ?>

    <div class="menu-group">
        <?php if ($is_logged_in): ?>
            <a href="logout.php" class="menu-item logout-btn">
                <div class="menu-left"><i class="fas fa-sign-out-alt"></i> <span>Log Out</span></div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
        <?php else: ?>
            <a href="login_page.php" class="menu-item">
                <div class="menu-left"><i class="fas fa-sign-in-alt"></i> <span>Go to Login Account</span></div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
        <?php endif; ?>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
<?php
// 1. Start Session and check if the user is logged in
session_start();

// 2. Connect to the database
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "yonex_db"; // 👈 Your database name

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Smart status detection (No automatic redirect, compatible with your index.php)
$is_logged_in = false;
$username = "Guest (Not Logged In)";
$bio = "Log in to enjoy full member services.";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // 3. Fetch current logged-in user's data
    $sql = "SELECT * FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $is_logged_in = true;
        // If username or bio is empty in the database, set default display text
        $username = !empty($user['username']) ? htmlspecialchars($user['username']) : "Yonex Member";
        $bio = !empty($user['bio']) ? htmlspecialchars($user['bio']) : "This user is lazy and left nothing here.";
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Uniform styles tailored for the outer container embedded in the main page */
    .profile-wrapper {
        width: 100%;
        max-width: 480px;
        margin: 40px auto; /* Centers it within the main area of index.php */
        padding-bottom: 30px;
    }

    /* Top main card area */
    .profile-card {
        background-color: #ffffff;
        margin: 12px;
        border-radius: 12px;
        padding: 24px 16px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8 rgba(0,0,0,0.04);
    }

    /* Avatar styles */
    .avatar-main {
        width: 70px;
        height: 70px;
        background-color: #002B7F; /* Yonex Blue */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .avatar-main i {
        color: #ffffff;
        font-size: 32px;
    }

    /* Username and Bio */
    .user-info {
        flex-grow: 1;
        overflow: hidden;
    }

    .user-info h2 {
        font-size: 20px;
        font-weight: 600;
        color: #111111;
        margin-bottom: 6px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .user-info p {
        font-size: 13px;
        color: #888888;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    /* Menu grouping */
    .menu-group {
        background-color: #ffffff;
        margin: 0 12px 12px 12px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    /* Menu list item */
    .menu-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        border-bottom: 1px solid #f5f5f5;
        text-decoration: none;
        color: #333333;
    }

    .menu-item:last-child {
        border-bottom: none;
    }

    .menu-item:active {
        background-color: #f9f9f9;
    }

    .menu-left {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 15px;
    }

    .menu-left i {
        color: #002B7F; /* Yonex Blue */
        font-size: 18px;
        width: 20px;
        text-align: center;
    }

    .menu-right {
        color: #cccccc;
        font-size: 13px;
    }

    .logout-btn {
        color: #ff4500 !important;
    }
    .logout-btn i {
        color: #ff4500 !important;
    }
    
    /* Login button specific highlight styling */
    .login-btn {
        color: #002B7F !important;
        font-weight: 600;
    }
    .login-btn i {
        color: #002B7F !important;
    }
</style>

<div class="profile-wrapper">

    <a href="<?php echo $is_logged_in ? 'edit_profile.php' : 'login_page.php'; ?>" style="text-decoration: none; display: block;">
        <div class="profile-card">
            <div class="avatar-main">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-info">
                <h2><?php echo $username; ?></h2>
                <p><?php echo $bio; ?></p>
            </div>
            <div class="menu-right">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
    </a>

    <div class="menu-group">
        <a href="../index.php?category=home" class="menu-item">
            <div class="menu-left">
                <i class="fas fa-home"></i>
                <span>Back to Store Home</span>
            </div>
            <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
        </a>
        
        <?php if ($is_logged_in): ?>
            <a href="../payment/order_history.php" class="menu-item">
                <div class="menu-left">
                    <i class="fas fa-shopping-bag"></i>
                    <span>My Orders</span>
                </div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="../payment/shopping_cart.php" class="menu-item">
                <div class="menu-left">
                    <i class="fas fa-shopping-cart"></i>
                    <span>My Cart</span>
                </div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
        <?php endif; ?>
    </div>

    <?php if ($is_logged_in): ?>
        <div class="menu-group">
            <a href="edit_profile.php" class="menu-item">
                <div class="menu-left">
                    <i class="fas fa-user-cog"></i>
                    <span>Edit Profile Information</span>
                </div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
            <a href="change_password.php" class="menu-item">
                <div class="menu-left">
                    <i class="fas fa-lock"></i>
                    <span>Change Password</span>
                </div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
        </div>
    <?php endif; ?>

    <div class="menu-group">
        <?php if ($is_logged_in): ?>
            <a href="logout.php" class="menu-item logout-btn">
                <div class="menu-left">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
        <?php else: ?>
            <a href="login_page.php" class="menu-item login-btn">
                <div class="menu-left">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Go to Login Account</span>
                </div>
                <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
            </a>
        <?php endif; ?>
    </div>

</div>

<?php
$conn->close();
?>
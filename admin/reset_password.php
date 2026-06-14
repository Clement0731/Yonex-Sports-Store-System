<?php
session_start();
include 'db.php';

$msg = "";
$email = isset($_GET['email']) ? $_GET['email'] : '';

if (isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $entered_otp = $_POST['otp'];
    $new_password = $_POST['new_password'];

    // 💡 1. 现代密码规则拦截器：至少 8 位，包含至少 1 个大写字母，至少 1 个数字
    $password_regex = "/^(?=.*[A-Z])(?=.*\d).{8,}$/";

    // 💡 2. 先去数据库检查 OTP 是否正确且没过期
    $check_otp = $conn->query("SELECT * FROM admin WHERE EMAIL = '$email' AND reset_otp = '$entered_otp' AND otp_expiry >= NOW()");

    if ($check_otp->num_rows == 0) {
        // 第一层拦截：验证码错了或者过期了
        $msg = "<div class='error-msg'>Invalid or Expired OTP! (验证码无效或已过期)</div>";
    } elseif (!preg_match($password_regex, $new_password)) {
        // 第二层拦截：虽然验证码对了，但是新密码太弱，拒绝换锁！
        $msg = "<div class='error-msg'>Password must be at least 8 characters long, contain at least 1 uppercase letter and 1 number! (密码强度不符合现代安全标准)</div>";
    } else {
        // 🎉 统统通过！安全更新密码，并把 OTP 清空
        $conn->query("UPDATE admin SET PASSWORD = '$new_password', reset_otp = NULL, otp_expiry = NULL WHERE EMAIL = '$email'");
        
        echo "<script>alert('Password reset successful! Please login with your new strong password.'); window.location.href='login.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Yonex Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-body { background:url('../images/LEEBG.jpg'); background-size: cover; background-position: center; background-attachment: fixed; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 400px; text-align: center; }
        .form-group { text-align: left; margin-bottom: 20px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .error-msg { color: #e60012; font-weight: bold; margin-bottom: 15px; font-size: 14px; text-align: left; background: #fdf2f2; padding: 10px; border-radius: 4px; border: 1px solid #fde8e8; }
    </style>
</head>
<body class="login-body">
<div class="login-box">
    <img src="../images/yonex_logo.png" alt="YONEX Logo" style="max-width: 320px; margin-bottom: 10px;">
    <p style="font-size: 20px; font-weight: bold; color: #000000;">Reset Your Password</p>
    <p style="font-size: 14px; margin-bottom: 20px;">We've sent a 6-digit OTP to <br><b style="color: #0033a0;"><?php echo htmlspecialchars($email); ?></b></p>
    
    <?php echo $msg; ?>
    
    <form action="" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        
        <div class="form-group">
            <label style="font-weight: bold; margin-bottom: 5px; display: block;">Enter 6-digit OTP</label>
            <input type="text" name="otp" required placeholder="e.g. 123456" maxlength="6">
        </div>
        <div class="form-group">
            <label style="font-weight: bold; margin-bottom: 5px; display: block;">Enter New Password</label>
            <input type="password" name="new_password" required placeholder="Min 8 chars, 1 Uppercase, 1 Number">
            <small style="color:#888; font-size: 11px; display:block; margin-top:5px;">* Must include at least 1 uppercase letter and 1 number.</small>
        </div>
        <button type="submit" name="reset_password" class="btn-login" style="width: 100%; padding: 12px; background: #e60012; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer;">Confirm & Reset Password</button>
    </form>
</div>
</body>
</html>
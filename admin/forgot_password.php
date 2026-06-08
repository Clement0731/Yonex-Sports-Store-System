<?php
session_start();
include 'db.php'; // 你的数据库连接

// 引入刚刚下载的 PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$msg = "";

if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];

    // 检查邮箱是否存在于管理员名单中
    $check_email = $conn->query("SELECT * FROM admin WHERE EMAIL = '$email' AND ROLE = 'Admin'");
    
    if ($check_email->num_rows > 0) {
        // 生成 6 位数随机验证码 (OTP)
        $otp = rand(100000, 999999);
        
        // 把 OTP 存进数据库，并设置 15 分钟后过期
        $conn->query("UPDATE USERS SET reset_otp = '$otp', otp_expiry = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE EMAIL = '$email'");

        // 🚀 开始配置发邮件
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // 👇 修改 1：换成你刚才新注册的专业 Gmail 地址
            $mail->Username   = 'yonexadmin@gmail.com'; 
            
            // 👇 修改 2：换成你刚才复制的 16 位字母密码（不要有空格）
            $mail->Password   = 'vztsnwjuxqfvnjod'; 
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // 👇 修改 3：也换成你的新邮箱
            $mail->setFrom('yonexadmin@gmail.com', 'Yonex Admin System');
            
            $mail->addAddress($email); // 发给请求重置密码的管理员

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP - Yonex Admin';
            $mail->Body    = "<h3>Your Password Reset OTP Code is: <b style='color:red;'>$otp</b></h3><p>This code will expire in 15 minutes.</p>";

            $mail->send();
            
            // 邮件发送成功，带上 email 参数跳去输入 OTP 的页面
            header("Location: reset_password.php?email=" . urlencode($email));
            exit();
        } catch (Exception $e) {
            $msg = "<div class='error-msg'>Email could not be sent. Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $msg = "<div class='error-msg'>No admin account found with that email address.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Yonex Admin</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .login-body { background:url('../images/LEEBG.jpg'); background-size: cover; background-position: center; background-attachment: fixed; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 400px; text-align: center; }
    </style>
</head>
<body class="login-body">
<div class="login-box">
    <img src="../images/yonex_logo.png" alt="YONEX Logo" style="max-width: 320px; margin-bottom: 10px;">
    <p style="font-size: 20px; font-weight: bold; color: #000000;">Forgot Password</p>
    <p style="font-size: 14px; margin-bottom: 20px;">Enter your email to receive an OTP.</p>
    
    <?php echo $msg; ?>
    
    <form action="" method="POST">
        <div class="form-group" style="text-align: left; margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px;">Admin Email Address</label>
            <input type="email" name="email" required placeholder="Enter your email" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <button type="submit" name="send_otp" class="btn-login" style="width: 100%; padding: 12px; background: #e60012; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; margin-bottom: 15px;">Send OTP via Email</button>
        <a href="login.php" style="color: #0033a0; text-decoration: none; font-size: 14px; font-weight: bold;">← Back to Login</a>
    </form>
</div>
</body>
</html>
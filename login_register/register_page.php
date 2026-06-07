<?php
require_once 'db.php'; 
session_start();

// 引入 PHPMailer 核心文件
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$pageTitle = "Join the Team - Yonex Badminton";
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 收集输入并清理
    $username = htmlspecialchars(trim($_POST['username']));
    $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone    = htmlspecialchars(trim($_POST['phone'])); 
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // 密码强度：大写、小写、数字、符号，至少8位
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    if ($password !== $confirm) {
        $message = "Passwords do not match!";
        $messageType = "error";
    } elseif (!preg_match($passwordPattern, $password)) {
        $message = "Password must be at least 8 characters and include uppercase, lowercase, numbers, and symbols.";
        $messageType = "error";
    } else {
        try {
            // 检查邮箱或电话是否已经存在
            $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
            $checkEmail->execute([$email, $phone]);

            if ($checkEmail->rowCount() > 0) {
                $message = "This email or phone number is already registered!";
                $messageType = "error";
            } else {
                // 生成 6 位随机数作为 OTP
                $otp_code = rand(100000, 999999);

                $mail = new PHPMailer(true);
                try {
                    // ====== SMTP 服务器配置 ======
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';                                 
                    $mail->SMTPAuth   = true;                                             
                    
                    // 🚀 打开这个可以看具体的发信错误日志。测试成功后，改成 0 即可关闭
                    $mail->SMTPDebug  = 2; 

                    // ⚠️ 记得去谷歌后台生成全新的 16位应用密码
                    $mail->Username   = 'teolijie4gmail@gmail.com';                           
                    $mail->Password   = 'ywxqvhuhuxtxtecv';                               
                    
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                   
                    $mail->Port       = 587;                                              

                    // 绕过本地 XAMPP 的 SSL 证书验证限制
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );

                    // 收件人信息
                    $mail->setFrom('teolijie4gmail@gmail.com', 'Yonex Badminton');            
                    $mail->addAddress($email, $username); 

                    // HTML 邮件内容设计
                    $mail->isHTML(true);
                    $mail->Subject = 'Verify your Yonex Account';
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;'>
                            <h2 style='color: #003366; text-align: center; letter-spacing: 2px;'>YONEX BADMINTON</h2>
                            <p>Hi <b>$username</b>,</p>
                            <p>Thank you for signing up with Yonex. Please use the following One-Time Password (OTP) to verify your account:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <span style='font-size: 32px; font-weight: bold; color: #003366; letter-spacing: 5px; background: #f7fafc; padding: 10px 30px; border-radius: 8px; border: 1px dashed #003366;'>$otp_code</span>
                            </div>
                            <p style='font-size: 12px; color: #718096; text-align: center;'>If you did not request this code, please secure your account.</p>
                        </div>
                    ";

                    // 执行发送
                    $mail->send();
                    
                    // 邮件发送成功后，才将用户数据写入数据库，默认为未验证（0）
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, is_verified) VALUES (?, ?, ?, ?, 0)");
                    $success = $stmt->execute([$username, $email, $phone, $hashedPassword]);

                    if ($success) {
                        // 数据存入 Session，供下一个页面比对
                        $_SESSION['pending_user_email'] = $email;
                        $_SESSION['generated_otp'] = (string)$otp_code; 

                        // 跳转到 OTP 验证页面（确保文件名完全对应）
                        header("Location: otpverify.php");
                        exit();
                    } else {
                        $message = "Registration failed during database saving.";
                        $messageType = "error";
                    }

                } catch (Exception $e) {
                    $message = "Registration failed! Failed to send OTP. Mailer Error: {$mail->ErrorInfo}";
                    $messageType = "error";
                }
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --yonex-blue: #003366; --accent-gray: #718096; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: radial-gradient(circle at center, #ffffff 0%, #e9eff5 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; position: relative; }
        .reg-card { background: #ffffff; padding: 50px 40px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0, 51, 102, 0.08); width: 100%; max-width: 420px; text-align: center; z-index: 1; border: 1px solid rgba(0, 51, 102, 0.05); margin: 20px; }
        .main-title { font-size: 34px; font-weight: 900; color: var(--yonex-blue); margin: 0; letter-spacing: 4px; text-transform: uppercase; }
        .sub-title { font-size: 13px; font-weight: 500; color: var(--accent-gray); margin: 8px 0 35px 0; letter-spacing: 1px; }
        .input-group { margin-bottom: 18px; text-align: left; position: relative; }
        .input-group input { width: 100%; padding: 15px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 15px; background-color: #f7fafc; outline: none; transition: all 0.2s ease; }
        .input-group .has-icon { padding-right: 45px; }
        .input-group input:focus { border-color: var(--yonex-blue); background-color: #fff; box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.05); }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--accent-gray); }
        .btn { width: 100%; padding: 16px; background: linear-gradient(135deg, #003366 0%, #002244 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 10px; transition: all 0.3s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2); }
        .alert { font-size: 13px; margin-bottom: 20px; padding: 12px; border-radius: 10px; text-align: center; }
        .error { background: #fff5f5; color: #e53e3e; border: 1px solid rgba(229, 62, 62, 0.1); }
        .footer-link { margin-top: 30px; padding-top: 20px; border-top: 1px solid #f1f5f9; font-size: 14px; color: var(--accent-gray); }
        .footer-link a { color: var(--yonex-blue); text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>

<div class="reg-card">
    <h1 class="main-title">YONEX</h1>
    <p class="sub-title">Create your member account</p>
    
    <?php if ($message): ?>
        <div class="alert <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group"><input type="text" name="username" placeholder="Full Name (e.g. Lin Dan)" required></div>
        <div class="input-group"><input type="email" name="email" placeholder="Email Address" required></div>
        <div class="input-group"><input type="tel" name="phone" placeholder="Phone Number (e.g. +60123456789)" required></div>
        <div class="input-group">
            <input type="password" name="password" id="password" class="has-icon" placeholder="Password (Upper, Lower, Num, Symbol)" required>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('password', this)"></i>
        </div>
        <div class="input-group">
            <input type="password" name="confirm_password" id="confirm_password" class="has-icon" placeholder="Confirm Password" required>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('confirm_password', this)"></i>
        </div>
        <button type="submit" class="btn">REGISTER NOW</button>
    </form>
    
    <div class="footer-link">Already a member? <a href="login_page.php">Login Here</a></div>
</div>

<script>
    function togglePass(inputId, icon) {
        const inputField = document.getElementById(inputId);
        if (inputField.type === "password") {
            inputField.type = "text";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            inputField.type = "password";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        }
    }
</script>
</body>
</html>
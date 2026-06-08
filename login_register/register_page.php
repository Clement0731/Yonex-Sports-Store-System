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

    // 密码强度正则：至少8字符，包含大小写字母、数字和特殊符号
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
                    // SMTP 配置
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';                                   
                    $mail->SMTPAuth   = true;                                           
                    $mail->SMTPDebug  = 0; 
                    $mail->Username   = 'teolijie4@gmail.com'; 
                    $mail->Password   = 'hodzkfiyllwycvxy';    
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                    
                    $mail->Port       = 465;                                            
                    $mail->CharSet    = 'UTF-8';

                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );

                    $mail->setFrom('teolijie4@gmail.com', 'Yonex Badminton');            
                    $mail->addAddress($email, $username); 

                    $mail->isHTML(true);
                    $mail->Subject = 'Verify your Yonex Account';
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;'>
                            <h2 style='color: #003366; text-align: center; letter-spacing: 2px;'>YONEX BADMINTON</h2>
                            <p>Hi <b>$username</b>,</p>
                            <p>Please use the following OTP to verify your account:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <span style='font-size: 32px; font-weight: bold; color: #003366; letter-spacing: 5px; background: #f7fafc; padding: 10px 30px; border-radius: 8px; border: 1px dashed #003366;'>$otp_code</span>
                            </div>
                        </div>
                    ";

                    $mail->send();
                    
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, is_verified) VALUES (?, ?, ?, ?, 0)");
                    $success = $stmt->execute([$username, $email, $phone, $hashedPassword]);

                    if ($success) {
                        $_SESSION['pending_user_email'] = $email;
                        $_SESSION['generated_otp'] = (string)$otp_code; 
                        $_SESSION['register_otp_time'] = time();
                        header("Location: otpverify.php");
                        exit();
                    }
                } catch (Exception $e) {
                    $message = "Registration failed! Mailer Error: {$mail->ErrorInfo}";
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
        body::before { content: ""; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: repeating-linear-gradient(45deg, transparent, transparent 100px, rgba(0, 51, 102, 0.015) 100px, rgba(0, 51, 102, 0.015) 200px); z-index: 0; }
        .reg-card { background: #ffffff; padding: 50px 40px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0, 51, 102, 0.08); width: 100%; max-width: 420px; text-align: center; position: relative; z-index: 1; border: 1px solid rgba(0, 51, 102, 0.05); margin: 20px; }
        .main-title { font-size: 34px; font-weight: 900; color: var(--yonex-blue); margin: 0; letter-spacing: 4px; text-transform: uppercase; }
        .sub-title { font-size: 13px; font-weight: 500; color: var(--accent-gray); margin: 8px 0 35px 0; letter-spacing: 1px; }
        .input-group { margin-bottom: 18px; text-align: left; position: relative; }
        .input-group input { width: 100%; padding: 15px; padding-right: 45px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 15px; background-color: #f7fafc; outline: none; transition: all 0.2s ease; }
        .input-group input:focus { border-color: var(--yonex-blue); background-color: #fff; box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.05); }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--accent-gray); transition: color 0.2s; }
        .toggle-password:hover { color: var(--yonex-blue); }
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
        <div class="input-group"><input type="text" name="username" placeholder="Full Name" required></div>
        <div class="input-group"><input type="email" name="email" placeholder="Email Address" required></div>
        <div class="input-group"><input type="tel" name="phone" placeholder="Phone Number" required></div>
        <div class="input-group">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('password', this)"></i>
        </div>
        <div class="input-group">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
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
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            inputField.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }
</script>
</body>
</html>
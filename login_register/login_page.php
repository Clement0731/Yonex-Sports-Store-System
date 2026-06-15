<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. 引入 PHPMailer 核心类文件（根据你的目录结构引入）
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'db.php'; 
session_start();
$pageTitle = "Yonex - Login Portal";
$error = "";
$success_msg = "";

// 2. 使用真实的 SMTP 服务发送 OTP 邮件
function sendOtpEmail($toEmail, $otp) {
    $mail = new PHPMailer(true);

    try {
        // --- SMTP 服务器配置 ---
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                        
        $mail->SMTPAuth   = true;                                   
        
        // 【配置区：请填入你的真实邮箱信息】
        $mail->Username   = 'teolijie4@gmail.com';             
        $mail->Password   = 'hodzkfiyllwycvxy';                  
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        $mail->Port       = 587;                                    

        // --- 收件人和发件人配置 ---
        $mail->setFrom('no-reply@yonex-portal.com', 'YONEX Portal'); 
        $mail->addAddress($toEmail);                                

        // --- 邮件内容配置 ---
        $mail->isHTML(true);                                        
        $mail->Subject = 'Your YONEX OTP Verification Code';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #edf2f7; border-radius: 10px; max-width: 500px;'>
                <h2 style='color: #003366; border-bottom: 2px solid #003366; padding-bottom: 10px;'>YONEX Verification Portal</h2>
                <p>Hello,</p>
                <p>You are requesting an Email OTP login. Your 6-digit verification code is:</p>
                <div style='background-color: #f7fafc; padding: 15px; text-align: center; border-radius: 8px; margin: 20px 0;'>
                    <span style='font-size: 24px; font-weight: bold; color: #003366; letter-spacing: 5px;'>{$otp}</span>
                </div>
                <p style='color: #718096; font-size: 12px;'>This code is valid for 5 minutes. If you did not request this, please ignore this email.</p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        global $error;
        $error = "Mail could not be sent. Error: {$mail->ErrorInfo}";
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action_type = isset($_POST['action_type']) ? $_POST['action_type'] : 'password_login';

    try {
        if ($action_type == 'send_otp') {
            $otp_email = trim($_POST['otp_email']);
            if (empty($otp_email)) {
                $error = "Please enter your email address first.";
            } else {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$otp_email]);
                $user = $stmt->fetch();

                if ($user) {
                    // 🔒 新增拦截：如果账号已被停用，拒绝发送 OTP
                    if (isset($user['status']) && $user['status'] === 'Deactivated') {
                        $error = "Your account has been deactivated by the administrator. Please contact support for assistance.";
                    } else {
                        $otp = rand(100000, 999999);
                        $_SESSION['login_otp'] = $otp;
                        $_SESSION['login_otp_email'] = $otp_email;
                        $_SESSION['login_otp_time'] = time();

                        if (sendOtpEmail($otp_email, $otp)) {
                            $success_msg = "OTP code has been successfully sent to your email!";
                        }
                    }
                } else {
                    $error = "Email address not registered.";
                }
            }
        }
        elseif ($action_type == 'password_login') {
            $login_input = trim($_POST['login_input']); 
            $password = $_POST['password'];

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$login_input, $login_input]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // 🔒 新增拦截：验证密码正确后，检查账号状态
                if (isset($user['status']) && $user['status'] === 'Deactivated') {
                    $error = "Your account has been deactivated by the administrator. Please contact support for assistance.";
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: ../index.php"); 
                    exit;
                }
            } else {
                $error = "Invalid identity or password. Please try again.";
            }
        }
        elseif ($action_type == 'otp_login') {
            $otp_email = trim($_POST['otp_email']);
            $otp_code = trim($_POST['otp_code']);

            if (isset($_SESSION['login_otp']) && $_SESSION['login_otp'] == $otp_code && $_SESSION['login_otp_email'] == $otp_email) {
                if ((time() - $_SESSION['login_otp_time']) < 300) {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                    $stmt->execute([$otp_email]);
                    $user = $stmt->fetch();

                    // 🔒 新增拦截：OTP验证通过后，检查账号状态
                    if ($user) {
                        if (isset($user['status']) && $user['status'] === 'Deactivated') {
                            $error = "Your account has been deactivated by the administrator. Please contact support for assistance.";
                        } else {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];

                            unset($_SESSION['login_otp'], $_SESSION['login_otp_email'], $_SESSION['login_otp_time']);

                            header("Location: ../index.php"); 
                            exit;
                        }
                    } else {
                        $error = "User not found.";
                    }
                } else {
                    $error = "OTP has expired. Please send a new one.";
                }
            } else {
                $error = "Invalid OTP verification code.";
            }
        }
    } catch (PDOException $e) {
        $error = "System error: " . $e->getMessage();
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
        :root {
            --yonex-blue: #003366;
            --accent-gray: #718096;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            font-family: 'Inter', -apple-system, sans-serif;
            
            /* 🎯 重点 1：下面这行就是控制背景图的代码，如果以后还要换图，把 'login image.jpg' 改掉就行 */
            background: url('../images/login image.jpg') no-repeat center center;
            background-size: cover;
            
            display: flex; justify-content: center; align-items: center;
            height: 100vh; overflow: hidden; position: relative;
        }
        body::before {
            content: ""; position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.45);
            z-index: 0;
        }
        .login-card {
            background: #ffffff; padding: 45px 40px; border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            width: 100%; max-width: 450px; text-align: center; position: relative; z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .brand-logo { height: 45px; margin-bottom: 5px; object-fit: contain; }
        
        .sub-title { font-size: 13px; font-weight: 500; color: var(--accent-gray); margin: 8px 0 25px 0; letter-spacing: 1px; }
        .login-tabs { display: flex; justify-content: center; margin-bottom: 25px; border-bottom: 2px solid #edf2f7; }
        .tab-btn { background: none; border: none; padding: 10px 15px; font-size: 14px; font-weight: 600; color: var(--accent-gray); cursor: pointer; transition: all 0.2s; }
        .tab-btn.active { color: var(--yonex-blue); border-bottom: 2px solid var(--yonex-blue); margin-bottom: -2px; }
        .input-group { margin-bottom: 18px; text-align: left; position: relative; }
        .input-group input { width: 100%; padding: 15px; padding-right: 45px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 15px; background-color: #f7fafc; outline: none; transition: all 0.2s ease; }
        .input-group input:focus { border-color: var(--yonex-blue); background-color: #fff; box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.05); }
        .otp-group { display: flex; gap: 10px; align-items: center; }
        .otp-group input { flex: 1; min-width: 0; padding: 15px 10px; font-size: 13.5px; }
        .btn-send-otp { background: #edf2f7; border: 1.5px solid #edf2f7; color: var(--yonex-blue); border-radius: 12px; padding: 0 15px; height: 50px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; transition: all 0.2s; }
        .btn-send-otp:hover { background: #e2e8f0; }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--accent-gray); transition: color 0.2s; }
        .toggle-password:hover { color: var(--yonex-blue); }
        .btn { width: 100%; padding: 16px; background: linear-gradient(135deg, #003366 0%, #002244 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 10px; transition: all 0.3s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2); }
        
        .error-msg { color: #e53e3e; font-size: 13px; margin-bottom: 20px; padding: 8px; background: #fff5f5; border-radius: 8px; text-align: left; word-break: break-all; }
        .success-msg { color: #38a169; font-size: 13px; margin-bottom: 15px; padding: 8px; background: #f0fff4; border-radius: 8px; }

        .forgot-link-container { text-align: right; margin-top: -10px; margin-bottom: 15px; }
        .forgot-link-container a { font-size: 13px; color: var(--accent-gray); text-decoration: none; }
        .forgot-link-container a:hover { color: var(--yonex-blue); text-decoration: underline; }
        .register-footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #f1f5f9; font-size: 14px; color: var(--accent-gray); }
        .register-footer a { color: var(--yonex-blue); text-decoration: none; font-weight: 700; }
        .register-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-card">
    
    <img src="../yonex-logo.png" alt="YONEX" class="brand-logo" onerror="this.src='yonex_logo.png'; this.onerror=null; this.alt='YONEX';">
    
    <p class="sub-title">Sign in to your account</p>

    <div class="login-tabs">
        <button type="button" class="tab-btn active" id="tab-pwd" onclick="switchLoginMode('password')">Password</button>
        <button type="button" class="tab-btn" id="tab-otp" onclick="switchLoginMode('otp')">Email OTP</button>
    </div>
    
    <?php if ($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success_msg): ?>
        <div class="success-msg"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <form id="form-password" method="POST" action="" autocomplete="off">
        <input type="hidden" name="action_type" value="password_login">
        <div class="input-group">
            <input type="text" name="login_input" placeholder="Username or Email" required autocomplete="off" value="">
        </div>
        <div class="input-group">
            <input type="password" name="password" id="password" placeholder="Password" required autocomplete="new-password" value="">
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('password', this)"></i>
        </div>
        <div class="forgot-link-container">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
        <button type="submit" class="btn">LOGIN</button>
    </form>

    <form id="form-otp" method="POST" action="" style="display: none;" autocomplete="off">
        <input type="hidden" name="action_type" id="otp-action-field" value="otp_login">
        
        <div class="input-group otp-group">
            <input type="email" name="otp_email" id="otp_email" placeholder="Your Registered Email" autocomplete="off" value="">
            <button type="button" class="btn-send-otp" onclick="triggerSendOtp()">Send OTP</button>
        </div>
        
        <div class="input-group">
            <input type="text" name="otp_code" placeholder="6-digit OTP Code" autocomplete="off" value="">
        </div>
        
        <button type="submit" class="btn">LOGIN WITH OTP</button>
    </form>
    
    <div class="register-footer">
        Don't have an account? <a href="register_page.php">Register Now</a>
    </div>
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

    function switchLoginMode(mode) {
        const pwdForm = document.getElementById('form-password');
        const otpForm = document.getElementById('form-otp');
        const tabPwd = document.getElementById('tab-pwd');
        const tabOtp = document.getElementById('tab-otp');

        if (mode === 'password') {
            pwdForm.style.display = 'block';
            otpForm.style.display = 'none';
            tabPwd.classList.add('active');
            tabOtp.classList.remove('active');
            document.getElementById('otp_email').removeAttribute('required');
        } else {
            pwdForm.style.display = 'none';
            otpForm.style.display = 'block';
            tabPwd.classList.remove('active');
            tabOtp.classList.add('active');
            document.getElementById('otp_email').setAttribute('required', 'true');
        }
    }

    function triggerSendOtp() {
        const emailInput = document.getElementById('otp_email');
        if(!emailInput.value) {
            alert('Please enter your email first.');
            return;
        }
        document.getElementById('otp-action-field').value = 'send_otp';
        document.getElementById('form-otp').submit();
    }

    <?php if (isset($_POST['action_type']) && ($_POST['action_type'] == 'send_otp' || $_POST['action_type'] == 'otp_login')): ?>
        switchLoginMode('otp');
    <?php endif; ?>
</script>

</body>
</html>
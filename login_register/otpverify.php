<?php
require_once 'db.php'; 
session_start();

// 安全防火墙：防止没有注册数据直接访问该页面
if (!isset($_SESSION['pending_user_email']) || !isset($_SESSION['generated_otp'])) {
    header("Location: register_page.php");
    exit();
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input_otp = trim($_POST['otp_code']);

    // 强类型严格全等比对验证码
    if ($user_input_otp === $_SESSION['generated_otp']) {
        try {
            // 验证成功：将 users 表中的激活状态修改为已激活 (1)
            $updateStmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
            $updateStmt->execute([$_SESSION['pending_user_email']]);

            $message = "Verification successful! Your account is now activated. Redirecting to login...";
            $messageType = "success";
            
            // 清除验证码缓存，保留 Email 供本次页面最后渲染
            unset($_SESSION['generated_otp']);
            
            // 2秒后重定向至你的登录页面
            header("refresh:2;url=login_page.php");
        } catch (PDOException $e) {
            $message = "Database error during verification: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Invalid OTP code! Please try again.";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Yonex Badminton</title>
    <style>
        :root { --yonex-blue: #003366; --accent-gray: #718096; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: radial-gradient(circle at center, #ffffff 0%, #e9eff5 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .otp-card { background: #ffffff; padding: 50px 40px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0, 51, 102, 0.08); width: 100%; max-width: 420px; text-align: center; border: 1px solid rgba(0, 51, 102, 0.05); }
        .main-title { font-size: 34px; font-weight: 900; color: var(--yonex-blue); margin: 0; letter-spacing: 4px; text-transform: uppercase; }
        .sub-title { font-size: 13px; font-weight: 500; color: var(--accent-gray); margin: 8px 0 25px 0; letter-spacing: 1px; }
        .instruction-text { font-size: 14px; color: #4a5568; margin-bottom: 25px; line-height: 1.5; }
        .input-group { margin-bottom: 20px; }
        .input-group input { width: 100%; padding: 15px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 24px; font-weight: 700; letter-spacing: 6px; text-align: center; background-color: #f7fafc; outline: none; transition: all 0.2s ease; }
        .input-group input:focus { border-color: var(--yonex-blue); background-color: #fff; box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.05); }
        .btn { width: 100%; padding: 16px; background: linear-gradient(135deg, #003366 0%, #002244 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2); }
        .alert { font-size: 13px; margin-bottom: 20px; padding: 12px; border-radius: 10px; text-align: center; }
        .error { background: #fff5f5; color: #e53e3e; border: 1px solid rgba(229, 62, 62, 0.1); }
        .success { background: #f0fff4; color: #38a169; border: 1px solid rgba(56, 161, 105, 0.1); }
        .footer-notice { font-size: 11px; color: #cbd5e1; margin-top: 25px; }
    </style>
</head>
<body>

<div class="otp-card">
    <h1 class="main-title">VERIFY</h1>
    <p class="sub-title">Enter Security Code</p>
    
    <?php if ($message): ?>
        <div class="alert <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <p class="instruction-text">
        An OTP verification code has been sent to your email:<br>
        <b><?php echo isset($_SESSION['pending_user_email']) ? htmlspecialchars($_SESSION['pending_user_email']) : ''; ?></b>. Please check your inbox.
    </p>

    <form method="POST" action="">
        <div class="input-group">
            <input type="text" name="otp_code" maxlength="6" pattern="\d{6}" placeholder="000000" autocomplete="off" required>
        </div>
        <button type="submit" class="btn">VERIFY CODE</button>
    </form>

    <div class="footer-notice">Secured by Yonex Badminton Authentication</div>
</div>

<script>
    // 💡 团队开发福利：如果邮件在本地测试有延迟，可以直接按 F12 打开开发者工具看 Console，会有当前正确的验证码
    console.log("[FYP Debug Mode] Current OTP Code is: <?php echo isset($_SESSION['generated_otp']) ? $_SESSION['generated_otp'] : 'Expired'; ?>");
</script>

</body>
</html>
<?php
require_once 'db.php';
session_start();
$error = "";
$success = "";
$show_form = false;

// 1. 检查有没有带 token 参数进来
if (isset($_GET['token'])) {
    $url_token = $_GET['token'];

    // 验证链接中的 token 是否和 Session 存的一致，以及是否超时（5分钟 = 300秒）
    if (!isset($_SESSION['reset_token']) || $url_token !== $_SESSION['reset_token']) {
        $error = "Invalid or expired reset link. Please request a new one.";
    } elseif ((time() - $_SESSION['reset_token_time']) > 300) {
        $error = "This reset link has expired (5 minutes timeout).";
    } else {
        // Token 完美通过校验，允许展示设置新密码表单
        $show_form = true;
        $target_email = $_SESSION['reset_email'];
    }
}

// 2. 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['reset_token'])) {
    $new_pwd = $_POST['new_password'];
    $confirm_pwd = $_POST['confirm_password'];
    $target_email = $_SESSION['reset_email'];

    if ($new_pwd !== $confirm_pwd) {
        $error = "The two passwords do not match.";
        $show_form = true; // 保持表单可见
    } else {
        try {
            // 加密新密码
            $hashed_password = password_hash($new_pwd, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashed_password, $target_email]);

            // 清理无用的重置 Session
            unset($_SESSION['reset_token'], $_SESSION['reset_email'], $_SESSION['reset_token_time']);

            $success = "Password changed successfully! Redirecting to login portal...";
            header("refresh:3;url=login_page.php");
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
            $show_form = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yonex - Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --yonex-blue: #003366; --accent-gray: #718096; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: radial-gradient(circle at center, #ffffff 0%, #e9eff5 100%); display: flex; justify-content: center; align-items: center; height: 100vh; overflow: hidden; position: relative; }
        body::before { content: ""; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: repeating-linear-gradient(45deg, transparent, transparent 100px, rgba(0, 51, 102, 0.015) 100px, rgba(0, 51, 102, 0.015) 200px); z-index: 0; }
        .login-card { background: #ffffff; padding: 40px 40px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0, 51, 102, 0.08); width: 100%; max-width: 400px; text-align: center; position: relative; z-index: 1; border: 1px solid rgba(0, 51, 102, 0.05); }
        .main-title { font-size: 24px; font-weight: 800; color: var(--yonex-blue); margin: 0 0 10px 0; letter-spacing: 2px; text-transform: uppercase; }
        .sub-title { font-size: 13px; font-weight: 500; color: var(--accent-gray); margin: 0 0 25px 0; }
        .input-group { margin-bottom: 18px; text-align: left; position: relative; }
        .input-group input { width: 100%; padding: 15px; padding-right: 45px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 15px; background-color: #f7fafc; outline: none; transition: all 0.2s ease; }
        .input-group input:focus { border-color: var(--yonex-blue); background-color: #fff; box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.05); }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--accent-gray); }
        .btn { width: 100%; padding: 16px; background: linear-gradient(135deg, #003366 0%, #002244 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 10px; transition: all 0.3s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2); }
        .error-msg { color: #e53e3e; font-size: 13px; margin-bottom: 20px; padding: 8px; background: #fff5f5; border-radius: 8px; }
        .success-msg { color: #38a169; font-size: 13px; margin-bottom: 20px; padding: 8px; background: #f0fff4; border-radius: 8px; }
        .test-link-box { background: #fffaf0; border: 1px solid #fbd38d; color: #c05621; padding: 12px; border-radius: 10px; font-size: 13px; text-align: left; margin-bottom: 20px; line-height: 1.4; }
        .test-link-box a { color: #dd6b20; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-card">
    <h2 class="main-title">Reset Password</h2>

    <?php if (isset($_GET['preview_link']) && !isset($_GET['token'])): ?>
        <p class="sub-title" style="color: #4a5568;">Link has been successfully dispatched.</p>
        <div class="test-link-box">
            📌 <b>[本地开发测试提示]</b><br>
            由于本地环境无法发出真实邮件，请点击下方系统为你生成的模拟邮件重置链接：<br><br>
            👉 <a href="<?php echo htmlspecialchars($_GET['preview_link']); ?>">Click here to Reset Password</a>
        </div>
    <?php endif; ?>

    <?php if ($error): ?> <div class="error-msg"><?php echo $error; ?></div> <?php endif; ?>
    <?php if ($success): ?> <div class="success-msg"><?php echo $success; ?></div> <?php endif; ?>

    <?php if ($show_form && !$success): ?>
        <p class="sub-title">Set a new password for: <b style="color:var(--yonex-blue);"><?php echo htmlspecialchars($target_email); ?></b></p>
        <form method="POST" action="">
            <div class="input-group">
                <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
                <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('new_password', this)"></i>
            </div>
            <div class="input-group">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
                <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('confirm_password', this)"></i>
            </div>
            <button type="submit" class="btn">UPDATE PASSWORD</button>
        </form>
    <?php endif; ?>
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
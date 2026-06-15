<?php
require_once 'db.php';
session_start();
$error = "";
$success = "";
$show_form = false;

// 1. Check if token exists in URL
if (isset($_GET['token'])) {
    $url_token = $_GET['token'];

    // Verify token expiration (5 minutes = 300 seconds)
    if (!isset($_SESSION['reset_token']) || $url_token !== $_SESSION['reset_token']) {
        $error = "Invalid or expired reset link. Please request a new one.";
    } elseif ((time() - $_SESSION['reset_token_time']) > 300) {
        $error = "This reset link has expired (5 minutes timeout).";
    } else {
        // Token passed verification, allow showing form
        $show_form = true;
        $target_email = $_SESSION['reset_email'];
    }
}

// 2. Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['reset_token'])) {
    $new_pwd = $_POST['new_password'];
    $confirm_pwd = $_POST['confirm_password'];
    $target_email = $_SESSION['reset_email'];

    // Password regex: min 8 chars, 1 uppercase, 1 number
    $password_regex = "/^(?=.*[A-Z])(?=.*\d).{8,}$/";

    if ($new_pwd !== $confirm_pwd) {
        $error = "The two passwords do not match.";
        $show_form = true; 
    } elseif (!preg_match($password_regex, $new_pwd)) {
        $error = "Password must be at least 8 characters long, contain at least 1 uppercase letter and 1 number.";
        $show_form = true;
    } else {
        $hashed_password = password_hash($new_pwd, PASSWORD_DEFAULT);
        
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        
        if ($update->execute([$hashed_password, $target_email])) {
            $success = "Password has been reset successfully! Redirecting to login page...";
            // Clear session data
            unset($_SESSION['reset_token']);
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_token_time']);
            $show_form = false;
        } else {
            $error = "Failed to update password. Please try again.";
            $show_form = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | YONEX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --yonex-blue: #003366; --yonex-green: #00A650; --accent-gray: #718096; }
        * { box-sizing: border-box; }
        
        body { 
            margin: 0; padding: 0; 
            font-family: 'Inter', sans-serif; 
            
            /* 🚀 统一的背景图 */
            background: url('../images/IqhNWC.webp') no-repeat center center; 
            background-size: cover;
            
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; position: relative; overflow: hidden;
        }
        
        /* 🚀 统一的黑色半透明遮罩 */
        body::before { 
            content: ""; position: absolute; 
            top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0, 0, 0, 0.45); 
            z-index: 0; 
        }
        
        /* 🚀 统一的高级卡片圆角和阴影 */
        .reset-card { 
            background: #ffffff; padding: 50px 40px; border-radius: 20px; 
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2); 
            width: 100%; max-width: 420px; text-align: center; position: relative; z-index: 1; 
            border: 1px solid rgba(255, 255, 255, 0.1); 
        }
        
        .brand-logo { height: 45px; margin-bottom: 20px; object-fit: contain; }
        
        .main-title { color: var(--yonex-blue); font-size: 24px; margin: 0 0 10px 0; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; }
        .sub-title { color: var(--accent-gray); font-size: 13px; margin: 0 0 25px 0; font-weight: 500; line-height: 1.5; }
        
        .input-group { position: relative; margin-bottom: 20px; text-align: left; }
        .input-group input { width: 100%; padding: 15px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 15px; background-color: #f7fafc; outline: none; transition: all 0.2s ease; }
        .input-group input:focus { border-color: var(--yonex-blue); background-color: #fff; box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.05); }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--accent-gray); cursor: pointer; transition: color 0.2s; }
        .toggle-password:hover { color: var(--yonex-blue); }
        
        .btn { width: 100%; padding: 16px; background: linear-gradient(135deg, #003366 0%, #002244 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: block; box-sizing: border-box; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2); }
        
        .error-msg { color: #e53e3e; font-size: 13px; margin-bottom: 20px; padding: 8px; background: #fff5f5; border-radius: 8px; }
        .success-msg { color: #38a169; font-size: 14px; margin-bottom: 15px; font-weight: bold; padding: 12px; background: #f0fff4; border-radius: 8px; border: 1px solid rgba(56, 161, 105, 0.1); }
        
        .back-link { display: block; margin-top: 25px; color: var(--yonex-blue); text-decoration: none; font-size: 14px; font-weight: 700; }
        .back-link:hover { text-decoration: underline; }
        
        /* Disable browser native password reveal eyes */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>
</head>
<body>

<div class="reset-card">
    <img src="../yonex-logo.png" alt="YONEX" class="brand-logo" onerror="this.src='yonex_logo.png'; this.onerror=null; this.alt='YONEX';">
    
    <h2 class="main-title">RESET PASSWORD</h2>
    
    <?php if ($error): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?>
        <div class="success-msg"><?php echo $success; ?></div>
        <p style="font-size: 13px; color: #666; margin-bottom: 20px;">Redirecting in <span id="countdown" style="font-weight:bold; color:var(--yonex-blue);">3</span> seconds...</p>
        <a href="login_page.php" class="btn">GO TO LOGIN NOW</a>
        
        <script>
            let seconds = 3;
            const countdownEl = document.getElementById('countdown');
            const interval = setInterval(() => {
                seconds--;
                if(countdownEl) countdownEl.innerText = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = 'login_page.php';
                }
            }, 1000);
        </script>
    <?php endif; ?>

    <?php if ($show_form && !$success): ?>
        <p class="sub-title">Set a new password for:<br><b style="color:var(--yonex-blue);"><?php echo htmlspecialchars($target_email); ?></b></p>
        <form method="POST" action="">
            <div class="input-group">
                <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
                <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('new_password', this)"></i>
            </div>
            <div class="input-group">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
                <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('confirm_password', this)"></i>
            </div>
            <p style="text-align:left; font-size:11px; color:#888; margin-top:-10px; margin-bottom:20px;">* Min 8 chars, 1 Uppercase, 1 Number.</p>
            <button type="submit" class="btn">UPDATE PASSWORD</button>
        </form>
    <?php endif; ?>
    
    <?php if (!$show_form && !$success): ?>
        <a href="forgot_password.php" class="back-link">Request a new reset link</a>
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
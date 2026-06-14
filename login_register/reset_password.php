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
        :root { --yonex-blue: #003366; --yonex-green: #00A650; }
        body { font-family: 'Roboto', sans-serif; background: #f4f6f8; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .reset-card { background: white; width: 100%; max-width: 400px; padding: 40px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center; border-top: 4px solid var(--yonex-blue); }
        .reset-card img { max-width: 200px; margin-bottom: 20px; }
        .main-title { color: var(--yonex-blue); font-size: 24px; margin-bottom: 10px; font-weight: 900; letter-spacing: 1px; }
        .sub-title { color: #666; font-size: 14px; margin-bottom: 25px; }
        
        .input-group { position: relative; margin-bottom: 20px; text-align: left; }
        .input-group input { width: 100%; padding: 14px 40px 14px 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        .input-group input:focus { border-color: var(--yonex-blue); outline: none; }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #888; cursor: pointer; }
        
        .btn { width: 100%; padding: 14px; background: var(--yonex-blue); color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; text-decoration: none; display: block; box-sizing: border-box; }
        .btn:hover { background: #002244; }
        .error-msg { color: #e60012; font-size: 13px; margin-bottom: 15px; font-weight: bold; }
        .success-msg { color: var(--yonex-green); font-size: 14px; margin-bottom: 15px; font-weight: bold; padding: 10px; background: #e8f5e9; border-radius: 4px; }
        .back-link { display: block; margin-top: 20px; color: #666; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: var(--yonex-blue); text-decoration: underline; }
        
        /* Disable browser native password reveal eyes to resolve double eyes conflict */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>
</head>
<body>

<div class="reset-card">
    <img src="../images/yonex_logo.png" alt="YONEX">
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
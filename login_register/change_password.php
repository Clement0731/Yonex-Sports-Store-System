<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit;
}

$error = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if ($user && password_verify($old_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$hashed_password, $_SESSION['user_id']]);
            $success_msg = "Password updated successfully!";
        } else {
            $error = "Incorrect current password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Yonex - Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --yonex-blue: #003366; --accent-gray: #718096; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f7f9; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-card { background: #ffffff; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 340px; text-align: center; }
        .main-title { font-size: 20px; font-weight: 800; color: var(--yonex-blue); margin-bottom: 20px; }
        .input-group { margin-bottom: 15px; position: relative; }
        /* 强制宽度，确保输入框不拉伸 */
        .input-group input { width: 100%; box-sizing: border-box; padding: 12px 15px; padding-right: 40px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; background: #f9f9f9; outline: none; }
        .btn { width: 100%; padding: 12px; background: var(--yonex-blue); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; margin-top: 5px; }
        .error-msg { color: #e53e3e; font-size: 12px; margin-bottom: 10px; }
        .success-msg { color: #38a169; font-size: 12px; margin-bottom: 10px; }
        .back-link { margin-top: 15px; font-size: 12px; color: var(--accent-gray); text-decoration: none; display: block; }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #a0aec0; }
    </style>
</head>
<body>
<div class="login-card">
    <h2 class="main-title">CHANGE PASSWORD</h2>
    <?php if ($error): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success_msg): ?><div class="success-msg"><?php echo $success_msg; ?></div><?php endif; ?>
    <form method="POST" action="">
        <div class="input-group">
            <input type="password" name="old_password" id="old_password" placeholder="Current Password" required>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('old_password', this)"></i>
        </div>
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
    <a href="user_profile.php" class="back-link">← Back to Profile</a>
</div>
<script>
    function togglePass(id, icon) {
        const input = document.getElementById(id);
        input.type = (input.type === "password") ? "text" : "password";
        icon.className = input.type === "password" ? "fa-solid fa-eye-slash toggle-password" : "fa-solid fa-eye toggle-password";
    }
</script>
</body>
</html>
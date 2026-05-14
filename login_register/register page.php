<?php
require_once 'db.php'; // 1. 引入数据库连接文件
session_start();
$pageTitle = "Join the Team - Yonex Badminton";
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 收集并清理输入
    $username = htmlspecialchars(trim($_POST['username']));
    $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // --- 密码强度验证正则表达式 ---
    // 要求：至少8位，包含大写字母、小写字母、数字和特殊符号
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

    // 验证逻辑
    if ($password !== $confirm) {
        $message = "Passwords do not match!";
        $messageType = "error";
    } elseif (!preg_match($passwordPattern, $password)) {
        $message = "Password must be at least 8 characters and include uppercase, lowercase, numbers, and symbols.";
        $messageType = "error";
    } else {
        try {
            // 2. 检查邮箱是否已经存在
            $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkEmail->execute([$email]);

            if ($checkEmail->rowCount() > 0) {
                $message = "This email is already registered!";
                $messageType = "error";
            } else {
                // 3. 加密密码
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // 4. 执行插入数据库操作
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $success = $stmt->execute([$username, $email, $hashedPassword]);

                if ($success) {
                    $message = "Registration successful! You can now login.";
                    $messageType = "success";
                } else {
                    $message = "Something went wrong. Please try again.";
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
    <!-- 引入 Font Awesome 用于显示小眼睛图标 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --yonex-blue: #003366;
            --accent-gray: #718096;
        }

        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, sans-serif;
            background: radial-gradient(circle at center, #ffffff 0%, #e9eff5 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: -50%; left: -50%; width: 200%; height: 200%;
            background: repeating-linear-gradient(45deg, transparent, transparent 100px, rgba(0, 51, 102, 0.015) 100px, rgba(0, 51, 102, 0.015) 200px);
            z-index: 0;
        }

        .reg-card {
            background: #ffffff;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 51, 102, 0.08);
            width: 100%;
            max-width: 420px;
            text-align: center;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(0, 51, 102, 0.05);
            margin: 20px;
        }

        .main-title { font-size: 34px; font-weight: 900; color: var(--yonex-blue); margin: 0; letter-spacing: 4px; text-transform: uppercase; }
        .sub-title { font-size: 13px; font-weight: 500; color: var(--accent-gray); margin: 8px 0 35px 0; letter-spacing: 1px; }

        .input-group {
            margin-bottom: 18px;
            text-align: left;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 15px;
            padding-right: 45px;
            border: 1.5px solid #edf2f7;
            border-radius: 12px;
            font-size: 15px;
            background-color: #f7fafc;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-group input:focus {
            border-color: var(--yonex-blue);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.05);
        }

        /* 眼睛图标样式 */
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--accent-gray);
            transition: color 0.2s;
        }
        .toggle-password:hover { color: var(--yonex-blue); }

        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #003366 0%, #002244 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2); }

        .alert { font-size: 13px; margin-bottom: 20px; padding: 12px; border-radius: 10px; text-align: center; }
        .error { background: #fff5f5; color: #e53e3e; border: 1px solid rgba(229, 62, 62, 0.1); }
        .success { background: #f0fff4; color: #38a169; border: 1px solid rgba(56, 161, 105, 0.1); }

        .footer-link { margin-top: 30px; padding-top: 20px; border-top: 1px solid #f1f5f9; font-size: 14px; color: var(--accent-gray); }
        .footer-link a { color: var(--yonex-blue); text-decoration: none; font-weight: 700; }
        .footer-link a:hover { text-decoration: underline; }
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
        <div class="input-group">
            <input type="text" name="username" placeholder="Full Name (e.g. Lin Dan)" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" id="password" placeholder="Password (Upper, Lower, Num, Symbol)" required>
            <!-- 初始状态设为闭眼 fa-eye-slash -->
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('password', this)"></i>
        </div>
        <div class="input-group">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            <!-- 初始状态设为闭眼 fa-eye-slash -->
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('confirm_password', this)"></i>
        </div>
        <button type="submit" class="btn">REGISTER NOW</button>
    </form>
    
    <div class="footer-link">
        Already a member? <a href="login page.php">Login Here</a>
    </div>
</div>

<script>
    function togglePass(inputId, icon) {
        const inputField = document.getElementById(inputId);
        if (inputField.type === "password") {
            // 点击后切换为明文并睁眼
            inputField.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            // 再次点击切换为隐藏并闭眼
            inputField.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }
</script>

</body>
</html>
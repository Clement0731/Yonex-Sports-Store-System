<?php
require_once __DIR__ . '/../db.php';
session_start();
$pageTitle = "Yonex - Login Portal";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST['login_input']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login_input, $login_input]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../index.php");
            exit;
        } else {
            $error = "Invalid identity or password. Please try again.";
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
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, sans-serif;
            background: radial-gradient(circle at center, #ffffff 0%, #e9eff5 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: -50%; left: -50%; width: 200%; height: 200%;
            background: repeating-linear-gradient(45deg, transparent, transparent 100px, rgba(0, 51, 102, 0.015) 100px, rgba(0, 51, 102, 0.015) 200px);
            z-index: 0;
        }

        .login-card {
            background: #ffffff;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 51, 102, 0.08);
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(0, 51, 102, 0.05);
        }

        .main-title {
            font-size: 38px;
            font-weight: 900;
            color: var(--yonex-blue);
            margin: 0;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .sub-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--accent-gray);
            margin: 8px 0 40px 0;
            letter-spacing: 1px;
        }

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

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
        }

        .error-msg {
            color: #e53e3e;
            font-size: 13px;
            margin-bottom: 20px;
            padding: 8px;
            background: #fff5f5;
            border-radius: 8px;
        }

        .register-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            font-size: 14px;
            color: var(--accent-gray);
        }

        .register-footer a {
            color: var(--yonex-blue);
            text-decoration: none;
            font-weight: 700;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h1 class="main-title">YONEX</h1>
    <p class="sub-title">Sign in to your account</p>
    
    <?php if ($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <input type="text" name="login_input" placeholder="Username or Email" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePass('password', this)"></i>
        </div>
        <button type="submit" class="btn">LOGIN</button>
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
</script>

</body>
</html>
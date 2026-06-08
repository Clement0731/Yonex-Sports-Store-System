<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE EMAIL = '$email' AND PASSWORD = '$password' AND ROLE = 'Admin'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['admin_id'] = $row['USER_ID'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Email or Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Yonex</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-body {
            background:url('../images/LEEBG.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
    </style>
</head>
<body class="login-body">
<div class="login-box">
    <img src="../images/yonex_logo.png" alt="YONEX Logo" style="max-width: 320px; margin-bottom: 10px; display: inline-block;">
    <p style="font-size: 20px; font-weight: bold; color: #000000;">Admin Portal Secure Login</p>
    <?php if(isset($error)) { echo "<div class='error-msg'>$error</div>"; } ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="example@gmail.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-login">Login to Dashboard</button>
        <a href="forgot_password.php" style="display: block; margin-top: 15px; color: #0033a0; text-decoration: none; font-size: 14px; font-weight: bold;">Forgot Password?</a>
    </form>
</div>
</body>
</html>
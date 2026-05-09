<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM USERS WHERE EMAIL = '$email' AND PASSWORD = '$password' AND ROLE = 'Admin'";
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
</head>
<body class="login-body">
<div class="login-box">
    <h2>YONEX</h2>
    <p>Admin Portal Secure Login</p>
    <?php if(isset($error)) { echo "<div class='error-msg'>$error</div>"; } ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="text" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-login">Login to Dashboard</button>
    </form>
</div>
</body>
</html>
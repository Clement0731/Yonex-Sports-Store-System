<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$conn = new mysqli("localhost", "root", "", "yonex_db");
$user_id = $_SESSION['user_id'];

// 处理表单保存逻辑（仅处理Name, Gender, Birthday）
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $gender   = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);

    $update_query = "UPDATE users SET username='$username', gender='$gender', birthday='$birthday' WHERE id='$user_id'";
    $conn->query($update_query);
}

$user = $conn->query("SELECT * FROM users WHERE id = '$user_id'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --yonex-blue: #002B7F; --bg-color: #f4f6f8; }
        body { background-color: var(--bg-color); font-family: 'Segoe UI', Roboto, sans-serif; display: flex; justify-content: center; padding-bottom: 30px; }
        .container { width: 100%; max-width: 480px; padding: 0 15px; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .save-btn { color: var(--yonex-blue); font-weight: bold; border: none; background: none; font-size: 16px; cursor: pointer; }
        .card { background: white; border-radius: 12px; padding: 0 20px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .avatar-card { display: flex; flex-direction: column; align-items: center; padding: 20px; }
        .avatar { width: 80px; height: 80px; background: var(--yonex-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px; }
        .list-item { display: flex; justify-content: space-between; align-items: center; padding: 18px 0; border-bottom: 1px solid #f0f0f0; cursor: pointer; }
        .list-item:last-child { border-bottom: none; }
        .label { color: #888; font-size: 15px; }
        .value { color: #333; display: flex; align-items: center; gap: 8px; font-weight: 500; }
        .chevron { color: var(--yonex-blue); font-size: 14px; }
        input, select { border: none; text-align: right; font-size: 15px; color: #333; outline: none; background: none; }
    </style>
</head>
<body>
<div class="container">
    <form method="POST">
        <header class="header">
            <a href="user_profile.php" style="color: var(--yonex-blue);"><i class="fas fa-arrow-left"></i></a>
            <h1>Edit Profile</h1>
            <button type="submit" class="save-btn">Save</button>
        </header>

        <div class="card avatar-card"><div class="avatar"><i class="fas fa-user"></i></div></div>

        <div class="card">
            <div class="list-item">
                <span class="label">Name</span>
                <div class="value"><input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"> <i class="fas fa-chevron-right chevron"></i></div>
            </div>
            <div class="list-item">
                <span class="label">Gender</span>
                <div class="value">
                    <select name="gender">
                        <option value="Male" <?php if($user['gender']=='Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if($user['gender']=='Female') echo 'selected'; ?>>Female</option>
                    </select>
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
            </div>
            <div class="list-item">
                <span class="label">Birthday</span>
                <div class="value"><input type="date" name="birthday" value="<?php echo $user['birthday']; ?>"> <i class="fas fa-chevron-right chevron"></i></div>
            </div>
        </div>

        <div class="card">
            <div class="list-item" onclick="window.location.href='change_phone.php'">
                <span class="label">Phone</span>
                <div class="value">
                    <span><?php echo htmlspecialchars($user['phone'] ?? ''); ?></span> 
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
            </div>
            <div class="list-item" onclick="window.location.href='change_email.php'">
                <span class="label">Email</span>
                <div class="value">
                    <span><?php echo htmlspecialchars($user['email'] ?? ''); ?></span> 
                    <i class="fas fa-chevron-right chevron"></i>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
</html>
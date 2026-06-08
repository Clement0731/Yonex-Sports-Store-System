<?php
// 1. 开启 Session 并检查用户是否登录
session_start();

if (!isset($_SESSION['user_id'])) {
    // 🛠️ 路径修正：因为此文件在 login_register 文件夹内，直接跳转到同目录下的 login.php
    header("Location: login.php");
    exit();
}

// 连接数据库
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "yonex_db"; 

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$message = "";

// 处理表单更新保存
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $gender   = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);

    // SQL 语句中也移除了 bio 字段
    $update_query = "UPDATE users SET username='$username', gender='$gender', birthday='$birthday', phone='$phone', email='$email' WHERE id='$user_id'";
    
    if ($conn->query($update_query) === TRUE) {
        $message = "<div class='alert success'>个人资讯更新成功！</div>";
    } else {
        $message = "<div class='alert error'>更新失败: " . $conn->error . "</div>";
    }
}

// 获取最新数据用于页面回显
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// 手机号星号脱敏处理
if (!empty($user['phone'])) {
    $phone_len = strlen($user['phone']);
    $display_phone = ($phone_len > 4) ? str_repeat('*', $phone_len - 4) . substr($user['phone'], -2) : $user['phone'];
} else {
    $display_phone = "现在设定";
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改个人资讯 - Yonex Badminton</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            background-color: #f7f8fa;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 480px;
            min-height: 100vh;
            background-color: #f7f8fa;
            padding-bottom: 30px;
        }

        .header {
            background-color: #ffffff;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
            border-bottom: 1px solid #eaeaea;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .back-btn {
            color: #ff4500; 
            font-size: 20px;
            text-decoration: none;
        }

        .header h1 {
            font-size: 18px;
            font-weight: normal;
            color: #111;
        }

        .save-btn {
            background: none;
            border: none;
            color: #002B7F; 
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .alert {
            margin: 10px 12px 0 12px;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            text-align: center;
        }
        .success { background-color: #e6f4ea; color: #137333; }
        .error { background-color: #fce8e6; color: #c5221f; }

        .avatar-section {
            background-color: #ffffff;
            margin: 12px;
            border-radius: 8px;
            padding: 25px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .avatar-placeholder {
            width: 75px;
            height: 75px;
            background-color: #002B7F;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .avatar-placeholder i {
            color: #ffffff;
            font-size: 35px;
        }

        .edit-avatar-text {
            color: #555555;
            font-size: 14px;
        }

        .info-group {
            background-color: #ffffff;
            margin: 0 12px 12px 12px;
            border-radius: 8px;
            overflow: hidden;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 15px;
            border-bottom: 1px solid #f2f2f2;
            cursor: pointer; 
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .label {
            font-size: 15px;
            color: #333333;
        }

        .hint-icon {
            color: #b0b0b0;
            font-size: 13px;
            margin-left: 4px;
        }

        .value-wrapper {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            width: 70%;
            position: relative;
        }

        .input-field, .select-field {
            border: none;
            outline: none;
            text-align: right;
            font-size: 14px;
            color: #333333;
            width: 100%;
            background: transparent;
        }

        .select-field {
            direction: rtl;
        }

        .input-field::placeholder, 
        .select-field.not-set {
            color: #ff5b45 !important;
        }

        .fa-chevron-right {
            color: #cccccc;
            font-size: 13px;
        }

        /* 生日专用样式 */
        .date-display {
            font-size: 14px;
            color: #333333;
        }
        .date-display.not-set {
            color: #ff5b45;
        }
        
        /* 采用更加安全的完全隐藏，但不使用 display: none，确保 JS 触发不被阻拦 */
        #real-date-input {
            opacity: 0;
            position: absolute;
            z-index: -1;
            width: 0;
            height: 0;
            border: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <form action="edit_profile.php" method="POST">
            
            <header class="header">
                <a href="user_profile.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1>修改个人资讯</h1>
                <button type="submit" class="save-btn">保存</button>
            </header>

            <?php echo $message; ?>

            <div class="avatar-section">
                <div class="avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
                <div class="edit-avatar-text">
                    <i class="far fa-edit"></i> 编辑
                </div>
            </div>

            <div class="info-group">
                <div class="info-item" onclick="this.querySelector('input').focus();">
                    <span class="label">名称</span>
                    <div class="value-wrapper">
                        <input type="text" name="username" class="input-field" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" placeholder="现在设定">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-item">
                    <span class="label">性别 <i class="far fa-question-circle hint-icon"></i></span>
                    <div class="value-wrapper">
                        <select name="gender" class="select-field <?php echo empty($user['gender']) ? 'not-set' : ''; ?>" onchange="this.classList.remove('not-set')">
                            <option value="" <?php echo empty($user['gender']) ? 'selected' : ''; ?>>现在设定</option>
                            <option value="男" <?php echo ($user['gender'] ?? '') == '男' ? 'selected' : ''; ?>>男</option>
                            <option value="女" <?php echo ($user['gender'] ?? '') == '女' ? 'selected' : ''; ?>>女</option>
                            <option value="保密" <?php echo ($user['gender'] ?? '') == '保密' ? 'selected' : ''; ?>>保密</option>
                        </select>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
                
                <div class="info-item" onclick="triggerDatePicker()">
                    <span class="label">生日 <i class="far fa-question-circle hint-icon"></i></span>
                    <div class="value-wrapper">
                        <span class="date-display <?php echo empty($user['birthday']) ? 'not-set' : ''; ?>" id="birthday-text">
                            <?php echo !empty($user['birthday']) ? htmlspecialchars($user['birthday']) : '现在设定'; ?>
                        </span>
                        <input type="date" id="real-date-input" name="birthday" value="<?php echo htmlspecialchars($user['birthday'] ?? ''); ?>" onchange="updateDateDisplay(this.value)">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-item" onclick="this.querySelector('input').focus();">
                    <span class="label">手机</span>
                    <div class="value-wrapper">
                        <input type="text" name="phone" class="input-field" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="<?php echo $display_phone; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
                <div class="info-item" onclick="this.querySelector('input').focus();">
                    <span class="label">电邮</span>
                    <div class="value-wrapper">
                        <input type="email" name="email" class="input-field" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" placeholder="现在设定">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <script>
        function triggerDatePicker() {
            const dateInput = document.getElementById('real-date-input');
            if (typeof dateInput.showPicker === 'function') {
                dateInput.showPicker();
            } else {
                dateInput.click();
            }
        }

        function updateDateDisplay(value) {
            const textDisplay = document.getElementById('birthday-text');
            if(value) {
                textDisplay.innerText = value;
                textDisplay.classList.remove('not-set');
            } else {
                textDisplay.innerText = '现在设定';
                textDisplay.classList.add('not-set');
            }
        }
    </script>

</body>
</html>
<?php
$conn->close();
?>
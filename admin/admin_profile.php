<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// 引入 PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$admin_id = $_SESSION['admin_id'];
$msg = "";

// 每次刷新都获取最新资料
$sql = "SELECT * FROM admin WHERE USER_ID = '$admin_id'";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();

// ==========================================
// 💡 功能 1.1：请求修改邮箱，发送 OTP
// ==========================================
if (isset($_POST['request_email_change'])) {
    $new_email = trim($_POST['new_email']);
    
    // 检查邮箱格式和是否被占用
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $msg = "<div class='alert alert-danger'>Invalid email format! (邮箱格式不正确)</div>";
    } else {
        $check_sql = "SELECT * FROM admin WHERE EMAIL = '$new_email' AND USER_ID != '$admin_id'";
        if ($conn->query($check_sql)->num_rows > 0) {
            $msg = "<div class='alert alert-danger'>This email is already in use! (该邮箱已被占用)</div>";
        } else {
            // 生成验证码，存入 Session (暂不改数据库)
            $otp = rand(100000, 999999);
            $_SESSION['pending_email'] = $new_email;
            $_SESSION['email_change_otp'] = $otp;
            $_SESSION['email_change_expiry'] = time() + (15 * 60); // 15分钟过期

            // 开始发邮件给新邮箱
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                
                // 👇 换成你的专属发件邮箱
                $mail->Username   = '你的专属发件邮箱@gmail.com'; 
                // 👇 换成你的 16 位专用密码
                $mail->Password   = 'abcdefghijklmnop'; 
                
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // 👇 发件人信息
                $mail->setFrom('你的专属发件邮箱@gmail.com', 'Yonex Admin System');
                
                $mail->addAddress($new_email); // 发给请求更换的新邮箱

                $mail->isHTML(true);
                $mail->Subject = 'Verify Your New Email - Yonex Admin';
                $mail->Body    = "<h3>Your Email Verification OTP is: <b style='color:red;'>$otp</b></h3><p>Please enter this code in your profile page to confirm your new email address. It will expire in 15 minutes.</p>";

                $mail->send();
                $msg = "<div class='alert alert-success'>An OTP has been sent to <b>$new_email</b>. Please enter it below to confirm. (验证码已发送至新邮箱，请验证)</div>";
            } catch (Exception $e) {
                $msg = "<div class='alert alert-danger'>Email could not be sent. Error: {$mail->ErrorInfo}</div>";
                unset($_SESSION['pending_email']); // 发送失败则取消暂存
            }
        }
    }
}

// ==========================================
// 💡 功能 1.2：验证 OTP 并真正修改邮箱
// ==========================================
if (isset($_POST['verify_email_change'])) {
    $entered_otp = $_POST['email_otp'];
    
    if (isset($_SESSION['pending_email']) && isset($_SESSION['email_change_otp'])) {
        if (time() > $_SESSION['email_change_expiry']) {
            $msg = "<div class='alert alert-danger'>OTP has expired! Please request again. (验证码已过期)</div>";
            unset($_SESSION['pending_email']);
        } elseif ($entered_otp == $_SESSION['email_change_otp']) {
            // 验证成功！更新数据库
            $new_email = $_SESSION['pending_email'];
            $conn->query("UPDATE USERS SET EMAIL = '$new_email' WHERE USER_ID = '$admin_id'");
            
            $msg = "<div class='alert alert-success'>Email updated successfully to $new_email! (邮箱更新成功)</div>";
            $admin['EMAIL'] = $new_email; // 刷新画面
            
            // 清除 Session 记录
            unset($_SESSION['pending_email']);
            unset($_SESSION['email_change_otp']);
        } else {
            $msg = "<div class='alert alert-danger'>Invalid OTP! (验证码错误)</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>No pending email change request found. (找不到修改请求)</div>";
    }
}

// 取消邮箱修改请求
if (isset($_POST['cancel_email_change'])) {
    unset($_SESSION['pending_email']);
    unset($_SESSION['email_change_otp']);
    header("Location: admin_profile.php");
    exit();
}

// ==========================================
// 💡 功能 2：修改密码
// ==========================================
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $password_regex = "/^(?=.*[A-Z])(?=.*\d).{8,}$/";

    if ($old_password != $admin['PASSWORD']) {
        $msg = "<div class='alert alert-danger'>Incorrect current password! (旧密码错误)</div>";
    } elseif ($new_password != $confirm_password) {
        $msg = "<div class='alert alert-danger'>New passwords do not match! (新密码不一致)</div>";
    } elseif (!preg_match($password_regex, $new_password)) {
        $msg = "<div class='alert alert-danger'>Password must be at least 8 characters long, contain at least 1 uppercase letter and 1 number! (密码太弱：必须满8位，包含大写字母和数字)</div>";
    } else {
        $conn->query("UPDATE USERS SET PASSWORD = '$new_password' WHERE USER_ID = '$admin_id'");
        $msg = "<div class='alert alert-success'>Password changed successfully to a strong password! (强密码修改成功)</div>";
        $admin['PASSWORD'] = $new_password;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile - Yonex Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container { display: flex; gap: 30px; margin-top: 20px; }
        .profile-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); width: 350px; text-align: center; border-top: 4px solid #0033a0; height: fit-content; }
        .profile-avatar { width: 120px; height: 120px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 50px; color: #0033a0; font-weight: bold; margin: 0 auto 20px auto; border: 3px solid #e60012; }
        .profile-card h3 { color: #333; margin-bottom: 5px; font-size: 22px; }
        .profile-card p { color: #888; font-size: 14px; margin-bottom: 20px; word-break: break-all; }
        .role-badge { background: #0033a0; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        
        .settings-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); flex: 1; }
        .settings-card h3 { color: #0033a0; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; color: #555; margin-bottom: 8px; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;}
        .form-group input:focus { outline: none; border-color: #0033a0; }
        .form-group input[readonly] { background: #f9f9f9; cursor: not-allowed; color: #777;}
        
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .btn-save { background: #0033a0; color: white; padding: 12px 25px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { background: #002277; }
        .btn-red { background: #e60012; }
        .btn-red:hover { background: #cc0010; }
        .btn-grey { background: #ccc; color: #333; }
        .btn-grey:hover { background: #bbb; }
        
        .verify-box { background: #f0f4f8; border: 1px solid #cce0f5; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1 style="color: #0033a0; font-weight: 900; letter-spacing: 1px; text-transform: uppercase;">My Profile</h1>
        
        <?php echo $msg; ?>

        <div class="profile-container">
            
            <div class="profile-card">
                <div class="profile-avatar">
                    <?php echo (strlen($admin['EMAIL']) > 0) ? strtoupper(substr($admin['EMAIL'], 0, 1)) : 'A'; ?>
                </div>
                <h3>System Administrator</h3>
                <p><?php echo htmlspecialchars($admin['EMAIL']); ?></p>
                <span class="role-badge"><?php echo strtoupper($admin['ROLE']); ?></span>
            </div>

            <div class="settings-card">
                
                <h3>Account Details (修改账户邮箱)</h3>
                
                <?php if (isset($_SESSION['pending_email'])) { ?>
                    <div class="verify-box">
                        <h4 style="color: #0033a0; margin-bottom: 10px;">Verification Required</h4>
                        <p style="font-size: 14px; margin-bottom: 15px;">We have sent a 6-digit OTP to <b><?php echo htmlspecialchars($_SESSION['pending_email']); ?></b>.</p>
                        
                        <form method="POST" action="admin_profile.php" style="display: flex; gap: 10px; align-items: flex-end;">
                            <div class="form-group" style="margin-bottom: 0; flex: 1;">
                                <label>Enter OTP Code</label>
                                <input type="text" name="email_otp" required placeholder="e.g. 123456" maxlength="6">
                            </div>
                            <button type="submit" name="verify_email_change" class="btn-save btn-red">Verify & Update</button>
                            <button type="submit" name="cancel_email_change" class="btn-save btn-grey">Cancel</button>
                        </form>
                    </div>
                <?php } else { ?>
                    <form method="POST" action="admin_profile.php">
                        <div class="form-group">
                            <label>Current Email Address</label>
                            <input type="email" value="<?php echo htmlspecialchars($admin['EMAIL']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>New Email Address (新邮箱)</label>
                            <input type="email" name="new_email" placeholder="Enter new email to receive OTP" required>
                        </div>
                        <button type="submit" name="request_email_change" class="btn-save">Send OTP to New Email</button>
                    </form>
                <?php } ?>

                <h3 style="margin-top: 40px;">Security & Password (修改高级密码)</h3>
                <form method="POST" action="admin_profile.php">
                    <div class="form-group">
                        <label>Current Password (旧密码)</label>
                        <input type="password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password (新密码)</label>
                        <input type="password" name="new_password" placeholder="Min 8 chars, 1 Uppercase, 1 Number" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password (确认新密码)</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn-save btn-red">Update Password</button>
                </form>

            </div>
        </div>
    </div>

</body>
</html>
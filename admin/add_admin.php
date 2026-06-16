<?php
session_start();
// 权限拦截：确保只有已登录的管理员可以访问
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// 引入你现有的 PHPMailer 组件
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$current_admin_id = $_SESSION['admin_id'];
$msg = "";

// 获取当前登录管理员的邮箱，用于接收授权 OTP
$admin_query = $conn->query("SELECT * FROM admin WHERE USER_ID = '$current_admin_id'");
$current_admin = $admin_query->fetch_assoc();
$current_admin_email = $current_admin['EMAIL'];

// ==========================================
// 阶段 1：暂存数据并发送验证码 (Dispatch OTP)
// ==========================================
if (isset($_POST['request_auth_otp'])) {
    // 收集并净化表单输入
    $new_username = $conn->real_escape_string($_POST['new_username']);
    $new_email = $conn->real_escape_string($_POST['new_email']);
    $new_password = $_POST['new_password'];
    $new_role = $conn->real_escape_string($_POST['new_role']);

    // 密码强度拦截器（匹配你原有的安全标准：至少8位，1大写，1数字）
    $password_regex = "/^(?=.*[A-Z])(?=.*\d).{8,}$/";

    // 检查邮箱是否已被注册
    $email_check = $conn->query("SELECT * FROM admin WHERE EMAIL = '$new_email'");

    if ($email_check->num_rows > 0) {
        $msg = "<div class='alert alert-danger'>Email mapping already exists in administration directory!</div>";
    } elseif (!preg_match($password_regex, $new_password)) {
        $msg = "<div class='alert alert-danger'>Password must be at least 8 characters long, contain at least 1 uppercase letter and 1 number.</div>";
    } else {
        // 将新员工数据暂存至 Session
        $_SESSION['pending_admin_data'] = [
            'username' => $new_username,
            'email' => $new_email,
            'password' => $new_password,
            'role' => $new_role
        ];

        // 生成 6 位安全验证码
        $otp = rand(100000, 999999);
        $_SESSION['add_admin_otp'] = $otp;
        $_SESSION['add_admin_otp_expiry'] = time() + (15 * 60); // 15分钟过期
        $_SESSION['awaiting_add_admin_otp'] = true;

        // 使用你现有的 Gmail 授信凭证发送邮件
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'yonexadmin@gmail.com'; 
            $mail->Password   = 'vztsnwjuxqfvnjod'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('yonexadmin@gmail.com', 'Yonex Admin System');
            $mail->addAddress($current_admin_email); 
            $mail->isHTML(true);
            $mail->Subject = 'Admin Account Provision Authorization';
            $mail->Body    = "<h3>Account Creation Request Detected</h3>
                             <p>You are attempting to create a new administrative profile (<b>$new_username</b>) with the role of <b>$new_role</b>.</p>
                             <h3>Authorization Passkey: <b style='color:red;'>$otp</b></h3>
                             <p>Please enter this code on the verification screen to authorize this command. It will expire in 15 minutes.</p>";

            $mail->send();
            $msg = "<div class='alert alert-success'>Authorization OTP dispatched to your corporate coordinates (<b>$current_admin_email</b>).</div>";
        } catch (Exception $e) {
            $msg = "<div class='alert alert-danger'>Security token routing failed. Error: {$mail->ErrorInfo}</div>";
            unset($_SESSION['awaiting_add_admin_otp']);
            unset($_SESSION['pending_admin_data']);
        }
    }
}

// ==========================================
// 阶段 2：校验验证码并正式写入数据库 (Commit Data)
// ==========================================
if (isset($_POST['verify_and_create'])) {
    $entered_otp = $_POST['auth_otp'];

    if (isset($_SESSION['add_admin_otp']) && isset($_SESSION['awaiting_add_admin_otp'])) {
        if (time() > $_SESSION['add_admin_otp_expiry']) {
            $msg = "<div class='alert alert-danger'>Authorization token has expired! Please retry protocol.</div>";
            unset($_SESSION['awaiting_add_admin_otp']);
            unset($_SESSION['pending_admin_data']);
        } elseif ($entered_otp != $_SESSION['add_admin_otp']) {
            $msg = "<div class='alert alert-danger'>Invalid Security Token! Access Denied.</div>";
        } else {
            // 令牌验证通过，从 Session 取出暂存数据并执行数据库插入
            $u = $_SESSION['pending_admin_data']['username'];
            $e = $_SESSION['pending_admin_data']['email'];
            $p = $_SESSION['pending_admin_data']['password'];
            $r = $_SESSION['pending_admin_data']['role'];

            $sql_insert = "INSERT INTO admin (USERNAME, EMAIL, PASSWORD, ROLE) VALUES ('$u', '$e', '$p', '$r')";
            
            if ($conn->query($sql_insert) === TRUE) {
                $msg = "<div class='alert alert-success'>New account successfully provisioned into the core index!</div>";
                // 成功后销毁暂存令牌状态
                unset($_SESSION['awaiting_add_admin_otp']);
                unset($_SESSION['pending_admin_data']);
                unset($_SESSION['add_admin_otp']);
            } else {
                $msg = "<div class='alert alert-danger'>Database structural injection error: " . $conn->error . "</div>";
            }
        }
    } else {
        $msg = "<div class='alert alert-danger'>No pending profile registration request detected.</div>";
    }
}

// 取消操作，清理状态
if (isset($_POST['cancel_creation'])) {
    unset($_SESSION['awaiting_add_admin_otp']);
    unset($_SESSION['pending_admin_data']);
    unset($_SESSION['add_admin_otp']);
    header("Location: add_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provision Administration Profile | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root { --premium-navy: #002d56; --slate-dark: #0f172a; --slate-muted: #64748b; --border-fine: #e2e8f0; }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--slate-dark); }
        .main-content { padding: 40px; width: 100%; }
        .dashboard-header { border-bottom: 1px solid var(--border-fine); padding-bottom: 20px; margin-bottom: 35px; }
        .dashboard-title { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.02em; color: var(--premium-navy); text-transform: uppercase; }
        .dashboard-subtitle { font-size: 0.85rem; color: var(--slate-muted); letter-spacing: 0.05em; text-transform: uppercase; margin-top: 4px; }
        
        .analytics-section { display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; }
        .block-container { background: #ffffff; border: 1px solid var(--border-fine); padding: 30px; }
        .block-title { font-size: 0.85rem; font-weight: 700; letter-spacing: 0.06em; color: var(--premium-navy); text-transform: uppercase; border-left: 2px solid var(--premium-navy); padding-left: 10px; margin-bottom: 25px; }
        
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--premium-navy); margin-bottom: 8px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid var(--border-fine); box-sizing: border-box; font-size: 0.9rem; outline: none; background: #fff; }
        
        .btn-submit { background: var(--premium-navy); color: white; padding: 12px 24px; border: none; font-weight: 700; text-transform: uppercase; cursor: pointer; font-size: 0.8rem; letter-spacing: 0.05em; display: inline-block; }
        .btn-submit:hover { background: #001f3f; }
        .btn-cancel { background: transparent; color: var(--slate-muted); border: 1px solid var(--border-fine); padding: 11px 24px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; text-decoration: none; display: inline-block; }
        .btn-cancel:hover { border-color: #0f172a; color: #0f172a; }

        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; font-size: 13px; border-left: 4px solid; }
        .alert-success { background: #ecfdf5; color: #10b981; border-color: #10b981; }
        .alert-danger { background: #fef2f2; color: #ef4444; border-color: #ef4444; }
        .verification-wrapper { border: 1px solid #fca5a5; padding: 25px; background: #fff5f5; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Account Provisioning</h1>
            <p class="dashboard-subtitle">Enterprise Staff Expansion Console</p>
        </div>
        
        <?php echo $msg; ?>

        <div class="analytics-section">
            <div class="block-container">
                
                <?php if (isset($_SESSION['awaiting_add_admin_otp'])) { ?>
                    <div class="verification-wrapper">
                        <h4 style="color: #ef4444; margin-top: 0; font-size: 0.85rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 10px;">Security Override Token Required</h4>
                        <p style="font-size: 0.85rem; color: var(--slate-muted); margin-bottom: 20px;">A dynamic core passkey was routed to the supervisor terminal: <b><?php echo htmlspecialchars($current_admin_email); ?></b>.</p>
                        
                        <form method="POST" action="add_admin.php">
                            <div class="form-group">
                                <label>Enter 6-Digit Authorization OTP</label>
                                <input type="text" name="auth_otp" required placeholder="e.g. 123456" maxlength="6" style="border-color: #fca5a5; font-family: monospace; font-size: 1.2rem; letter-spacing: 4px;">
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" name="verify_and_create" class="btn-submit" style="background:#ef4444;">Authorize & Commit Account</button>
                                <button type="submit" name="cancel_creation" class="btn-cancel" formnovalidate>Abort Protocol</button>
                            </div>
                        </form>
                    </div>
                <?php } else { ?>
                    <h2 class="block-title">Profile Parameters</h2>
                    <form method="POST" action="add_admin.php">
                        <div class="form-group">
                            <label>Staff/Admin Username</label>
                            <input type="text" name="new_username" placeholder="e.g. Marcus_Yonex" required>
                        </div>
                        <div class="form-group">
                            <label>Corporate Email Mapping</label>
                            <input type="email" name="new_email" placeholder="e.g. staff@yonex.com" required>
                        </div>
                        <div class="form-group">
                            <label>Initial Security Access Password</label>
                            <input type="password" name="new_password" placeholder="Min 8 chars, 1 Uppercase, 1 Number" required>
                        </div>
                        <div class="form-group">
                            <label>System Directory Role</label>
                            <select name="new_role" required>
                                <option value="Admin">System Administrator (Full Control)</option>
                                <option value="Staff">Operational Staff (Logistics & Inventory Only)</option>
                            </select>
                        </div>
                        <div style="margin-top: 30px;">
                            <button type="submit" name="request_auth_otp" class="btn-submit">Dispatch Security OTP Token</button>
                        </div>
                    </form>
                <?php } ?>

            </div>

            <div class="block-container" style="background: #f8fafc;">
                <h2 class="block-title" style="color: var(--slate-muted); border-color: var(--slate-muted);">System Directive</h2>
                <p style="font-size: 0.85rem; color: var(--slate-muted); line-height: 1.6; margin-bottom: 15px;">
                    Provisioning administrative nodes grants immediate system control overrides, database structural modifications, and sales index management access.
                </p>
                <p style="font-size: 0.85rem; color: var(--slate-muted); line-height: 1.6;">
                    <b>Security Notice:</b> Every deployment requires verification clearance routing directly through your supervisor key profile mapping (<span style="color:var(--premium-navy); font-weight:700;"><?php echo htmlspecialchars($current_admin_email); ?></span>).
                </p>
            </div>
        </div>
    </div>

</body>
</html>
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$admin_id = $_SESSION['admin_id'];
$msg = "";

$sql = "SELECT * FROM admin WHERE USER_ID = '$admin_id'";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();
$current_email = $admin['EMAIL'];

// ==========================================
// Feature 1: Request Password Change, Send OTP
// ==========================================
if (isset($_POST['request_pwd_otp'])) {
    $otp = rand(100000, 999999);
    $_SESSION['pwd_otp'] = $otp;
    $_SESSION['pwd_otp_expiry'] = time() + (15 * 60); 
    $_SESSION['awaiting_pwd_otp'] = true;

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
        $mail->addAddress($current_email);
        $mail->isHTML(true);
        $mail->Subject = 'Password Change OTP - Yonex Admin';
        $mail->Body    = "<h3>Your OTP to change password is: <b style='color:red;'>$otp</b></h3><p>Please enter this code in your profile page to authorize the password change. It will expire in 15 minutes.</p>";

        $mail->send();
        $msg = "<div class='alert alert-success'>An OTP has been sent to your email (<b>$current_email</b>). Please check your inbox.</div>";
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger'>Email could not be sent. Error: {$mail->ErrorInfo}</div>";
        unset($_SESSION['awaiting_pwd_otp']); 
    }
}

// ==========================================
// Feature 2: Verify OTP and Change Password
// ==========================================
if (isset($_POST['verify_and_change_pwd'])) {
    $entered_otp = $_POST['pwd_otp'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $password_regex = "/^(?=.*[A-Z])(?=.*\d).{8,}$/";

    if (isset($_SESSION['pwd_otp']) && isset($_SESSION['awaiting_pwd_otp'])) {
        if (time() > $_SESSION['pwd_otp_expiry']) {
            $msg = "<div class='alert alert-danger'>OTP has expired! Please request again.</div>";
            unset($_SESSION['awaiting_pwd_otp']);
            unset($_SESSION['pwd_otp']);
        } elseif ($entered_otp != $_SESSION['pwd_otp']) {
            $msg = "<div class='alert alert-danger'>Invalid OTP!</div>";
        } elseif ($new_password != $confirm_password) {
            $msg = "<div class='alert alert-danger'>New passwords do not match!</div>";
        } elseif (!preg_match($password_regex, $new_password)) {
            $msg = "<div class='alert alert-danger'>Password must be at least 8 characters long, contain at least 1 uppercase letter and 1 number.</div>";
        } else {
            $conn->query("UPDATE admin SET PASSWORD = '$new_password' WHERE USER_ID = '$admin_id'");
            $msg = "<div class='alert alert-success'>Password updated successfully!</div>";
            $admin['PASSWORD'] = $new_password; 
            unset($_SESSION['awaiting_pwd_otp']);
            unset($_SESSION['pwd_otp']);
        }
    } else {
        $msg = "<div class='alert alert-danger'>No pending password change request found.</div>";
    }
}

if (isset($_POST['cancel_pwd_change'])) {
    unset($_SESSION['awaiting_pwd_otp']);
    unset($_SESSION['pwd_otp']);
    header("Location: admin_profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile Matrix | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root { --premium-navy: #002d56; --slate-dark: #0f172a; --slate-muted: #64748b; --border-fine: #e2e8f0; }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--slate-dark); }
        .main-content { padding: 40px; width: 100%; }
        .dashboard-header { border-bottom: 1px solid var(--border-fine); padding-bottom: 20px; margin-bottom: 35px; }
        .dashboard-title { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.02em; color: var(--premium-navy); text-transform: uppercase; }
        .dashboard-subtitle { font-size: 0.85rem; color: var(--text-muted); letter-spacing: 0.05em; text-transform: uppercase; margin-top: 4px; }
        
        .analytics-section { display: grid; grid-template-columns: 1fr 2.2fr; gap: 30px; }
        .block-container { background: #ffffff; border: 1px solid var(--border-fine); padding: 30px; border-radius: 0; }
        .block-title { font-size: 0.85rem; font-weight: 700; letter-spacing: 0.06em; color: var(--premium-navy); text-transform: uppercase; border-left: 2px solid var(--premium-navy); padding-left: 10px; margin-bottom: 25px; }
        
        .profile-avatar-box { width: 90px; height: 90px; background: #f1f5f9; border: 1px solid var(--border-fine); display: flex; align-items: center; justify-content: center; font-size: 32px; color: var(--premium-navy); font-weight: bold; margin: 0 auto 20px auto; }
        .user-status-badge { font-size: 0.65rem; font-weight: 700; padding: 4px 10px; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; color: #10b981; background: #ecfdf5; border: 1px solid #6ee7b7; margin-top: 5px;}
        
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--premium-navy); margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid var(--border-fine); box-sizing: border-box; font-size: 0.9rem; outline: none; background: #fff; }
        .form-group input[readonly] { background: #f8fafc; color: var(--slate-muted); cursor: not-allowed; }
        
        .btn-submit { background: var(--premium-navy); color: white; padding: 12px 24px; border: none; font-weight: 700; text-transform: uppercase; cursor: pointer; font-size: 0.8rem; letter-spacing: 0.05em; display: inline-block; }
        .btn-submit:hover { background: #001f3f; }
        .btn-cancel { background: transparent; color: var(--slate-muted); border: 1px solid var(--border-fine); padding: 11px 24px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; text-decoration: none; display: inline-block; }
        .btn-cancel:hover { border-color: #0f172a; color: #0f172a; }

        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; font-size: 13px; border-left: 4px solid; }
        .alert-success { background: #ecfdf5; color: #10b981; border-color: #10b981; }
        .alert-danger { background: #fef2f2; color: #ef4444; border-color: #ef4444; }
        .verification-wrapper { border: 1px solid var(--border-fine); padding: 25px; background: #f8fafc; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">My Profile</h1>
            <p class="dashboard-subtitle">Enterprise Administration Control Panel</p>
        </div>
        
        <?php echo $msg; ?>

        <div class="analytics-section">
            <div class="block-container">
                <h2 class="block-title">Identity Index</h2>
                <div style="text-align: center; padding: 10px 0;">
                    <div class="profile-avatar-box">
                        <?php echo (strlen($admin['EMAIL']) > 0) ? strtoupper(substr($admin['EMAIL'], 0, 1)) : 'A'; ?>
                    </div>
                    <div style="font-weight: 700; font-size: 1.05rem; color: var(--slate-dark);"><?php echo htmlspecialchars($admin['USERNAME'] ?? 'Administrator'); ?></div>
                    <div style="font-size: 0.8rem; color: var(--slate-muted); margin-top: 4px; word-break: break-all;"><?php echo htmlspecialchars($admin['EMAIL']); ?></div>
                    <div class="user-status-badge"><?php echo htmlspecialchars($admin['ROLE'] ?? 'ADMIN'); ?></div>
                </div>
            </div>

            <div class="block-container">
                <h2 class="block-title">Account Parameters</h2>
                <div class="form-group">
                    <label>Administrator Username (Read-Only)</label>
                    <input type="text" value="<?php echo htmlspecialchars($admin['USERNAME'] ?? 'Administrator'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Primary Email Mapping (Read-Only)</label>
                    <input type="email" value="<?php echo htmlspecialchars($admin['EMAIL']); ?>" readonly>
                </div>

                <h2 class="block-title" style="margin-top: 45px;">Security Authentication Override</h2>
                
                <?php if (isset($_SESSION['awaiting_pwd_otp'])) { ?>
                    <div class="verification-wrapper">
                        <h4 style="color: #ef4444; margin-top: 0; font-size: 0.85rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 10px;">Token Verification Required</h4>
                        <p style="font-size: 0.85rem; color: var(--slate-muted); margin-bottom: 20px;">A 6-digit dynamic OTP secure passkey was routed to <b><?php echo htmlspecialchars($current_email); ?></b>.</p>
                        
                        <form method="POST" action="admin_profile.php">
                            <div class="form-group">
                                <label>Enter Dynamic OTP Token</label>
                                <input type="text" name="pwd_otp" required placeholder="e.g. 123456" maxlength="6" style="border-color: #fca5a5;">
                            </div>
                            <div class="form-group">
                                <label>New Password Matrix</label>
                                <input type="password" name="new_password" placeholder="Min 8 chars, 1 Uppercase, 1 Number" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password Matrix</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <button type="submit" name="verify_and_change_pwd" class="btn-submit">Update Credentials</button>
                                <button type="submit" name="cancel_pwd_change" class="btn-cancel" formnovalidate>Cancel Protocol</button>
                            </div>
                        </form>
                    </div>
                <?php } else { ?>
                    <p style="font-size: 0.85rem; color: var(--slate-muted); margin-bottom: 20px; line-height: 1.6;">To rewrite security hashes, identity authorization must override via One-Time Password dynamic routing directly into your administration coordinates.</p>
                    <form method="POST" action="admin_profile.php">
                        <button type="submit" name="request_pwd_otp" class="btn-submit">Dispatch Security OTP Token</button>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>

</body>
</html>
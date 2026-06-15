<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$admin_id = $_SESSION['admin_id'];
$msg = "";

// Fetch latest data on refresh
$sql = "SELECT * FROM admin WHERE USER_ID = '$admin_id'";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();
$current_email = $admin['EMAIL'];

// ==========================================
// Feature 1: Request Password Change, Send OTP
// ==========================================
if (isset($_POST['request_pwd_otp'])) {
    // Generate OTP, store in Session
    $otp = rand(100000, 999999);
    $_SESSION['pwd_otp'] = $otp;
    $_SESSION['pwd_otp_expiry'] = time() + (15 * 60); // 15 mins expiry
    $_SESSION['awaiting_pwd_otp'] = true;

    // Send email to admin's current email
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
            // Success! Update DB
            $conn->query("UPDATE admin SET PASSWORD = '$new_password' WHERE USER_ID = '$admin_id'");
            
            $msg = "<div class='alert alert-success'>Password updated successfully!</div>";
            $admin['PASSWORD'] = $new_password; 
            
            // Clear Session
            unset($_SESSION['awaiting_pwd_otp']);
            unset($_SESSION['pwd_otp']);
        }
    } else {
        $msg = "<div class='alert alert-danger'>No pending password change request found.</div>";
    }
}

// Cancel Request
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
    <title>Admin Profile - Yonex Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container { display: flex; gap: 30px; margin-top: 20px; }
        .profile-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); width: 350px; text-align: center; border-top: 4px solid #0033a0; height: fit-content; }
        .profile-avatar { width: 120px; height: 120px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 50px; color: #0033a0; font-weight: bold; margin: 0 auto 20px auto; border: 3px solid #e60012; }
        .profile-card h3 { color: #333; margin-bottom: 5px; font-size: 22px; }
        .profile-card p { color: #888; font-size: 14px; margin-bottom: 20px; word-break: break-all; }
        .role-badge { background: #0033a0; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        
        .settings-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); flex: 1; }
        .settings-card h3 { color: #0033a0; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-weight: bold; color: #555; margin-bottom: 8px; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; transition: border-color 0.3s; }
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
        
        .verify-box { background: #fdf2f2; border: 1px solid #f8d7da; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
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
                <h3><?php echo htmlspecialchars($admin['USERNAME'] ?? 'System Administrator'); ?></h3>
                <p><?php echo htmlspecialchars($admin['EMAIL']); ?></p>
                <span class="role-badge"><?php echo htmlspecialchars($admin['ROLE'] ?? 'ADMIN'); ?></span>
            </div>

            <div class="settings-card">
                
                <h3>Account Details</h3>
                <div class="form-group">
                    <label>Administrator Name (Read-only)</label>
                    <input type="text" value="<?php echo htmlspecialchars($admin['USERNAME'] ?? 'Administrator'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Admin Email Address (Read-only)</label>
                    <input type="email" value="<?php echo htmlspecialchars($admin['EMAIL']); ?>" readonly>
                </div>

                <h3 style="margin-top: 40px;">Security & Password</h3>
                
                <?php if (isset($_SESSION['awaiting_pwd_otp'])) { ?>
                    <div class="verify-box">
                        <h4 style="color: #e60012; margin-bottom: 10px;">Verification Required</h4>
                        <p style="font-size: 14px; margin-bottom: 15px;">Please check your email <b><?php echo htmlspecialchars($current_email); ?></b> for the 6-digit OTP.</p>
                        
                        <form method="POST" action="admin_profile.php">
                            <div class="form-group">
                                <label>Enter OTP Code</label>
                                <input type="text" name="pwd_otp" required placeholder="e.g. 123456" maxlength="6" style="border: 1px solid #e60012;">
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" placeholder="Min 8 chars, 1 Uppercase, 1 Number" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" name="verify_and_change_pwd" class="btn-save btn-red">Update Password</button>
                                <button type="submit" name="cancel_pwd_change" class="btn-save btn-grey" formnovalidate>Cancel</button>
                            </div>
                        </form>
                    </div>
                <?php } else { ?>
                    <p style="font-size: 14px; color: #666; margin-bottom: 20px;">To update your password, we need to verify your identity by sending an OTP to your current email address.</p>
                    <form method="POST" action="admin_profile.php">
                        <button type="submit" name="request_pwd_otp" class="btn-save">Send OTP to Email & Change Password</button>
                    </form>
                <?php } ?>

            </div>
        </div>
    </div>

</body>
</html>
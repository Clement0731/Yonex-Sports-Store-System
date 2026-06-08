<?php
// 1. 引入 PHPMailer 核心类文件（根据你的目录结构精准匹配）
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

require_once 'db.php';
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    try {
        // 查找邮箱是否存在
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // 生成唯一 Token
            $token = bin2hex(random_bytes(32));
            
            // 存入 Session
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_token_time'] = time();

            // 生成绝对路径的重置链接，方便用户在邮件客户端里直接点击
            $reset_link = "http://localhost/fyp/Yonex-Sports-Store-System/login_register/reset_password.php?token=" . $token;

            // ==================== 【核心修改：使用 PHPMailer 发送真实邮件】 ====================
            $mail = new PHPMailer(true);

            try {
                // 服务器配置
                $mail->isSMTP();                                            // 使用 SMTP 发送
                $mail->Host       = 'smtp.gmail.com';                          // 如果你用的是 QQ 邮箱
                // $mail->Host    = 'smtp.gmail.com';                       // 如果你用的是 Gmail 邮箱
                
                $mail->SMTPAuth   = true;                                   // 开启 SMTP 认证
                $mail->Username   = 'teolijie4@gmail.com';                   // 【填写：你的发件人邮箱账号】
                $mail->Password   = 'hodzkfiyllwycvxy';                         // 【填写：你获取的十六位客户端授权码，而非登录密码！】
                
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // 开启 SSL 加密
                $mail->Port       = 465;                                    // SSL 端口号
                $mail->CharSet    = 'UTF-8';                                // 防止邮件中文乱码

                // 收件人与发件人
                $mail->setFrom('teolijie4@gmail.com', 'YONEX Official');
                $mail->addAddress($email);                                  // 动态接收前端表单提交的用户邮箱

                // 邮件内容
                $mail->isHTML(true);                                        // 支持 HTML 格式
                $mail->Subject = 'YONEX - Reset Password Link';
                
                // 邮件正文（支持 HTML 标签，看起来更专业）
                $mail->Body    = "
                    <h3>Dear User,</h3>
                    <p>We received a request to reset your password. Please click the link below to proceed:</p>
                    <p><a href='{$reset_link}' style='padding: 10px 20px; background-color: #003366; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a></p>
                    <p>Or copy this link to your browser:</p>
                    <p><a href='{$reset_link}'>{$reset_link}</a></p>
                    <br>
                    <p><i>Valid for 5 minutes. If you did not make this request, please ignore this email.</i></p>
                ";

                $mail->send();

                // 【关键修正】发送成功后，不再传递 preview_link 参数，直接跳转干净的成功状态
                header("Location: reset_password.php?status=sent");
                exit;

            } catch (Exception $e) {
                $error = "Mail could not be sent. Error: {$mail->ErrorInfo}";
            }
            // ===============================================================================

        } else {
            $error = "This email address is not registered.";
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
    <title>Yonex - Forgot Password</title>
    <style>
        :root { --yonex-blue: #003366; --accent-gray: #718096; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: radial-gradient(circle at center, #ffffff 0%, #e9eff5 100%); display: flex; justify-content: center; align-items: center; height: 100vh; overflow: hidden; position: relative; }
        body::before { content: ""; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: repeating-linear-gradient(45deg, transparent, transparent 100px, rgba(0, 51, 102, 0.015) 100px, rgba(0, 51, 102, 0.015) 200px); z-index: 0; }
        .login-card { background: #ffffff; padding: 50px 40px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0, 51, 102, 0.08); width: 100%; max-width: 400px; text-align: center; position: relative; z-index: 1; border: 1px solid rgba(0, 51, 102, 0.05); }
        .main-title { font-size: 24px; font-weight: 800; color: var(--yonex-blue); margin: 0 0 10px 0; letter-spacing: 2px; text-transform: uppercase; }
        .sub-title { font-size: 13px; font-weight: 500; color: var(--accent-gray); margin: 0 0 30px 0; line-height: 1.5; }
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group input { width: 100%; padding: 15px; border: 1.5px solid #edf2f7; border-radius: 12px; font-size: 15px; background-color: #f7fafc; outline: none; transition: all 0.2s ease; }
        .input-group input:focus { border-color: var(--yonex-blue); background-color: #fff; box-shadow: 0 0 0 4px rgba(0, 51, 102, 0.05); }
        .btn { width: 100%; padding: 16px; background: linear-gradient(135deg, #003366 0%, #002244 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2); }
        .error-msg { color: #e53e3e; font-size: 13px; margin-bottom: 20px; padding: 8px; background: #fff5f5; border-radius: 8px; }
        .back-to-login { margin-top: 25px; font-size: 14px; }
        .back-to-login a { color: var(--yonex-blue); text-decoration: none; font-weight: 700; }
        .back-to-login a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-card">
    <h2 class="main-title">Find Password</h2>
    <p class="sub-title">Enter your registered email below to receive a password reset link.</p>

    <?php if ($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <input type="email" name="email" placeholder="Enter Your Email Address" required>
        </div>
        <button type="submit" class="btn">REQUEST RESET LINK</button>
    </form>

    <div class="back-to-login">
        <a href="login_page.php">← Back to Login</a>
    </div>
</div>

</body>
</html>
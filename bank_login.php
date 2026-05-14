<?php
session_start();
$bank = isset($_GET['bank']) ? $_GET['bank'] : 'Bank';
$amount = isset($_GET['amount']) ? $_GET['amount'] : '0.00';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($bank); ?> Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .login-container { max-width: 400px; margin: 100px auto; }
        .bank-header { 
            background: #ffffff; 
            padding: 20px; 
            border-bottom: 3px solid #003366; 
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .login-box { 
            background: white; 
            padding: 30px; 
            border-radius: 0 0 8px 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }
        .btn-login { 
            background: #003366; 
            color: white; 
            width: 100%; 
            padding: 12px; 
            font-weight: bold; 
            border: none;
            border-radius: 4px;
        }
        .btn-login:hover { background: #002244; color: white; }
        .payment-info { background: #fffbe6; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffe58f; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="bank-header">
        <h4 class="mb-0 text-uppercase" style="letter-spacing: 1px; color: #003366;"><?php echo htmlspecialchars($bank); ?></h4>
    </div>
    <div class="login-box">
        <div class="payment-info text-center">
            <small class="text-muted">Transfer Amount:</small>
            <div class="h5 mb-0 fw-bold">RM <?php echo number_format((float)$amount, 2); ?></div>
        </div>

        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label small fw-bold">USER ID</label>
                <input type="text" id="userid" class="form-control form-control-lg" placeholder="Enter User ID" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">PASSWORD</label>
                <input type="password" id="pwd" class="form-control form-control-lg" placeholder="Enter Password" required>
                <div class="form-text mt-2" style="font-size: 0.75rem;">
                    * Must include: Uppercase, Lowercase, Number, and Symbol.
                </div>
            </div>

            <button type="button" class="btn btn-login" onclick="validateAndPay()">LOGIN & PAY</button>
            
            <div class="text-center mt-3">
                <a href="fpx_payment.php" class="text-muted small text-decoration-none">Cancel Transaction</a>
            </div>
        </form>
    </div>
    <p class="text-center mt-4 text-muted small">© Secure Banking Portal System</p>
</div>

<script>
function validateAndPay() {
    const password = document.getElementById('pwd').value;
    const userid = document.getElementById('userid').value;

    // 正则表达式检查：大写、小写、数字、特殊符号
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    if (userid === "") {
        alert("Please enter your User ID.");
        return;
    }

    // 判断是否全部符合条件
    if (hasUpper && hasLower && hasNumber && hasSpecial) {
        // 符合条件，跳转到支付成功页面
        const bank = "<?php echo urlencode($bank); ?>";
        const amount = "<?php echo $amount; ?>";
        window.location.href = 'payment_success.php?method=FPX&amount=' + amount + '&bank=' + bank;
    } else {
        // 不符合条件，弹出失败提示
        alert("Login Failed!\n\nYour password must contain:\n- Uppercase letter\n- Lowercase letter\n- Number\n- Special character (e.g. !@#$)");
    }
}
</script>

</body>
</html>
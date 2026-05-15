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
            background: #ffffff; padding: 20px; border-bottom: 3px solid #003366; 
            text-align: center; border-radius: 8px 8px 0 0;
        }
        .login-box { 
            background: white; padding: 30px; border-radius: 0 0 8px 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }
        .btn-login { 
            background: #003366; color: white; width: 100%; padding: 12px; 
            font-weight: bold; border: none; border-radius: 4px;
        }
        .btn-login:hover { background: #002244; color: white; }
        .payment-info { background: #fffbe6; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffe58f; }
        .is-invalid { border-color: #dc3545 !important; }
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

        <form id="loginForm" onsubmit="event.preventDefault(); validateAndPay();">
            <div class="mb-3">
                <label class="form-label small fw-bold">USER ID</label>
                <input type="text" id="userid" class="form-control form-control-lg" placeholder="e.g. lijie1234" maxlength="12" required>
                <div id="userid-feedback" class="form-text" style="font-size: 0.75rem;">* Must be a mix of letters & numbers (Max 12).</div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">PASSWORD</label>
                <input type="password" id="pwd" class="form-control form-control-lg" placeholder="Enter Password" required>
            </div>

            <button type="submit" class="btn btn-login">LOGIN & PAY</button>
            
            <div class="text-center mt-3">
                <a href="fpx_payment.php" class="text-muted small text-decoration-none">Cancel Transaction</a>
            </div>
        </form>
    </div>
    <p class="text-center mt-4 text-muted small">© Secure Banking Portal System</p>
</div>

<script>
function validateAndPay() {
    const useridInput = document.getElementById('userid');
    const userid = useridInput.value.trim();
    const password = document.getElementById('pwd').value;

    // --- USER ID 逻辑修改 ---

    // 1. 正则表达式解释：
    // (?=.*[a-zA-Z])  -> 必须包含至少一个字母
    // (?=.*[0-9])     -> 必须包含至少一个数字
    // [a-zA-Z0-9]{2,12} -> 只能包含字母和数字，长度在2到12位之间
    const idRegex = /^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]{2,12}$/;

    if (!idRegex.test(userid)) {
        alert("USER ID ERROR!\n\n1. Must contain BOTH letters and numbers.\n2. No special characters allowed.\n3. Maximum 12 characters.");
        useridInput.classList.add('is-invalid');
        useridInput.focus();
        return;
    } else {
        useridInput.classList.remove('is-invalid');
    }

    // --- 密码校验 (维持你原有的强度要求) ---
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasPwdNum = /[0-9]/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    if (hasUpper && hasLower && hasPwdNum && hasSpecial) {
        // 校验通过，执行跳转
        const bank = "<?php echo urlencode($bank); ?>";
        const amount = "<?php echo $amount; ?>";
        window.location.href = 'payment_success.php?method=FPX&amount=' + amount + '&bank=' + bank;
    } else {
        alert("LOGIN FAILED:\nPassword must include Uppercase, Lowercase, Number, and Special Symbol.");
    }
}
</script>

</body>
</html>
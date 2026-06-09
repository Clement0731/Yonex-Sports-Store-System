<?php
session_start();
$bank = isset($_GET['bank']) ? $_GET['bank'] : 'Bank';
$amount = isset($_GET['amount']) ? $_GET['amount'] : '0.00';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($bank); ?> Secure Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #eef2f6; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-container { 
            width: 100%;
            max-width: 420px; 
            margin: 20px;
        }
        .bank-header { 
            background: #003366; 
            color: #fff;
            padding: 25px 20px; 
            text-align: center; 
            border-radius: 12px 12px 0 0;
            position: relative;
        }
        .bank-header h4 { font-weight: 700; letter-spacing: 1px; margin-bottom: 5px; }
        .bank-header p { margin: 0; font-size: 0.85rem; color: #b3cce6; }
        
        .login-box { 
            background: white; 
            padding: 35px 30px; 
            border-radius: 0 0 12px 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.08); 
        }
        .payment-info { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            border: 1px solid #e9ecef; 
            border-left: 4px solid #003366;
        }
        .form-control-lg {
            font-size: 1rem;
            border-radius: 8px;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #003366;
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.1);
        }
        .btn-login { 
            background: #003366; color: white; width: 100%; padding: 14px; 
            font-weight: bold; border: none; border-radius: 8px;
            letter-spacing: 1px; transition: 0.3s; margin-top: 10px;
        }
        .btn-login:hover { background: #001f3f; color: #FFD700; transform: translateY(-2px); }
        .is-invalid { border-color: #dc3545 !important; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="bank-header">
        <i class="fas fa-shield-alt fs-2 mb-2 text-warning"></i>
        <h4 class="text-uppercase"><?php echo htmlspecialchars($bank); ?></h4>
        <p>Secure Internet Banking Gateway</p>
    </div>
    <div class="login-box">
        <div class="payment-info d-flex justify-content-between align-items-center">
            <span class="text-muted fw-medium small">Transfer Amount</span>
            <span class="h5 mb-0 fw-bold" style="color: #003366;">RM <?php echo number_format((float)$amount, 2); ?></span>
        </div>

        <form id="loginForm" onsubmit="event.preventDefault(); validateAndPay();">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">USER ID</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" id="userid" class="form-control form-control-lg" placeholder="e.g. lijie1234" maxlength="12" required>
                </div>
                <div id="userid-feedback" class="form-text text-muted mt-2" style="font-size: 0.75rem;">
                    <i class="fas fa-info-circle me-1"></i>Must be a mix of letters & numbers (Max 12).
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" id="pwd" class="form-control form-control-lg" placeholder="Enter Password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login">LOGIN & PAY</button>
            
            <div class="text-center mt-4">
                <a href="fpx_payment.php" class="text-muted small text-decoration-none hover-underline"><i class="fas fa-arrow-left me-1"></i>Cancel Transaction</a>
            </div>
        </form>
    </div>
    <div class="text-center mt-4 text-muted small">
        <i class="fas fa-lock me-1"></i> End-to-End Encrypted Transaction
    </div>
</div>

<script>
function validateAndPay() {
    const useridInput = document.getElementById('userid');
    const userid = useridInput.value.trim();
    const password = document.getElementById('pwd').value;

    const idRegex = /^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]{2,12}$/;

    if (!idRegex.test(userid)) {
        alert("USER ID ERROR!\n\n1. Must contain BOTH letters and numbers.\n2. No special characters allowed.\n3. Maximum 12 characters.");
        useridInput.classList.add('is-invalid');
        useridInput.focus();
        return;
    } else {
        useridInput.classList.remove('is-invalid');
    }

    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasPwdNum = /[0-9]/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    if (hasUpper && hasLower && hasPwdNum && hasSpecial) {
        const bank = "<?php echo urlencode($bank); ?>";
        const amount = "<?php echo $amount; ?>";
        // 传递 payment method 以便 success 页面显示
        window.location.href = 'payment_success.php?method=FPX&amount=' + amount + '&bank=' + bank;
    } else {
        alert("LOGIN FAILED:\nPassword must include Uppercase, Lowercase, Number, and Special Symbol.");
    }
}
</script>

</body>
</html>
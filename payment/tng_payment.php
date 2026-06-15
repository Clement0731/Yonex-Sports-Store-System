<?php
session_start();
require_once 'db_config.php';

$total_raw = isset($_GET['total']) ? $_GET['total'] : '0.00';
$total_clean = str_replace(',', '', $total_raw);
$total = (float)$total_clean;
$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
$addr_id = isset($_GET['addr_id']) ? $_GET['addr_id'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TNG eWallet | Secure Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --tng-blue: #005bac; --tng-light-blue: #e6f0fa; }
        body { background-color: #f3f4f6; font-family: 'Inter', sans-serif; }
        .tng-box { max-width: 420px; margin: 50px auto; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 35px; }
        .tng-logo-header { text-align: center; margin-bottom: 25px; }
        .form-control-custom { border: 1px solid #d1d5db; border-radius: 12px; padding: 14px; font-size: 1rem; width: 100%; transition: all 0.2s; }
        .form-control-custom:focus { border-color: var(--tng-blue); outline: none; box-shadow: 0 0 0 3px rgba(0,91,172,0.15); }
        .btn-tng { background: var(--tng-blue); color: white; border: none; width: 100%; padding: 15px; border-radius: 12px; font-weight: 600; font-size: 1rem; transition: background 0.2s; }
        .btn-tng:hover { background: #004b8d; }
        .pin-input { letter-spacing: 8px; text-align: center; font-size: 1.5rem; }
        .price-badge { background: var(--tng-light-blue); color: var(--tng-blue); font-weight: 700; font-size: 1.6rem; text-align: center; padding: 12px; border-radius: 12px; margin-bottom: 25px; }
        .account-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="tng-box">
        <div class="tng-logo-header">
            <h3 style="color: var(--tng-blue); font-weight: 800; letter-spacing: -0.5px;">
                <i class="fas fa-wallet me-2"></i>Touch 'n Go <span style="font-weight: 400; color: #374151;">eWallet</span>
            </h3>
            <p class="text-muted small mt-1">Official Secure Payment Gateway</p>
        </div>

        <div class="price-badge">
            RM <?php echo number_format($total, 2); ?>
        </div>

        <form action="tng_process.php" method="POST" id="tng-payment-form">
            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
            <input type="hidden" name="product_ids" value="<?php echo $ids; ?>">
            <input type="hidden" name="addr_id" value="<?php echo $addr_id; ?>">

            <div id="tng-step-login">
                <div class="text-uppercase text-secondary fw-bold mb-3 small" style="letter-spacing: 0.5px;">Step 1: Account Login</div>
                
                <div class="mb-3">
                    <label for="phone_no" class="form-label small text-muted fw-medium">Mobile Number</label>
                    <input type="text" id="phone_no" name="phone_no" class="form-control-custom" placeholder="0123456789" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    <div class="form-text text-muted small mt-1">Enter your mobile number (e.g. 0123456789)</div>
                </div>

                <div class="mb-4">
                    <label for="payment_pin" class="form-label small text-muted fw-medium">6-Digit Wallet PIN</label>
                    <input type="password" id="payment_pin" name="payment_pin" class="form-control-custom pin-input" maxlength="6" autocomplete="new-password" oninput="this.value = this.value.replace(/[^0-9]/g, '');" placeholder="••••••">
                </div>

                <button type="button" class="btn-tng" onclick="handleTngLogin()">
                    LOG IN <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>

            <div id="tng-step-confirm" style="display: none;">
                <div class="text-uppercase text-success fw-bold mb-3 small" style="letter-spacing: 0.5px;">Step 2: Confirm Transaction</div>
                
                <h5 class="fw-bold mb-3">Payment Authorization</h5>

                <div class="account-box">
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Deduct From:</span>
                        <strong class="text-dark" id="display-tng-phone">012-*** ****</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Merchant:</span>
                        <strong class="text-dark">YONEX Badminton Website</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Wallet Balance:</span>
                        <span class="fw-bold text-success">Sufficient Balance</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 p-3 fw-bold rounded-3" style="background-color: #10b981; border: none;">
                    <i class="fas fa-lock me-1"></i> CONFIRM & PAY NOW
                </button>

                <button type="button" class="btn btn-link w-100 text-muted small mt-2 text-decoration-none" onclick="backToLoginStep()">
                    <i class="fas fa-chevron-left me-1"></i> Back to Modify Login
                </button>
            </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <a href="check_out.php?ids=<?php echo $ids; ?>" class="text-muted small text-decoration-none fw-medium">
                <i class="fas fa-times me-1"></i> Cancel Transaction
            </a>
        </div>
    </div>
</div>

<script>
function handleTngLogin() {
    const phone = document.getElementById('phone_no').value.trim();
    const pin = document.getElementById('payment_pin').value;

    // 沿用你原本严谨的 10 位数且以 01 开头的大马手机格式校验
    if ((phone.length < 10 || phone.length > 11) || !phone.startsWith('01')) {
        alert("Error: Phone number must be 10 or 11 digits and start with '01'!");
        document.getElementById('phone_no').focus();
        return;
    }   

    // 校验 6 位 PIN 码长度
    if (pin.length !== 6) {
        alert("Error: Payment PIN must be exactly 6 digits!");
        document.getElementById('payment_pin').focus();
        return;
    }

    // 登录验证通过！处理手机号脱敏显示 (例如: 0123456789 -> 012-***6789)
    let maskedPhone = phone.substring(0, 3) + "-*** " + phone.substring(6);
    document.getElementById('display-tng-phone').innerText = "TNG Account (" + maskedPhone + ")";

    // 隐藏登录表单，平滑展出确定扣款面板
    document.getElementById('tng-step-login').style.display = 'none';
    document.getElementById('tng-step-confirm').style.display = 'block';
}

function backToLoginStep() {
    document.getElementById('tng-step-confirm').style.display = 'none';
    document.getElementById('tng-step-login').style.display = 'block';
}
</script>

</body>
</html>
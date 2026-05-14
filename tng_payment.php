<?php
session_start();
require_once 'db_config.php';

$total_raw = isset($_GET['total']) ? $_GET['total'] : '0.00';
$total_clean = str_replace(',', '', $total_raw);
$total = (float)$total_clean;
$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TNG eWallet | Login to Pay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --tng-blue: #005bac;
            --yonex-blue: #002d56;
            --gold: #ffcc00;
        }

        body {
            background: radial-gradient(circle at center, #004a8d 0%, #001a33 100%);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden; 
        }

        .payment-card {
            background: white;
            width: 95%;
            max-width: 380px;
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            overflow: hidden;
        }

        .card-header-custom {
            background: var(--yonex-blue);
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--gold);
        }

        .tng-title { color: white; font-size: 0.9rem; font-weight: 700; margin: 0; }
        .logo-box img { height: 35px; border-radius: 4px; width: auto; }

        .amount-banner {
            background: #f0f7ff;
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #e1eefd;
        }

        .amount-val {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--tng-blue);
            margin: 0;
        }

        .form-section { padding: 25px 30px; }
        .form-label { font-size: 0.85rem; font-weight: 600; color: #555; display: block; margin-bottom: 5px; }
        .input-group-custom { margin-bottom: 20px; }

        .form-control-custom {
            border: 2px solid #eee;
            border-radius: 12px;
            padding: 12px;
            font-size: 1rem;
            width: 100%;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .form-control-custom:focus {
            border-color: var(--tng-blue);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 91, 172, 0.1);
        }

        .pin-input { letter-spacing: 8px; text-align: center; }

        .btn-pay {
            background: var(--tng-blue);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            margin-top: 5px;
            transition: 0.3s;
            cursor: pointer;
        }

        .footer-links { text-align: center; margin-top: 15px; }
        .cancel-link { color: #bbb; text-decoration: none; font-size: 0.75rem; }
        .secure-note { font-size: 0.65rem; color: #ccc; text-align: center; margin-top: 15px; display: flex; align-items: center; justify-content: center; gap: 4px; }
    </style>
</head>
<body>

<div class="payment-card">
    <div class="card-header-custom">
        <h1 class="tng-title">TNG eWallet Payment</h1>
        <div class="logo-box">
            <img src="images/4.jpg" alt="Logo">
        </div>
    </div>

    <div class="amount-banner">
        <p style="font-size: 0.7rem; color: #666; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 1px;">Total Amount</p>
        <h2 class="amount-val">RM <?php echo number_format($total, 2); ?></h2>
    </div>

    <div class="form-section">
        <form action="tng_process.php" method="POST" onsubmit="return validateForm()">
            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
            <input type="hidden" name="product_ids" value="<?php echo $ids; ?>">

            <div class="input-group-custom">
                <label class="form-label">Phone Number</label>
                <input type="tel" 
                       id="phone_no" 
                       name="phone_no" 
                       class="form-control-custom" 
                       placeholder="01xxxxxxxx" 
                       maxlength="10" 
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                       required>
            </div>

            <div class="input-group-custom">
                <label class="form-label">6-Digit Payment PIN</label>
                <input type="password" 
                       id="payment_pin"
                       name="payment_pin" 
                       class="form-control-custom pin-input" 
                       maxlength="6" 
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                       placeholder="******" 
                       required>
            </div>

            <button type="submit" class="btn-pay">LOGIN & PAY NOW</button>
        </form>

        <div class="footer-links">
            <a href="check_out.php?ids=<?php echo $ids; ?>" class="cancel-link">Cancel and return to store</a>
        </div>

        <div class="secure-note">
            <svg width="10" height="10" fill="currentColor" viewBox="0 0 20 20"><path d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"></path></svg>
            SECURE ENCRYPTED TRANSACTION
        </div>
    </div>
</div>

<script>
    function validateForm() {
        const phone = document.getElementById('phone_no').value;
        const pin = document.getElementById('payment_pin').value;

        // 1. 检查电话号码是否符合要求 (10位 且 01开头)
        if (phone.length !== 10 || !phone.startsWith('01')) {
            alert("Error: Phone number must be exactly 10 digits and start with '01'!");
            document.getElementById('phone_no').focus();
            return false; 
        }

        // 2. 检查密码是否符合要求 (必须是6位)
        if (pin.length !== 6) {
            alert("Error: Please enter a valid 6-digit payment PIN!");
            document.getElementById('payment_pin').focus();
            return false;
        }

        // 全部通过则跳转
        return true; 
    }
</script>

</body>
</html>
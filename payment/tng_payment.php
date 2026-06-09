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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --tng-blue: #005bac;
            --tng-light-blue: #e6f0fa;
        }

        body {
            background-color: #f4f7f9;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .payment-card {
            background: white;
            width: 100%;
            max-width: 400px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            border-top: 5px solid var(--tng-blue);
        }

        .card-header-custom {
            padding: 25px 20px 15px;
            text-align: center;
        }

        .logo-box {
            display: inline-block;
            background: white;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }

        .logo-box img { 
            height: 40px; 
            width: auto; 
        }

        .amount-banner {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 1px dashed #dee2e6;
        }

        .amount-val {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #1a1a1a;
            margin: 0;
        }

        .form-section { padding: 25px 30px; }
        .form-label { font-size: 0.85rem; font-weight: 600; color: #6c757d; margin-bottom: 8px; }
        
        .form-control-custom {
            border: 1px solid #ced4da;
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 14px;
            font-size: 1rem;
            width: 100%;
            transition: 0.3s;
        }

        .form-control-custom:focus {
            border-color: var(--tng-blue);
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 91, 172, 0.15);
        }

        .pin-input { letter-spacing: 10px; text-align: center; font-weight: bold; font-size: 1.2rem; }

        .btn-pay {
            background: var(--tng-blue);
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.05rem;
            margin-top: 15px;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(0, 91, 172, 0.3);
        }

        .btn-pay:hover { background: #004a8d; transform: translateY(-2px); }

        .secure-note { font-size: 0.7rem; color: #adb5bd; text-align: center; margin-top: 25px; }
    </style>
</head>
<body>

<div class="payment-card">
    <div class="card-header-custom">
        <div class="logo-box">
            <img src="../images/payment/4.jpg" alt="TNG Logo" onerror="this.outerHTML='<h4 class=\'m-0 fw-bold\' style=\'color:#005bac;\'>TNG eWallet</h4>'">
        </div>
        <h5 class="fw-bold mb-0 text-dark">Merchant Payment</h5>
    </div>

    <div class="amount-banner">
        <p class="text-muted small fw-medium mb-1">Total Amount</p>
        <h2 class="amount-val">RM <?php echo number_format($total, 2); ?></h2>
    </div>

    <div class="form-section">
        <form action="tng_process.php" method="POST" onsubmit="return validateForm()">
            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
            <input type="hidden" name="product_ids" value="<?php echo $ids; ?>">

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 bg-white" style="border-radius: 10px 0 0 10px;"><i class="fas fa-mobile-alt text-muted"></i></span>
                    <input type="tel" id="phone_no" name="phone_no" class="form-control form-control-custom border-start-0" style="border-radius: 0 10px 10px 0;" placeholder="01xxxxxxxx" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">6-Digit PIN</label>
                <input type="password" id="payment_pin" name="payment_pin" class="form-control-custom pin-input" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '');" placeholder="••••••" required>
            </div>

            <button type="submit" class="btn-pay">AUTHORIZE PAYMENT</button>
        </form>

        <div class="text-center mt-4">
            <a href="check_out.php?ids=<?php echo $ids; ?>" class="text-muted small text-decoration-none fw-medium"><i class="fas fa-times me-1"></i> Cancel Transaction</a>
        </div>

        <div class="secure-note">
            <i class="fas fa-shield-alt me-1"></i> Secured by TNG Digital
        </div>
    </div>
</div>

<script>
    function validateForm() {
        const phone = document.getElementById('phone_no').value;
        const pin = document.getElementById('payment_pin').value;

        if (phone.length !== 10 || !phone.startsWith('01')) {
            alert("Error: Phone number must be exactly 10 digits and start with '01'!");
            document.getElementById('phone_no').focus();
            return false; 
        }

        if (pin.length !== 6) {
            alert("Error: Please enter a valid 6-digit payment PIN!");
            document.getElementById('payment_pin').focus();
            return false;
        }

        return true; 
    }
</script>

</body>
</html>
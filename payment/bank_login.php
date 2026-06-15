<?php
session_start();
$bank = isset($_GET['bank']) ? $_GET['bank'] : 'Bank';
$amount = isset($_GET['amount']) ? $_GET['amount'] : '0.00';
$addr_id = isset($_GET['addr_id']) ? $_GET['addr_id'] : '';
$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
$qtys = isset($_GET['qtys']) ? $_GET['qtys'] : '';

// 6 家银行的专属核心色彩体系
$bank_configs = [
    'Maybank (MAE)' => [
        'logo' => '5.jpg',
        'primary' => '#FFCC00',
        'grad_to' => '#F4B400',
        'header_text' => '#212529',
        'btn_hover' => '#DBA000',
        'btn_text' => '#212529',
        'rgb' => '255, 204, 0'
    ],
    'CIMB Clicks' => [
        'logo' => '6.jpg',
        'primary' => '#E10011',
        'grad_to' => '#99000A',
        'header_text' => '#FFFFFF',
        'btn_hover' => '#7A0008',
        'btn_text' => '#FFFFFF',
        'rgb' => '225, 0, 17'
    ],
    'Public Bank' => [
        'logo' => '7.jpg',
        'primary' => '#D91B24',
        'grad_to' => '#A81118',
        'header_text' => '#FFFFFF',
        'btn_hover' => '#850B10',
        'btn_text' => '#FFFFFF',
        'rgb' => '217, 27, 36'
    ],
    'RHB Bank' => [
        'logo' => '8.jpg',
        'primary' => '#005EA6',
        'grad_to' => '#003A66',
        'header_text' => '#FFFFFF',
        'btn_hover' => '#002947',
        'btn_text' => '#FFFFFF',
        'rgb' => '0, 94, 166'
    ],
    'Hong Leong' => [
        'logo' => '9.jpg',
        'primary' => '#003399',
        'grad_to' => '#001F5C',
        'header_text' => '#FFFFFF',
        'btn_hover' => '#00143B',
        'btn_text' => '#FFFFFF',
        'rgb' => '0, 51, 153'
    ],
    'AmBank' => [
        'logo' => '10.jpg',
        'primary' => '#FF5000',
        'grad_to' => '#C43D00',
        'header_text' => '#FFFFFF',
        'btn_hover' => '#9E3100',
        'btn_text' => '#FFFFFF',
        'rgb' => '255, 80, 0'
    ]
];

$current_style = isset($bank_configs[$bank]) ? $bank_configs[$bank] : [
    'logo' => 'placeholder.jpg',
    'primary' => '#005bac',
    'grad_to' => '#003D75',
    'header_text' => '#FFFFFF',
    'btn_hover' => '#00264A',
    'btn_text' => '#FFFFFF',
    'rgb' => '0, 91, 172'
];

// --- 🏆 核心修复：直接指定 payment 文件夹路径 ---
$logo_filename = $current_style['logo'];
$img_path = "../images/payment/" . $logo_filename;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($bank); ?> Secure Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bank-primary: <?php echo $current_style['primary']; ?>;
            --bank-grad-to: <?php echo $current_style['grad_to']; ?>;
            --bank-header-text: <?php echo $current_style['header_text']; ?>;
            --bank-hover: <?php echo $current_style['btn_hover']; ?>;
            --bank-btn-text: <?php echo $current_style['btn_text']; ?>;
            --bank-rgb: <?php echo $current_style['rgb']; ?>;
            
            --checkout-primary: #003366;
            --checkout-grad-to: #001F3F;
            --checkout-success: #10b981;
            --checkout-success-hover: #059669;
        }

        body { 
            background-color: #f1f5f9; 
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(var(--bank-rgb), 0.12) 0%, transparent 50%),
                radial-gradient(circle at 85% 85%, rgba(var(--bank-rgb), 0.08) 0%, transparent 45%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: background-image 0.6s ease;
            padding: 20px 0;
        }
        
        body.unified-bg {
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(0, 51, 102, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 85% 85%, rgba(16, 185, 129, 0.05) 0%, transparent 45%);
        }

        .payment-container { width: 100%; max-width: 440px; margin: auto; }
        .card { border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06); overflow: hidden; background: #fff; }
        
        .bank-header { 
            position: relative; 
            background: linear-gradient(135deg, var(--bank-primary) 0%, var(--bank-grad-to) 100%); 
            padding: 42px 25px 70px 25px; 
            text-align: center; 
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .bank-header.unified-view { 
            background: linear-gradient(135deg, var(--checkout-primary) 0%, var(--checkout-grad-to) 100%); 
            padding: 30px 25px 35px 25px;
        }
        
        .portal-subtext { 
            position: relative; 
            z-index: 10; 
            color: var(--bank-header-text); 
            font-size: 0.85rem; 
            font-weight: 500; 
            opacity: 0.85; 
            transition: all 0.5s ease;
        }
        .bank-header.unified-view .portal-subtext { color: #ffffff; opacity: 0.8; }

        .wave-wrapper { 
            position: absolute; 
            bottom: -1px; 
            left: 0; 
            width: 100%; 
            line-height: 0; 
            z-index: 2; 
            transition: all 0.4s ease;
        }
        .bank-header.unified-view .wave-wrapper { 
            opacity: 0; 
            transform: translateY(25px); 
            pointer-events: none;
        }

        .card-body { padding: 35px 35px; background: #fff; position: relative; z-index: 5; }

        .btn-bank-login { 
            background-color: var(--bank-primary); 
            color: var(--bank-btn-text); 
            border: none; 
            padding: 14px; 
            font-weight: 600; 
            border-radius: 10px; 
            width: 100%; 
            transition: all 0.3s; 
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }
        .btn-bank-login:hover { 
            background-color: var(--bank-hover); 
            color: var(--bank-btn-text); 
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        .step-indicator-bank { font-size: 0.8rem; font-weight: 700; color: var(--bank-primary); text-transform: uppercase; letter-spacing: 1px; }
        <?php if($bank == 'Maybank (MAE)') echo '.step-indicator-bank { color: #D6A000; }'; ?>

        .step-indicator-unified { font-size: 0.8rem; font-weight: 700; color: var(--checkout-success); text-transform: uppercase; letter-spacing: 1px; }
        .amount-display { font-size: 2.2rem; font-weight: 800; color: #0f172a; text-align: center; margin: 15px 0; letter-spacing: -0.5px; }
        .account-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 22px; }
        
        .btn-unified-pay {
            background-color: var(--checkout-success);
            color: #fff;
            padding: 14px;
            font-weight: 700;
            border-radius: 10px;
            width: 100%;
            border: none;
            font-size: 1.05rem;
            transition: all 0.3s;
        }
        .btn-unified-pay:hover {
            background-color: var(--checkout-success-hover);
            color: #fff;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);
        }
        
        .secure-footer { text-align: center; margin-top: 25px; font-size: 0.78rem; color: #94a3b8; }
    </style>
</head>
<body id="page-body">

<div class="payment-container">
    <div class="card">
        <div class="bank-header" id="portal-header">
            
            <div style="background: #ffffff; padding: 10px 22px; border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); display: inline-flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 14px; position: relative; z-index: 10; transition: all 0.5s ease;" id="logo-badge-container">
                
                <img src="<?php echo $img_path; ?>" alt="Bank Logo" style="height: 28px; width: auto; object-fit: contain; display: block;">
                
                <div style="width: 1px; height: 18px; background-color: #cbd5e1;"></div>
                
                <span style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0; line-height: 1;"><?php echo htmlspecialchars($bank); ?></span>
            </div>
            
            <div class="portal-subtext" id="portal-subtext">FPX Online Banking Secure Portal</div>
            
            <div class="wave-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 140" preserveAspectRatio="none" style="width: 100%; height: 45px;">
                    <path fill="rgba(255,255,255,0.18)" d="M0,32L60,42.7C120,53,240,75,360,74.7C480,75,600,53,720,42.7C840,32,960,32,1080,42.7C1200,53,1320,75,1380,85.3L1440,96L1440,140L1380,140C1320,140,1200,140,1080,140C960,140,840,140,720,140C600,140,480,140,360,140C240,140,120,140,60,140L0,140Z"></path>
                    <path fill="#ffffff" d="M0,64L60,58.7C120,53,240,43,360,48C480,53,600,75,720,80C840,85,960,75,1080,64C1200,53,1320,43,1380,37.3L1440,32L1440,140L1380,140C1320,140,1200,140,1080,140C960,140,840,140,720,140C600,140,480,140,360,140C240,140,120,140,60,140L0,140Z"></path>
                </svg>
            </div>
        </div>

        <div class="card-body">
            <div id="step-login">
                <div class="step-indicator-bank mb-3">Step 1 of 2: Secure Login</div>
                <h5 class="fw-bold mb-4" style="color: #1e293b;">Login to your account</h5>
                
                <div class="mb-3">
                    <label for="userid" class="form-label small fw-semibold text-secondary">User ID</label>
                    <input type="text" id="userid" class="form-control form-control-lg" placeholder="Enter your online banking ID" autocomplete="off" style="font-size: 0.95rem; border-radius: 8px;">
                </div>
                <div class="mb-4">
                    <label for="pwd" class="form-label small fw-semibold text-secondary">Password</label>
                    <input type="password" id="pwd" class="form-control form-control-lg" placeholder="Enter password" style="font-size: 0.95rem; border-radius: 8px;">
                </div>

                <button type="button" class="btn btn-bank-login" onclick="handleBankLogin()">
                    LOG IN <i class="fas fa-arrow-right ms-1" style="font-size: 0.9rem;"></i>
                </button>
            </div>

            <div id="step-confirm" style="display: none;">
                <div class="step-indicator-unified mb-3">Step 2 of 2: Confirm Payment</div>
                <h5 class="fw-bold mb-3" style="color: #1e293b;">Transaction Details</h5>
                
                <div class="amount-display">RM <?php echo number_format((float)$amount, 2); ?></div>
                
                <div class="account-box">
                    <div class="d-flex justify-content-between small text-muted">
                        <span>From Account:</span>
                        <span class="fw-bold text-dark" id="display-user-id">Savings Account-******</span>
                    </div>
                </div>

                <div class="mb-4 small text-muted bg-light p-2.5 rounded" style="border-left: 3px solid var(--checkout-primary); border-radius: 6px;">
                    <i class="fas fa-shield-alt text-primary me-1"></i> Merchant: <strong>YONEX Official Store</strong>
                </div>

                <button type="button" class="btn btn-unified-pay" onclick="executeFinalPayment()">
                    <i class="fas fa-lock me-1" style="font-size: 0.95rem;"></i> CONFIRM & PAY NOW
                </button>
                
                <button type="button" class="btn btn-link w-100 text-muted small mt-3 text-decoration-none" onclick="backToLogin()">
                    <i class="fas fa-chevron-left me-1" style="font-size: 0.8rem;"></i> Back to Login
                </button>
            </div>
            
        </div>
    </div>
    
    <div class="text-center mt-3">
        <a href="fpx_payment.php?total=<?php echo $amount; ?>&ids=<?php echo $ids; ?>&qtys=<?php echo $qtys; ?>&addr_id=<?php echo $addr_id; ?>" class="text-muted small text-decoration-none">
            <i class="fas fa-times me-1"></i> Cancel and change payment method
        </a>
    </div>
</div>

<div class="secure-footer">
    <p><i class="fas fa-lock me-1"></i> Secure 256-bit SSL Encrypted Connection</p>
    <div class="d-flex justify-content-center gap-3 opacity-50 mt-1">
        <span>FPX Gateway</span>
        <span>•</span>
        <span>Bank Negara Compliant</span>
    </div>
</div>

<script>
function handleBankLogin() {
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

    if (password.length === 0) {
        alert("Please enter your password!");
        return;
    }

    document.getElementById('display-user-id').innerText = "Account (" + userid + ")";
    document.getElementById('step-login').style.display = 'none';
    document.getElementById('step-confirm').style.display = 'block';
    
    document.getElementById('portal-header').classList.add('unified-view');
    document.getElementById('page-body').classList.add('unified-bg');
    document.getElementById('portal-subtext').innerText = "FPX Centralized Secure Payment Gateway";
    document.getElementById('logo-badge-container').style.transform = 'scale(0.92)';
}

function backToLogin() {
    document.getElementById('step-confirm').style.display = 'none';
    document.getElementById('step-login').style.display = 'block';
    
    document.getElementById('portal-header').classList.remove('unified-view');
    document.getElementById('page-body').classList.remove('unified-bg');
    document.getElementById('portal-subtext').innerText = "FPX Online Banking Secure Portal";
    document.getElementById('logo-badge-container').style.transform = 'scale(1)';
}

function executeFinalPayment() {
    const amount = "<?php echo $amount; ?>";
    const addrId = "<?php echo $addr_id; ?>";
    window.location.href = `payment_success.php?amount=${amount}&method=FPX_${encodeURIComponent("<?php echo $bank; ?>")}&addr_id=${addrId}`;
}
</script>

</body>
</html>
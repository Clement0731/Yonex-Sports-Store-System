<?php
session_start();
require_once 'db_config.php';

// 获取从 checkout 传过来的数据
$total = isset($_GET['total']) ? $_GET['total'] : '0.00';
$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
$qtys = isset($_GET['qtys']) ? $_GET['qtys'] : '';

// 银行图片对应关系 (5.jpg - 10.jpg)
$banks = [
    ['name' => 'Maybank (MAE)', 'img' => '5.jpg'], 
    ['name' => 'CIMB Clicks',   'img' => '6.jpg'],
    ['name' => 'Public Bank',   'img' => '7.jpg'],
    ['name' => 'RHB Bank',      'img' => '8.jpg'],
    ['name' => 'Hong Leong bank',        'img' => '9.jpg'],
    ['name' => 'AmBank',    'img' => '10.jpg']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FPX Payment | YONEX Official</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { --yonex-blue: #003366; }
        body { background-color: #f8f9fa; font-family: 'Roboto', sans-serif; }
        .payment-header { background: white; padding: 20px 0; border-bottom: 2px solid var(--yonex-blue); margin-bottom: 30px; }
        .bank-card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 15px; 
            text-align: center; 
            cursor: pointer; 
            background: white;
            transition: 0.2s;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .bank-card:hover { border-color: var(--yonex-blue); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .bank-card.selected { border: 2px solid var(--yonex-blue); background: #f0f7ff; }
        .bank-card img { width: 80px; height: 80px; object-fit: contain; margin-bottom: 10px; }
        .btn-pay { background: var(--yonex-blue); color: white; padding: 12px 0; width: 100%; font-weight: bold; border: none; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>

<div class="payment-header">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="mb-0" style="color: var(--yonex-blue); font-weight: bold;">FPX Online Banking</h4>
        <img src="https://vpay.my/wp-content/uploads/2021/04/fpx-logo.png" height="40" alt="FPX">
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <span class="text-muted">Amount to Pay:</span>
                        <span class="h4 mb-0" style="color:var(--yonex-blue); font-weight:bold;">RM <?php echo number_format($total, 2); ?></span>
                    </div>
                    <hr>
                    <h6 class="mb-3 fw-bold">Select Your Bank:</h6>
                    <div class="row g-3">
                        <?php foreach ($banks as $bank): ?>
                        <div class="col-6 col-md-4">
                            <div class="bank-card" onclick="selectBank(this, '<?php echo $bank['name']; ?>')">
                                <img src="../../images/payment/<?php echo $bank['img']; ?>" alt="<?php echo $bank['name']; ?>">
                                <div class="small fw-bold"><?php echo $bank['name']; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <button class="btn-pay" onclick="processFPX()">PROCEED TO BANK</button>
            <div class="text-center mt-3">
                <a href="check_out.php?ids=<?php echo $ids; ?>&qtys=<?php echo $qtys; ?>" class="text-muted small text-decoration-none">← Back to Checkout</a>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedBankName = '';
    function selectBank(element, name) {
        document.querySelectorAll('.bank-card').forEach(card => card.classList.remove('selected'));
        element.classList.add('selected');
        selectedBankName = name;
    }

    function processFPX() {
        if (!selectedBankName) {
            alert("Please select a bank!");
            return;
        }
        const total = "<?php echo $total; ?>";
        window.location.href = 'bank_login.php?bank=' + encodeURIComponent(selectedBankName) + '&amount=' + total;
    }
</script>
</body>
</html>
<?php
session_start();
require_once 'db_config.php';

$total_raw = isset($_GET['total']) ? $_GET['total'] : '0.00';
$total_clean = str_replace(',', '', $total_raw); 
$total = (float)$total_clean; 

$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
$qtys = isset($_GET['qtys']) ? $_GET['qtys'] : '';
$addr_id = isset($_GET['addr_id']) ? $_GET['addr_id'] : '';

$banks = [
    ['name' => 'Maybank (MAE)', 'img' => '5.jpg'], 
    ['name' => 'CIMB Clicks',   'img' => '6.jpg'],
    ['name' => 'Public Bank',   'img' => '7.jpg'],
    ['name' => 'RHB Bank',      'img' => '8.jpg'],
    ['name' => 'Hong Leong',    'img' => '9.jpg'],
    ['name' => 'AmBank',        'img' => '10.jpg']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FPX Payment | YONEX Official</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
    :root { var(--yonex-blue): #003366; var(--yonex-gold): #FFD700; --bg-color: #f4f7f9; --card-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    body { background-color: var(--bg-color); font-family: 'Roboto', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
    .card-pay { background: #fff; border-radius: 16px; padding: 40px; box-shadow: var(--card-shadow); width: 100%; max-width: 600px; border-top: 5px solid #003366; }
    .bank-card { border: 2px solid #eef2f6; border-radius: 12px; padding: 15px 10px; text-align: center; cursor: pointer; transition: all 0.2s ease; background: #fff; height: 100%; }
    .bank-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-color: #b3cce6; }
    .bank-card.selected { border-color: #FFD700; background-color: #fffdf5; box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.3); }
    .bank-card img { height: 40px; object-fit: contain; margin-bottom: 10px; }
    .btn-pay { background: #003366; color: #fff; padding: 16px; border-radius: 8px; font-weight: 700; font-size: 1.1rem; letter-spacing: 1px; text-transform: uppercase; border: none; width: 100%; transition: 0.3s; margin-top: 30px; }
    .btn-pay:hover { background: #001f3f; color: #FFD700; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="card-pay">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #003366; font-family: 'Oswald', sans-serif;">FPX INTERNET BANKING</h3>
            <p class="text-muted small">Select your preferred bank to complete the transaction.</p>
        </div>

        <div class="p-4 rounded-3 mb-4 text-center" style="background-color:#f8f9fa; border: 1px dashed #ced4da;">
            <span class="text-muted small d-block text-uppercase fw-bold mb-1">Total Amount to Pay</span>
            <h1 class="fw-bold mb-0" style="color: #003366; font-family: 'Oswald', sans-serif;">RM <?php echo number_format($total, 2); ?></h1>
        </div>

        <h6 class="fw-bold mb-3 text-secondary">Select Bank:</h6>
        <div class="row g-3">
            <?php foreach ($banks as $bank): ?>
            <div class="col-6 col-md-4">
                <div class="bank-card" onclick="selectBank(this, '<?php echo $bank['name']; ?>')">
                    <img src="../images/payment/<?php echo $bank['img']; ?>" alt="<?php echo $bank['name']; ?>" onerror="this.src='https://via.placeholder.com/100x40?text=Bank'">
                    <div class="small fw-bold text-dark mt-2"><?php echo $bank['name']; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button class="btn-pay" onclick="processFPX()">PROCEED TO BANK</button>
        
        <div class="text-center mt-4">
            <a href="check_out.php?ids=<?php echo $ids; ?>&qtys=<?php echo $qtys; ?>" class="text-muted small text-decoration-none fw-medium hover-underline">← Cancel and return to Checkout</a>
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
            alert("Please select a bank to proceed!");
            return;
        }
        
        const total = "<?php echo $total; ?>"; 
        const ids = "<?php echo $ids; ?>";
        const qtys = "<?php echo $qtys; ?>";
        const addrId = "<?php echo $addr_id; ?>"; // 传递地址ID

        window.location.href = 'bank_login.php?bank=' + encodeURIComponent(selectedBankName) + '&amount=' + total + '&ids=' + ids + '&qtys=' + qtys + '&addr_id=' + addrId;
    }
</script>
</body>
</html>
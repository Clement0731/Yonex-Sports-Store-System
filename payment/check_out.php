<?php
session_start();
require_once 'db_config.php';

// --- 1. 处理删除地址 ---
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM addresses WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    header("Location: check_out.php?ids=" . $_GET['ids'] . "&qtys=" . $_GET['qtys']);
    exit();
}

// --- 2. 处理新增地址 ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_new_addr'])) {
    $stmt = $conn->prepare("INSERT INTO addresses (receiver_name, receiver_phone, full_address, postcode, city_state, label) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $_POST['name'], $_POST['phone'], $_POST['full_address'], $_POST['postcode'], $_POST['city_state'], $_POST['label']);
    $stmt->execute();
    header("Location: check_out.php?ids=" . $_GET['ids'] . "&qtys=" . $_GET['qtys']);
    exit();
}

// --- 3. 获取地址数据 ---
$address_result = mysqli_query($conn, "SELECT * FROM addresses ORDER BY id DESC");
$all_addresses = mysqli_fetch_all($address_result, MYSQLI_ASSOC);

// 新增：确定要显示哪个地址
$selected_addr = (!empty($all_addresses)) ? $all_addresses[0] : null; 
if (isset($_GET['addr_id'])) {
    foreach ($all_addresses as $addr) {
        if ($addr['id'] == $_GET['addr_id']) {
            $selected_addr = $addr;
            break;
        }
    }
}

// --- 4. 获取商品数据并计算总价 ---
$ids_str = isset($_GET['ids']) ? $_GET['ids'] : '';
$qtys_str = isset($_GET['qtys']) ? $_GET['qtys'] : '';
$products = [];
$total_product_price = 0;

if (!empty($ids_str)) {
    $id_array = explode(',', preg_replace('/[^0-9,]/', '', $ids_str));
    $qty_array = explode(',', preg_replace('/[^0-9,]/', '', $qtys_str));
    $id_to_qty = array_combine($id_array, $qty_array);
    $result = $conn->query("SELECT * FROM products WHERE id IN (" . implode(',', $id_array) . ")");
    while($row = $result->fetch_assoc()) {
        $row['buy_qty'] = $id_to_qty[$row['id']];
        $products[] = $row;
        $total_product_price += ($row['price'] * $row['buy_qty']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | YONEX Official</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --yonex-blue: #003366; --bg-light: #f4f6f8; }
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; padding-bottom: 120px; }
        .checkout-box { background: white; padding: 20px; border-radius: 4px; margin-bottom: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .section-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 15px; color: var(--yonex-blue); border-left: 4px solid var(--yonex-blue); padding-left: 10px; }
        .area-option, .payment-option { padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px; cursor: pointer; display: flex; align-items: center; background: white; }
        .final-price { font-family: 'Oswald', sans-serif; color: var(--yonex-blue); font-size: 2.2rem; font-weight: 800; }
        .btn-checkout { background: var(--yonex-blue); color: white; padding: 12px 60px; font-weight: 700; border-radius: 4px; border:none; }
        .addr-label { font-size: 0.7rem; padding: 2px 6px; border: 1px solid var(--yonex-blue); color: var(--yonex-blue); border-radius: 2px; margin-left: 8px; }
        .product-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="checkout-box" data-bs-toggle="modal" data-bs-target="#addressModal" style="cursor:pointer;">
        <div class="section-title">Delivery Address</div>
        <?php if ($selected_addr): ?>
            <strong><?php echo $selected_addr['receiver_name']; ?> | <?php echo $selected_addr['receiver_phone']; ?></strong>
            <span class="addr-label"><?php echo $selected_addr['label']; ?></span><br>
            <span class="text-muted small"><?php echo $selected_addr['full_address']; ?>, <?php echo $selected_addr['city_state']; ?>, <?php echo $selected_addr['postcode']; ?></span>
        <?php else: ?>
            <span class="text-primary">+ Add New Address</span>
        <?php endif; ?>
    </div>

    <div class="checkout-box" style="border: 2px solid var(--yonex-blue); background: #fffbe6;">
        <div class="section-title">Shipping Area</div>
        <div class="area-option w-100">
            <input type="radio" name="area" value="West" checked onclick="updateTotal(10, 'West Malaysia')">
            <span class="ms-2 fw-bold">West Malaysia (Fixed: RM 10.00)</span>
        </div>
    </div>

    <div class="checkout-box">
        <div class="section-title">Order Details</div>
        <?php foreach ($products as $p): ?>
            <div class="d-flex justify-content-between product-row">
                <span><?php echo htmlspecialchars($p['name']); ?> <small class="text-muted">x<?php echo $p['buy_qty']; ?></small></span>
                <strong>RM <?php echo number_format($p['price'] * $p['buy_qty'], 2); ?></strong>
            </div>
        <?php endforeach; ?>
        <span id="uPrice" style="display:none"><?php echo $total_product_price; ?></span>
        <div class="d-flex justify-content-between mt-3">
            <span>Shipping Fee (<span id="aTag">West Malaysia</span>)</span>
            <span id="sFee">RM 10.00</span>
        </div>
    </div>

    <div class="checkout-box">
        <div class="section-title">Payment Method</div>
        <label class="payment-option w-100">
            <input type="radio" name="payment_method" value="TNG" checked>
            <span class="ms-2">Touch 'n Go eWallet</span>
        </label>
        <label class="payment-option w-100">
            <input type="radio" name="payment_method" value="FPX">
            <span class="ms-2">Online Banking (FPX)</span>
        </label>
    </div>

    <div class="checkout-box">
        <div class="section-title">Payment Summary</div>
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Total Payment</span>
            <span class="final-price" id="tDisplay">RM <?php echo number_format($total_product_price + 10, 2); ?></span>
        </div>
    </div>
</div>

<div class="fixed-bottom bg-white border-top py-3">
    <div class="container d-flex justify-content-end align-items-center">
        <div class="me-4 text-end">
            <div class="small text-muted">Total Payment</div>
            <div class="fw-bold text-primary h4 mb-0" id="fPrice">RM <?php echo number_format($total_product_price + 10, 2); ?></div>
        </div>
        <button class="btn-checkout" onclick="confirmOrder()">PLACE ORDER</button>
    </div>
</div>

<script>
    window.onload = function() { updateTotal(10, 'West Malaysia'); };
    function updateTotal(s, l) {
        const p = parseFloat(document.getElementById('uPrice').innerText) || 0;
        document.getElementById('aTag').innerText = l;
        document.getElementById('sFee').innerText = 'RM ' + s.toFixed(2);
        const t = p + s;
        document.getElementById('tDisplay').innerText = 'RM ' + t.toFixed(2);
        document.getElementById('fPrice').innerText = 'RM ' + t.toFixed(2);
    }
    function confirmOrder() {
        const payMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const total = document.getElementById('tDisplay').innerText.replace('RM ', '');
        const targetPage = (payMethod === 'TNG' ? 'tng_payment.php' : 'fpx_payment.php');
        window.location.href = targetPage + '?total=' + total + '&ids=<?php echo $ids_str; ?>&qtys=<?php echo $qtys_str; ?>';
    }
</script>
</body>
</html>
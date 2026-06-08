<?php
session_start();
require_once 'db_config.php';

// --- 1. 处理删除地址 (保持参数) ---
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM addresses WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    
    $current_ids = isset($_GET['ids']) ? $_GET['ids'] : '';
    $current_qtys = isset($_GET['qtys']) ? $_GET['qtys'] : '';
    header("Location: check_out.php?ids=" . $current_ids . "&qtys=" . $current_qtys);
    exit();
}

// --- 2. 处理新增地址 ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_new_addr'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $full_addr = $_POST['full_address']; 
    $postcode = $_POST['postcode'];
    $city = $_POST['city_state'];
    $label = $_POST['label'];

    $stmt = $conn->prepare("INSERT INTO addresses (receiver_name, receiver_phone, full_address, postcode, city_state, label) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $phone, $full_addr, $postcode, $city, $label);
    $stmt->execute();

    $current_ids = isset($_GET['ids']) ? $_GET['ids'] : '';
    $current_qtys = isset($_GET['qtys']) ? $_GET['qtys'] : '';
    header("Location: check_out.php?ids=" . $current_ids . "&qtys=" . $current_qtys);
    exit();
}

// --- 3. 获取地址数据 ---
$address_result = mysqli_query($conn, "SELECT * FROM addresses ORDER BY id DESC");
$all_addresses = mysqli_fetch_all($address_result, MYSQLI_ASSOC);

// --- 4. 获取商品数据并计算总价 ---
$ids_str = isset($_GET['ids']) ? $_GET['ids'] : '';
$qtys_str = isset($_GET['qtys']) ? $_GET['qtys'] : '';

$products = [];
$total_product_price = 0;

if (!empty($ids_str)) {
    $id_array = explode(',', preg_replace('/[^0-9,]/', '', $ids_str));
    $qty_array = explode(',', preg_replace('/[^0-9,]/', '', $qtys_str));
    $id_to_qty = array_combine($id_array, $qty_array);

    if (!empty($id_array)) {
        $clean_ids = implode(',', $id_array);
        $sql = "SELECT * FROM products WHERE id IN ($clean_ids)";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $current_qty = isset($id_to_qty[$row['id']]) ? (int)$id_to_qty[$row['id']] : 1;
                $row['buy_qty'] = $current_qty;
                $products[] = $row;
                $total_product_price += ($row['price'] * $current_qty);
            }
        }
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
        .area-option, .payment-option { padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px; cursor: pointer; display: flex; align-items: center; background: white; transition: 0.2s; }
        .payment-option:hover { border-color: var(--yonex-blue); }
        .final-price { font-family: 'Oswald', sans-serif; color: var(--yonex-blue); font-size: 2.2rem; font-weight: 800; }
        .btn-checkout { background: var(--yonex-blue); color: white; padding: 12px 60px; font-weight: 700; border-radius: 4px; border:none; }
        .addr-label { font-size: 0.7rem; padding: 2px 6px; border: 1px solid var(--yonex-blue); color: var(--yonex-blue); border-radius: 2px; margin-left: 8px; }
        .product-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .shopee-input { background-color: #f5f5f5 !important; border: none !important; border-radius: 4px !important; padding: 12px !important; }
        .disabled-option { opacity: 0.5; pointer-events: none; background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="checkout-box" data-bs-toggle="modal" data-bs-target="#addressModal" style="cursor:pointer;">
        <div class="section-title">Delivery Address</div>
        <div id="selectedAddrDisplay" 
             data-city="<?php echo !empty($all_addresses) ? htmlspecialchars($all_addresses[0]['city_state']) : ''; ?>"
             data-postcode="<?php echo !empty($all_addresses) ? htmlspecialchars($all_addresses[0]['postcode']) : '0'; ?>">
            <?php if (!empty($all_addresses)): $d = $all_addresses[0]; ?>
                <strong><?php echo $d['receiver_name']; ?> | <?php echo $d['receiver_phone']; ?></strong>
                <span class="addr-label"><?php echo $d['label']; ?></span><br>
                <span class="text-muted small"><?php echo $d['full_address']; ?>, <?php echo $d['city_state']; ?>, <?php echo $d['postcode']; ?></span>
            <?php else: ?>
                <span class="text-primary">+ Add New Address</span>
            <?php endif; ?>
        </div>
    </div>

    <div id="shippingSection" class="checkout-box" style="border: 2px solid var(--yonex-blue); background: #fffbe6;">
        <div class="section-title">Select Shipping Area <span id="areaError" class="text-danger small" style="display:none;">(Please select an area)</span></div>
        <div class="row">
            <div class="col-md-4"><label class="area-option w-100"><input type="radio" name="area" value="Melaka" onclick="updateTotal(0, 'Melaka')"><span class="ms-2">Melaka (FREE)</span></label></div>
            <div class="col-md-4"><label class="area-option w-100"><input type="radio" name="area" value="West" onclick="updateTotal(10, 'West Malaysia')"><span class="ms-2">West MY (RM 10)</span></label></div>
            <div class="col-md-4"><label class="area-option w-100"><input type="radio" name="area" value="East" onclick="updateTotal(20, 'East Malaysia')"><span class="ms-2">East MY (RM 20)</span></label></div>
        </div>
    </div>

    <div class="checkout-box">
        <div class="section-title">Order Details</div>
        <?php foreach ($products as $p): ?>
            <div class="d-flex justify-content-between product-row">
                <span><img src="images/<?php echo $p['id']; ?>.jpg" width="40" class="me-2"><?php echo htmlspecialchars($p['name']); ?> <small class="text-muted">x<?php echo $p['buy_qty']; ?></small></span>
                <strong>RM <?php echo number_format($p['price'] * $p['buy_qty'], 2); ?></strong>
            </div>
        <?php endforeach; ?>
        <span id="uPrice" style="display:none"><?php echo $total_product_price; ?></span>
        <div class="d-flex justify-content-between mt-3">
            <span>Shipping Fee (<span id="aTag">Not Selected</span>)</span>
            <span id="sFee">RM 0.00</span>
        </div>
    </div>

    <div class="checkout-box">
        <div class="section-title">Payment Method</div>
        <div class="payment-options-list">
            <label class="payment-option w-100"><input type="radio" name="payment_method" value="TNG" checked><span class="ms-2">Touch 'n Go eWallet</span></label>
            <label class="payment-option w-100"><input type="radio" name="payment_method" value="FPX"><span class="ms-2">Online Banking (FPX)</span></label>
        </div>
    </div>

    <div class="checkout-box">
        <div class="section-title">Payment Summary</div>
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Total Payment</span>
            <span class="final-price" id="tDisplay">RM <?php echo number_format($total_product_price, 2); ?></span>
        </div>
    </div>
</div>

<div class="fixed-bottom bg-white border-top py-3">
    <div class="container d-flex justify-content-end align-items-center">
        <div class="me-4 text-end">
            <div class="small text-muted">Total Payment</div>
            <div class="fw-bold text-primary h4 mb-0" id="fPrice">RM <?php echo number_format($total_product_price, 2); ?></div>
        </div>
        <button class="btn-checkout" onclick="confirmOrder()">PLACE ORDER</button>
    </div>
</div>

<script>
    window.onload = function() {
        const addrDiv = document.getElementById('selectedAddrDisplay');
        const postcode = parseInt(addrDiv.getAttribute('data-postcode')) || 0;
        const city = addrDiv.getAttribute('data-city').toLowerCase();
        const radios = document.getElementsByName('area');
        let firstAvailable = null;

        radios.forEach(radio => {
            const val = radio.value;
            let disable = false;
            // 逻辑: 80000+ 为东马
            const isEast = (postcode >= 80000);
            const isMelaka = city.includes('melaka');

            if (isEast) { if (val !== 'East') disable = true; } 
            else if (isMelaka) { if (val !== 'Melaka') disable = true; } 
            else { if (val !== 'West') disable = true; }

            if (disable) {
                radio.parentElement.classList.add('disabled-option');
                radio.disabled = true;
            } else if (!firstAvailable) {
                firstAvailable = radio;
            }
        });
        if (firstAvailable) firstAvailable.click();
    };

    let isAreaSelected = false;
    function updateTotal(s, l) {
        isAreaSelected = true;
        document.getElementById('areaError').style.display = 'none';
        const p = parseFloat(document.getElementById('uPrice').innerText) || 0;
        document.getElementById('aTag').innerText = l;
        document.getElementById('sFee').innerText = 'RM ' + s.toFixed(2);
        const t = p + s;
        document.getElementById('tDisplay').innerText = 'RM ' + t.toFixed(2);
        if(document.getElementById('fPrice')) document.getElementById('fPrice').innerText = 'RM ' + t.toFixed(2);
    }

    function confirmOrder() {
        if (!isAreaSelected) {
            alert("Please select a Shipping Area!");
            document.getElementById('shippingSection').scrollIntoView({ behavior: 'smooth' });
            return;
        }
        const payMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const total = document.getElementById('tDisplay').innerText.replace('RM ', '');
        window.location.href = (payMethod === 'TNG' ? 'tng_payment.php' : 'fpx_payment.php') + 
                               '?total=' + total + '&ids=<?php echo $ids_str; ?>&qtys=<?php echo $qtys_str; ?>';
    }
</script>
</body>
</html>
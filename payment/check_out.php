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
        :root { 
            --yonex-blue: #003366; 
            --bg-light: #f4f6f8; 
        }
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; padding-bottom: 120px; }
        .checkout-box { background: white; padding: 20px; border-radius: 4px; margin-bottom: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .section-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 15px; color: var(--yonex-blue); border-left: 4px solid var(--yonex-blue); padding-left: 10px; }
        .area-option, .payment-option { padding: 12px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px; cursor: pointer; display: flex; align-items: center; background: white; transition: 0.2s; }
        .payment-option:hover { border-color: var(--yonex-blue); }
        .final-price { font-family: 'Oswald', sans-serif; color: var(--yonex-blue); font-size: 2.2rem; font-weight: 800; }
        .btn-checkout { background: var(--yonex-blue); color: white; padding: 12px 60px; font-weight: 700; border-radius: 4px; border:none; }
        
        .addr-label { font-size: 0.7rem; padding: 2px 6px; border: 1px solid var(--yonex-blue); color: var(--yonex-blue); border-radius: 2px; margin-left: 8px; }
        .btn-add-new { color: var(--yonex-blue); border: 1px solid var(--yonex-blue); background: white; padding: 10px; width: 100%; border-radius: 4px; font-weight: bold; margin-top: 10px; }
        .btn-add-new:hover { background: #f0f4f8; }

        .product-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .shopee-input { background-color: #f5f5f5 !important; border: none !important; border-radius: 4px !important; padding: 12px !important; }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="checkout-box" data-bs-toggle="modal" data-bs-target="#addressModal" style="cursor:pointer;">
        <div class="section-title">Delivery Address</div>
        <div id="selectedAddrDisplay">
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
            <div class="col-md-4"><label class="area-option w-100"><input type="radio" name="area" onclick="updateTotal(0, 'Melaka')"><span class="ms-2">Melaka (FREE)</span></label></div>
            <div class="col-md-4"><label class="area-option w-100"><input type="radio" name="area" onclick="updateTotal(10, 'West Malaysia')"><span class="ms-2">West MY (RM 10)</span></label></div>
            <div class="col-md-4"><label class="area-option w-100"><input type="radio" name="area" onclick="updateTotal(20, 'East Malaysia')"><span class="ms-2">East MY (RM 20)</span></label></div>
        </div>
    </div>

    <div class="checkout-box">
        <div class="section-title">Order Details</div>
        <?php foreach ($products as $p): ?>
            <div class="d-flex justify-content-between product-row">
                <span>
                    <img src="images/<?php echo $p['id']; ?>.jpg" width="40" class="me-2">
                    <?php echo htmlspecialchars($p['name']); ?> <small class="text-muted">x<?php echo $p['buy_qty']; ?></small>
                </span>
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
            <label class="payment-option w-100"><input type="radio" name="payment_method" value="CARD"><span class="ms-2">Credit Card / Debit Card</span></label>
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

<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Address Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="addressList">
                    <?php if (empty($all_addresses)): ?>
                        <p class="text-center text-muted">No address found.</p>
                    <?php else: ?>
                        <?php foreach ($all_addresses as $addr): ?>
                        <div class="border p-3 rounded mb-2 position-relative">
                            <strong><?php echo $addr['receiver_name']; ?> | <?php echo $addr['receiver_phone']; ?></strong>
                            <span class="addr-label"><?php echo $addr['label']; ?></span><br>
                            <small class="text-muted"><?php echo $addr['full_address']; ?></small>
                            <div class="mt-2 text-end">
                                <a href="?delete_id=<?php echo $addr['id']; ?>&ids=<?php echo $ids_str; ?>&qtys=<?php echo $qtys_str; ?>" 
                                   class="text-danger small text-decoration-none" 
                                   onclick="return confirm('Delete this address?')">Delete</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <button type="button" class="btn-add-new" onclick="toggleAddressForm()">+ Add New Address</button>
                </div>

                <div id="newAddressForm" style="display: none;">
                    <hr>
                    <h6>Add New Address</h6>
                    <form method="POST">
                        <input type="hidden" name="add_new_addr" value="1">
                        <input type="text" name="name" class="form-control shopee-input mb-2" placeholder="Full Name" required>
                        <input type="text" name="phone" class="form-control shopee-input mb-2" placeholder="Phone Number" required>
                        <textarea name="full_address" class="form-control shopee-input mb-2" placeholder="Address Details" required></textarea>
                        <div class="row">
                            <div class="col"><input type="text" name="postcode" class="form-control shopee-input mb-2" placeholder="Postcode" required></div>
                            <div class="col"><input type="text" name="city_state" class="form-control shopee-input mb-2" placeholder="City, State" required></div>
                        </div>
                        <select name="label" class="form-select shopee-input mb-3">
                            <option value="Home">Home</option>
                            <option value="Work">Work</option>
                        </select>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light w-50" onclick="toggleAddressForm()">Back</button>
                            <button type="submit" class="btn btn-primary w-50" style="background:var(--yonex-blue); border:none;">Save Address</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const addrModalEl = document.getElementById('addressModal');
    addrModalEl.addEventListener('hidden.bs.modal', function () {
        document.getElementById('addressList').style.display = 'block';
        document.getElementById('newAddressForm').style.display = 'none';
    });

    function toggleAddressForm() {
        const list = document.getElementById('addressList');
        const form = document.getElementById('newAddressForm');
        if (list.style.display === 'none') {
            list.style.display = 'block';
            form.style.display = 'none';
        } else {
            list.style.display = 'none';
            form.style.display = 'block';
        }
    }

    let isAreaSelected = false;
    function updateTotal(s, l) {
        isAreaSelected = true;
        document.getElementById('areaError').style.display = 'none';
        const p = parseFloat(document.getElementById('uPrice').innerText) || 0;
        document.getElementById('aTag').innerText = l;
        document.getElementById('sFee').innerText = 'RM ' + s.toFixed(2);
        const t = p + s;
        document.getElementById('tDisplay').innerText = 'RM ' + t.toFixed(2);
        document.getElementById('fPrice').innerText = 'RM ' + t.toFixed(2);
    }

    function confirmOrder() {
        if (!isAreaSelected) {
            alert("Please select a Shipping Area!");
            document.getElementById('areaError').style.display = 'inline';
            document.getElementById('shippingSection').scrollIntoView({ behavior: 'smooth' });
            return;
        }
        const payMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const total = document.getElementById('fPrice').innerText.replace('RM ', '');
        const ids = "<?php echo $ids_str; ?>";
        const qtys = "<?php echo $qtys_str; ?>";

        if (payMethod === 'TNG') {
            window.location.href = 'tng_payment.php?total=' + total + '&ids=' + ids + '&qtys=' + qtys;
        } else if (payMethod === 'FPX') {
            // 已添加 FPX 跳转逻辑
            window.location.href = 'fpx_payment.php?total=' + total + '&ids=' + ids + '&qtys=' + qtys;
        } else {
            alert("Order Success! Method: " + payMethod + " Amount: RM " + total);
        }
    }
</script>

</body>
</html>
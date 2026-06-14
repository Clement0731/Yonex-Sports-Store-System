<?php
session_start();
require_once 'db_config.php';

// 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_register/login_page.php");
    exit();
}
$user_id = $_SESSION['user_id']; 

// --- 1. 处理删除地址 ---
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $del_id, $user_id);
    $stmt->execute();
    header("Location: check_out.php?ids=" . $_GET['ids'] . "&qtys=" . $_GET['qtys']);
    exit();
}

// --- 2. 处理新增地址 ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_new_addr'])) {
    $stmt = $conn->prepare("INSERT INTO addresses (user_id, receiver_name, receiver_phone, full_address, postcode, city_state, label) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $_POST['name'], $_POST['phone'], $_POST['full_address'], $_POST['postcode'], $_POST['city_state'], $_POST['label']);
    $stmt->execute();
    header("Location: check_out.php?ids=" . $_GET['ids'] . "&qtys=" . $_GET['qtys']);
    exit();
}

// --- 3. 获取当前用户的地址列表 ---
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address_result = $stmt->get_result();
$all_addresses = mysqli_fetch_all($address_result, MYSQLI_ASSOC);

// --- 4. 获取订单金额 (简化逻辑，保留你的核心计算) ---
$ids_str = isset($_GET['ids']) ? $_GET['ids'] : '';
$qtys_str = isset($_GET['qtys']) ? $_GET['qtys'] : '';

$total_product_price = 0;
if (!empty($ids_str)) {
    $cart_ids = explode(',', $ids_str);
    foreach ($cart_ids as $c_id) {
        $c_id = intval($c_id);
        $cart_query = "SELECT c.quantity, p.price FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.id = $c_id";
        $cart_res = mysqli_query($conn, $cart_query);
        if ($cart_row = mysqli_fetch_assoc($cart_res)) {
            $total_product_price += $cart_row['price'] * $cart_row['quantity'];
        }
    }
}
$shipping_fee = 10.00;
$grand_total = $total_product_price + $shipping_fee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Checkout | Yonex</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary-blue: #003366;
        --primary-gold: #FFD700;
        --bg-gray: #f4f7f9;
        --card-shadow: 0 4px 15px rgba(0,0,0,0.04);
    }
    body { background-color: var(--bg-gray); font-family: 'Inter', sans-serif; color: #333; }
    
    .checkout-wrap { max-width: 900px; margin: 40px auto; }
    .section-card { background: #fff; border-radius: 16px; padding: 30px; box-shadow: var(--card-shadow); margin-bottom: 25px; border: 1px solid #eef2f6; }
    
    /* === 顶级地址卡片 UI === */
    .address-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
    
    .addr-card {
        border: 2px solid #eef2f6;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
        background: #fff;
    }
    
    .addr-card:hover { border-color: #b3cce6; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,51,102,0.05); }
    
    /* 选中状态 */
    .addr-card.selected {
        border-color: var(--primary-blue);
        background: #f8fbff;
    }
    /* 右上角打勾动画 */
    .addr-card.selected::before {
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        color: var(--primary-blue);
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 1.4rem;
        animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .addr-name { font-weight: 700; font-size: 1.05rem; color: #1a1a1a; margin-bottom: 5px; }
    .addr-phone { color: #6c757d; font-weight: 500; font-size: 0.9rem; }
    .addr-detail { font-size: 0.9rem; color: #555; line-height: 1.5; margin: 10px 0; }
    
    .btn-delete { 
        position: absolute; bottom: 15px; right: 15px; 
        color: #dc3545; font-size: 0.85rem; font-weight: 600; text-decoration: none; 
        padding: 5px 10px; border-radius: 6px; transition: 0.2s;
    }
    .btn-delete:hover { background: #fee2e2; }

    /* === 空状态引导 (Empty State) === */
    .empty-address {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: 0.3s;
    }
    .empty-address:hover { border-color: var(--primary-blue); background: #f0f7ff; }

    /* 支付与结算按钮 */
    .payment-option { border: 1px solid #dee2e6; border-radius: 10px; transition: 0.2s; cursor: pointer; }
    .payment-option:hover { background: #f8f9fa; border-color: var(--primary-blue); }
    
    .btn-place-order { 
        background: var(--primary-blue); color: #fff; border: none; 
        padding: 16px 30px; border-radius: 10px; font-weight: 700; font-size: 1.1rem; 
        letter-spacing: 1px; width: 100%; transition: 0.3s;
    }
    .btn-place-order:hover { background: #001f3f; color: var(--primary-gold); transform: translateY(-2px); }
    .btn-place-order:disabled { background: #94a3b8; cursor: not-allowed; transform: none; color: white;}
    </style>
</head>
<body>

<div class="checkout-wrap">
    <h2 class="mb-4 fw-bold" style="font-family: 'Oswald'; color: var(--primary-blue);">CHECKOUT</h2>

    <div class="section-card" id="address-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>Delivery Address</h5>
            <?php if (!empty($all_addresses)): ?>
                <button class="btn btn-sm btn-outline-dark fw-medium rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addressModal">
                    <i class="fas fa-plus me-1"></i> Add New
                </button>
            <?php endif; ?>
        </div>

        <?php if (empty($all_addresses)): ?>
            <div class="empty-address" data-bs-toggle="modal" data-bs-target="#addressModal">
                <i class="fas fa-shipping-fast fs-1 mb-3" style="color: #94a3b8;"></i>
                <h6 class="fw-bold text-dark">No Shipping Address Found</h6>
                <p class="text-muted small mb-3">Please add a delivery address to proceed with your order.</p>
                <button class="btn btn-primary rounded-pill px-4" style="background: var(--primary-blue); border: none;">+ Add Delivery Address</button>
            </div>
            <input type="radio" name="selected_address" value="" id="dummy_addr" checked style="display:none;">
        <?php else: ?>
            <div class="address-grid">
                <?php foreach ($all_addresses as $index => $addr): ?>
                    <div class="addr-card <?php echo $index === 0 ? 'selected' : ''; ?>" onclick="selectAddress(this)">
                        <input type="radio" name="selected_address" value="<?php echo $addr['id']; ?>" <?php echo $index === 0 ? 'checked' : ''; ?> style="display:none;">
                        
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="addr-name"><?php echo htmlspecialchars($addr['receiver_name']); ?></span>
                                <span class="addr-phone ms-2"><?php echo htmlspecialchars($addr['receiver_phone']); ?></span>
                            </div>
                            <span class="badge bg-secondary rounded-pill px-2" style="font-size: 0.7rem;"><?php echo htmlspecialchars($addr['label'] ?? 'Home'); ?></span>
                        </div>
                        
                        <div class="addr-detail">
                            <?php echo htmlspecialchars($addr['full_address']); ?><br>
                            <?php echo htmlspecialchars($addr['postcode']); ?>, <?php echo htmlspecialchars($addr['city_state'] ?? ''); ?>
                        </div>
                        
                        <a href="check_out.php?ids=<?php echo $ids_str; ?>&qtys=<?php echo $qtys_str; ?>&delete_id=<?php echo $addr['id']; ?>" class="btn-delete" onclick="event.stopPropagation(); return confirm('Delete this address?');">
                            <i class="fas fa-trash-alt me-1"></i> Remove
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="section-card">
        <h5 class="fw-bold mb-4"><i class="fas fa-wallet text-primary me-2"></i>Payment Method</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="payment-option d-block p-3">
                    <input class="form-check-input ms-1 mt-1 me-2" type="radio" name="payment_method" value="fpx" checked>
                    <span class="fw-medium"><i class="fas fa-university text-info me-2"></i> FPX Online Banking</span>
                </label>
            </div>
            <div class="col-md-6">
                <label class="payment-option d-block p-3">
                    <input class="form-check-input ms-1 mt-1 me-2" type="radio" name="payment_method" value="tng" checked>
                    <span class="fw-medium"><i class="fas fa-mobile-alt text-primary me-2"></i> Touch 'n Go eWallet</span>
                </label>
            </div>
        </div>
    </div>

    <div class="section-card">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="text-muted small fw-medium text-uppercase tracking-wider">Subtotal: RM <?php echo number_format($total_product_price, 2); ?> | Shipping: RM 10.00</div>
                <div class="d-flex align-items-end mt-1">
                    <span class="fs-6 fw-bold me-2">Total:</span>
                    <span class="fw-bold" style="font-family: 'Oswald'; font-size: 2rem; color: var(--primary-blue); line-height: 1;">RM <?php echo number_format($grand_total, 2); ?></span>
                    <span id="cleanTotal" style="display:none;"><?php echo $grand_total; ?></span>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn-place-order" onclick="processCheckout()">PLACE ORDER NOW</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addressModal" tabindex="-1">
  <div class="modal-content modal-dialog modal-dialog-centered border-0 rounded-4 shadow-lg">
    <form method="POST" action="check_out.php?ids=<?php echo $ids_str; ?>&qtys=<?php echo $qtys_str; ?>">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold fs-4">New Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="add_new_addr" value="1">
        
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">Receiver Name</label>
                <input type="text" name="name" class="form-control form-control-lg bg-light" placeholder="e.g. Ali bin Abu" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">Phone Number</label>
                <input type="tel" name="phone" class="form-control form-control-lg bg-light" placeholder="01x-xxxxxxx" required>
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold text-muted">Detailed Address</label>
                <textarea name="full_address" class="form-control bg-light" rows="3" placeholder="Unit/House No, Building Name, Street..." required></textarea>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted">Postcode</label>
                <input type="text" name="postcode" class="form-control bg-light" required>
            </div>
            <div class="col-md-7">
                <label class="form-label small fw-semibold text-muted">City & State</label>
                <input type="text" name="city_state" class="form-control bg-light" placeholder="e.g. KL, Kuala Lumpur" required>
            </div>
            <div class="col-12 mt-4">
                <label class="form-label small fw-semibold text-muted d-block">Label as</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="label" id="btnradio1" value="Home" checked>
                    <label class="btn btn-outline-secondary" for="btnradio1"><i class="fas fa-home me-2"></i>Home</label>

                    <input type="radio" class="btn-check" name="label" id="btnradio2" value="Office">
                    <label class="btn btn-outline-secondary" for="btnradio2"><i class="fas fa-building me-2"></i>Office</label>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
          <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3" style="background: var(--primary-blue); border:none;">SAVE ADDRESS</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // 1. 卡片点击选中特效逻辑
    function selectAddress(element) {
        // 移除所有卡片的 selected 状态
        document.querySelectorAll('.addr-card').forEach(card => card.classList.remove('selected'));
        // 给当前点击的卡片加上 selected
        element.classList.add('selected');
        // 勾选里面隐藏的 radio button
        element.querySelector('input[type="radio"]').checked = true;
    }

    // 2. 结算强制拦截防呆逻辑
    function processCheckout() {
        // 获取隐藏的干净总额 (避免 RM 和 逗号干扰)
        const cleanTotal = document.getElementById('cleanTotal').innerText.trim();
        const ids = "<?php echo $ids_str; ?>";
        const qtys = "<?php echo $qtys_str; ?>";
        
        // 检查地址：如果取到的 value 是空的，说明用的是 dummy_addr（即没有地址）
        const selectedAddr = document.querySelector('input[name="selected_address"]:checked');
        if (!selectedAddr || selectedAddr.value === "") {
            // 界面震动特效 + 警告
            const addrSection = document.getElementById('address-section');
            addrSection.style.borderColor = "#dc3545";
            addrSection.style.boxShadow = "0 0 10px rgba(220,53,69,0.2)";
            
            alert("🛑 ACTION REQUIRED:\nPlease add a shipping address before proceeding to payment.");
            
            // 自动打开填写地址弹窗
            var myModal = new bootstrap.Modal(document.getElementById('addressModal'));
            myModal.show();
            return;
        }
        
        const addrId = selectedAddr.value;
        const payMethod = document.querySelector('input[name="payment_method"]:checked').value;

        // 根据选择跳转支付网关
        if (payMethod === 'fpx') {
            window.location.href = `fpx_payment.php?total=${cleanTotal}&ids=${ids}&qtys=${qtys}&addr_id=${addrId}`;
        } else if (payMethod === 'tng') {
            window.location.href = `tng_payment.php?total=${cleanTotal}&ids=${ids}&qtys=${qtys}&addr_id=${addrId}`;
        }
    }
</script>
</body>
</html>
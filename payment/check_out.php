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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_type']) && $_POST['action_type'] == 'add_new_addr') {
    $unit = trim($_POST['unit_no']);
    $building = trim($_POST['building_name']);
    $street = trim($_POST['street_name']);
    $full_address = "$unit, $building, $street";
    $city_state = trim($_POST['city']) . ", " . trim($_POST['state']);

    $stmt = $conn->prepare("INSERT INTO addresses (user_id, receiver_name, receiver_phone, full_address, postcode, city_state, label) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $_POST['name'], $_POST['phone'], $full_address, $_POST['postcode'], $city_state, $_POST['label']);
    $stmt->execute();
    header("Location: check_out.php?ids=" . $_GET['ids'] . "&qtys=" . $_GET['qtys']);
    exit();
}

// --- 3. 处理编辑地址 ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_type']) && $_POST['action_type'] == 'edit_addr') {
    $addr_id = intval($_POST['edit_addr_id']);
    $unit = trim($_POST['unit_no']);
    $building = trim($_POST['building_name']);
    $street = trim($_POST['street_name']);
    $full_address = "$unit, $building, $street";
    $city_state = trim($_POST['city']) . ", " . trim($_POST['state']);

    $stmt = $conn->prepare("UPDATE addresses SET receiver_name=?, receiver_phone=?, full_address=?, postcode=?, city_state=?, label=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssssssii", $_POST['name'], $_POST['phone'], $full_address, $_POST['postcode'], $city_state, $_POST['label'], $addr_id, $user_id);
    $stmt->execute();
    header("Location: check_out.php?ids=" . $_GET['ids'] . "&qtys=" . $_GET['qtys']);
    exit();
}

// --- 4. 获取当前用户的地址列表 ---
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address_result = $stmt->get_result();
$all_addresses = mysqli_fetch_all($address_result, MYSQLI_ASSOC);

// --- 5. 获取订单金额 (加强版分类算法) ---
$ids_str = isset($_GET['ids']) ? $_GET['ids'] : '';
$qtys_str = isset($_GET['qtys']) ? $_GET['qtys'] : '';

$base_subtotal = 0;   // 纯原价总计
$services_total = 0;  // 附加服务费总计

if (!empty($ids_str)) {
    $cart_ids = explode(',', $ids_str);
    foreach ($cart_ids as $c_id) {
        $c_id = intval($c_id);
        $cart_query = "SELECT 
            c.quantity, 
            c.custom_name, 
            p.price AS base_price, 
            IFNULL(s1.additional_price, 0) AS string_price,
            IFNULL(s2.additional_price, 0) AS tension_price
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        LEFT JOIN service_options s1 ON c.string_option_id = s1.id
        LEFT JOIN service_options s2 ON c.tension_option_id = s2.id
        WHERE c.id = $c_id";

        $cart_res = mysqli_query($conn, $cart_query);
        if ($cart_res && $cart_row = mysqli_fetch_assoc($cart_res)) {
            $qty = $cart_row['quantity'];
            
            // 1. 累计：商品原价
            $base_subtotal += $cart_row['base_price'] * $qty;
            
            // 2. 累计：附加服务费 (球线 + 磅数)
            $item_service_fee = $cart_row['string_price'] + $cart_row['tension_price'];
            // 衣服印字附加费
            if (!empty($cart_row['custom_name'])) {
                $item_service_fee += 15;
            }
            $services_total += $item_service_fee * $qty;
        }
    }
}
$shipping_fee = 10.00;
// 原价 + 服务费 + 运费 = 最终总额
$grand_total = $base_subtotal + $services_total + $shipping_fee;
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
    
    /* === 地址卡片 UI === */
    .address-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
    
    .addr-card { border: 2px solid #eef2f6; border-radius: 12px; padding: 20px 20px 15px; cursor: pointer; position: relative; transition: all 0.2s ease; background: #fff; display: flex; flex-direction: column; justify-content: space-between; }
    .addr-card:hover { border-color: #b3cce6; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,51,102,0.05); }
    .addr-card.selected { border-color: var(--primary-blue); background: #f8fbff; }
    .addr-card.selected::before { content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900; color: var(--primary-blue); position: absolute; top: 15px; right: 20px; font-size: 1.4rem; }

    .addr-name { font-weight: 700; font-size: 1.1rem; color: #1a1a1a; }
    .addr-phone { color: #6c757d; font-weight: 500; font-size: 0.95rem; }
    .addr-label { font-size: 0.7rem; letter-spacing: 0.5px; text-transform: uppercase; }
    .addr-detail { font-size: 0.9rem; color: #555; line-height: 1.6; margin: 15px 0; }
    
    .addr-actions { border-top: 1px dashed #e2e8f0; padding-top: 12px; display: flex; justify-content: flex-end; gap: 15px; }
    .action-link { font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.2s; position: relative; z-index: 2;}
    .action-link.edit { color: var(--primary-blue); }
    .action-link.edit:hover { color: #00509e; text-decoration: underline; }
    .action-link.delete { color: #dc3545; }
    .action-link.delete:hover { color: #a71d2a; text-decoration: underline; }

    .empty-address { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 40px 20px; text-align: center; background: #f8fafc; cursor: pointer; transition: 0.3s; }
    .empty-address:hover { border-color: var(--primary-blue); background: #f0f7ff; }
    
    .payment-option { border: 1px solid #dee2e6; border-radius: 10px; transition: 0.2s; cursor: pointer; }
    .payment-option:hover { background: #f8f9fa; border-color: var(--primary-blue); }
    
    .btn-place-order { background: var(--primary-blue); color: #fff; border: none; padding: 16px 30px; border-radius: 10px; font-weight: 700; font-size: 1.1rem; width: 100%; transition: 0.3s; }
    .btn-place-order:hover { background: #001f3f; color: var(--primary-gold); transform: translateY(-2px); }

    .modal-body .form-control, .modal-body .form-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem; background-color: #f8fafc; transition: all 0.2s ease-in-out; }
    .modal-body .form-control:focus, .modal-body .form-select:focus { background-color: #ffffff; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1); }
    .btn-light-custom { 
        background: #fff; border: 2px solid #eef2f6; color: var(--primary-blue); 
        font-weight: 700; transition: 0.3s; display: inline-flex; align-items: center;
    }
    .btn-light-custom:hover {
        background-color: var(--primary-blue) !important;
        color: white !important;
        border-color: var(--primary-blue) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,51,102,0.15);
    }
    </style>
</head>
<body>

<div style="max-width: 900px; margin: 30px auto 0;">
    <a href="shopping_cart.php" class="btn btn-light-custom rounded-pill px-4 py-2 text-decoration-none">
        <i class="fas fa-arrow-left me-2"></i>Back to Cart
    </a>
</div>

<div class="checkout-wrap">
    <h2 class="mb-4 fw-bold" style="font-family: 'Oswald'; color: var(--primary-blue);">CHECKOUT</h2>

    <div class="section-card" id="address-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>Delivery Address</h5>
            <button class="btn btn-sm btn-outline-dark fw-medium rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="prepareAddModal()">
                <i class="fas fa-plus me-1"></i> Add New
            </button>
        </div>

        <?php if (empty($all_addresses)): ?>
            <div class="empty-address" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="prepareAddModal()">
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
                        
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="addr-name"><?php echo htmlspecialchars($addr['receiver_name']); ?></span>
                                <span class="addr-phone ms-3"><?php echo htmlspecialchars($addr['receiver_phone']); ?></span>
                            </div>
                            <span class="badge bg-secondary rounded-pill px-2 py-1 addr-label"><?php echo htmlspecialchars($addr['label'] ?? 'Home'); ?></span>
                            
                            <div class="addr-detail">
                                <?php echo htmlspecialchars($addr['full_address']); ?><br>
                                <?php echo htmlspecialchars($addr['postcode']); ?>, <?php echo htmlspecialchars($addr['city_state'] ?? ''); ?>
                            </div>
                        </div>
                        
                        <div class="addr-actions">
                            <a href="javascript:void(0)" class="action-link edit" onclick="prepareEditModal(
                                <?php echo $addr['id']; ?>, 
                                '<?php echo addslashes($addr['receiver_name']); ?>', 
                                '<?php echo addslashes($addr['receiver_phone']); ?>', 
                                '<?php echo addslashes($addr['full_address']); ?>', 
                                '<?php echo addslashes($addr['city_state'] ?? ''); ?>', 
                                '<?php echo addslashes($addr['postcode']); ?>', 
                                '<?php echo addslashes($addr['label'] ?? 'Home'); ?>'
                            )">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="check_out.php?ids=<?php echo $ids_str; ?>&qtys=<?php echo $qtys_str; ?>&delete_id=<?php echo $addr['id']; ?>" class="action-link delete" onclick="event.stopPropagation(); return confirm('Delete this address?');">
                                <i class="fas fa-trash-alt me-1"></i> Remove
                            </a>
                        </div>
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
                    <input class="form-check-input ms-1 mt-1 me-2" type="radio" name="payment_method" value="tng">
                    <span class="fw-medium"><i class="fas fa-mobile-alt text-primary me-2"></i> Touch 'n Go eWallet</span>
                </label>
            </div>
        </div>
    </div>

    <div class="section-card" style="background: #f8fafc; border: 2px solid #eef2f6;">
        <div class="row align-items-center">
            <div class="col-md-7 mb-4 mb-md-0">
                <h6 class="fw-bold mb-3" style="color: var(--primary-blue);">ORDER SUMMARY</h6>
                <div class="d-flex justify-content-between mb-2 small text-muted fw-bold text-uppercase">
                    <span>Products Base Price:</span>
                    <span class="text-dark">RM <?php echo number_format($base_subtotal, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-muted fw-bold text-uppercase">
                    <span>Add-on Services (String / Print):</span>
                    <span class="text-success">+ RM <?php echo number_format($services_total, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 small text-muted fw-bold text-uppercase">
                    <span>Shipping Fee:</span>
                    <span class="text-danger">+ RM <?php echo number_format($shipping_fee, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-end border-top pt-3 mt-1">
                    <span class="fs-6 fw-bold me-2" style="color: var(--primary-blue);">GRAND TOTAL:</span>
                    <span class="fw-bold" style="font-family: 'Oswald'; font-size: 2.2rem; color: var(--primary-blue); line-height: 1;">RM <?php echo number_format($grand_total, 2); ?></span>
                </div>
            </div>
            <div class="col-md-5 text-md-end d-flex align-items-end justify-content-end">
                <button class="btn-place-order" id="btn-place-order" style="height: 60px;">PLACE ORDER NOW</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addressModal" tabindex="-1">
  <div class="modal-content modal-dialog modal-dialog-centered border-0 rounded-4 shadow-lg">
    <form method="POST" action="check_out.php?ids=<?php echo $ids_str; ?>&qtys=<?php echo $qtys_str; ?>" id="addressForm">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold fs-4" id="modalTitle">New Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action_type" id="action_type" value="add_new_addr">
        <input type="hidden" name="edit_addr_id" id="edit_addr_id" value="">
        
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">Receiver Name</label>
                <input type="text" name="name" id="receiver_name" class="form-control bg-light" placeholder="e.g. Ali bin Abu" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">Phone Number</label>
                <input type="tel" name="phone" id="phone_input" class="form-control bg-light" placeholder="01X XXXXXXX" required pattern="01\d \d{7,8}" title="Format: 01X XXXXXXX">
            </div>

            <div class="col-12">
                <label class="form-label small fw-semibold text-muted">Detailed Address</label>
                <input type="text" name="unit_no" id="unit_no" class="form-control bg-light mb-2" placeholder="Unit / House No." required>
                <input type="text" name="building_name" id="building_name" class="form-control bg-light mb-2" placeholder="Building Name / Taman" required pattern=".*[a-zA-Z].*" title="Must contain at least one letter">
                <input type="text" name="street_name" id="street_name" class="form-control bg-light" placeholder="Street Name" required pattern=".*[a-zA-Z].*" title="Must contain at least one letter">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">State</label>
                <select name="state" id="state_select" class="form-select bg-light" required>
                    <option value="" disabled selected>Select State</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">City</label>
                <select name="city" id="city_select" class="form-select bg-light" required disabled>
                    <option value="" disabled selected>Select City First</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label small fw-semibold text-muted">Postcode <span id="postcode_hint" class="text-danger" style="font-size:0.75rem;"></span></label>
                <input type="text" name="postcode" id="postcode_input" class="form-control bg-light" placeholder="e.g. 80000" required maxlength="5">
            </div>

            <div class="col-12 mt-2">
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
    const malaysiaData = {
        "Johor": { cities: { "Johor Bahru": "80000", "Batu Pahat": "83000", "Kluang": "86000", "Muar": "84000", "Kulai": "81000", "Kota Tinggi": "81900", "Segamat": "85000", "Pontian": "82000" }, prefix: /^(79|80|81|82|83|84|85|86)/, hint: "79000-86999" },
        "Kedah": { cities: { "Alor Setar": "05000", "Sungai Petani": "08000", "Kulim": "09000", "Langkawi": "07000", "Baling": "09100", "Jitra": "06000" }, prefix: /^(05|06|07|08|09)/, hint: "05000-09999" },
        "Kelantan": { cities: { "Kota Bharu": "15000", "Pasir Mas": "17000", "Tanah Merah": "17500", "Gua Musang": "18300", "Machang": "18500" }, prefix: /^(15|16|17|18)/, hint: "15000-18999" },
        "Kuala Lumpur": { cities: { "Kuala Lumpur": "50000", "Cheras": "56000", "Kepong": "52000", "Setapak": "53000", "Wangsa Maju": "53300" }, prefix: /^(50|51|52|53|54|55|56|57|58|59|60)/, hint: "50000-60000" },
        "Melaka": { cities: { "Melaka City": "75000", "Alor Gajah": "78000", "Jasin": "77000", "Ayer Keroh": "75450", "Batu Berendam": "75350" }, prefix: /^(75|76|77|78)/, hint: "75000-78999" },
        "Negeri Sembilan": { cities: { "Seremban": "70000", "Port Dickson": "71000", "Nilai": "71800", "Kuala Pilah": "72000", "Bahau": "72100" }, prefix: /^(70|71|72|73)/, hint: "70000-73999" },
        "Pahang": { cities: { "Kuantan": "25000", "Temerloh": "28000", "Bentong": "28700", "Cameron Highlands": "39000", "Raub": "27600" }, prefix: /^(25|26|27|28|39|49|69)/, hint: "25000-49999" },
        "Penang": { cities: { "George Town": "10000", "Butterworth": "12000", "Bayan Lepas": "11900", "Bukit Mertajam": "14000", "Nibong Tebal": "14300" }, prefix: /^(10|11|12|13|14)/, hint: "10000-14999" },
        "Perak": { cities: { "Ipoh": "30000", "Taiping": "34000", "Sitiawan": "32000", "Teluk Intan": "36000", "Kampar": "31900" }, prefix: /^(30|31|32|33|34|35|36)/, hint: "30000-36999" },
        "Perlis": { cities: { "Kangar": "01000", "Arau": "02600", "Padang Besar": "02100" }, prefix: /^(01|02)/, hint: "01000-02999" },
        "Selangor": { cities: { "Shah Alam": "40000", "Petaling Jaya": "46000", "Subang Jaya": "47500", "Klang": "41000", "Cyberjaya": "63000", "Puchong": "47100", "Sepang": "43900" }, prefix: /^(40|41|42|43|44|45|46|47|48|63|64|65|66|67|68)/, hint: "40000-68999" },
        "Terengganu": { cities: { "Kuala Terengganu": "20000", "Kemaman": "24000", "Dungun": "23000", "Besut": "22200" }, prefix: /^(20|21|22|23|24)/, hint: "20000-24999" },
        "Putrajaya": { cities: { "Putrajaya": "62000" }, prefix: /^(62)/, hint: "62000-62999" }
    };

    const stateSelect = document.getElementById('state_select');
    const citySelect = document.getElementById('city_select');
    const postcodeInput = document.getElementById('postcode_input');
    const postcodeHint = document.getElementById('postcode_hint');

    Object.keys(malaysiaData).forEach(state => {
        stateSelect.add(new Option(state, state));
    });

    stateSelect.addEventListener('change', function() {
        citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';
        citySelect.disabled = false;
        
        const selectedState = this.value;
        const cityData = malaysiaData[selectedState].cities;
        postcodeHint.innerText = `(Valid range: ${malaysiaData[selectedState].hint})`;
        
        Object.keys(cityData).forEach(city => {
            citySelect.add(new Option(city, city));
        });
        
        if(event && event.isTrusted) postcodeInput.value = ''; 
    });

    citySelect.addEventListener('change', function() {
        const state = stateSelect.value;
        const city = this.value;
        if(state && city) {
            postcodeInput.value = malaysiaData[state].cities[city]; 
            validatePostcode();
        }
    });

    postcodeInput.addEventListener('input', validatePostcode);

    function validatePostcode() {
        const state = stateSelect.value;
        let pc = postcodeInput.value.replace(/\D/g, ''); 
        postcodeInput.value = pc; 
        postcodeInput.setCustomValidity("");

        if (state && pc.length === 5) {
            const regex = malaysiaData[state].prefix;
            if (!regex.test(pc)) {
                postcodeInput.setCustomValidity(`Invalid postcode for ${state}. Please use range ${malaysiaData[state].hint}`);
            }
        } else if (pc.length < 5 && pc.length > 0) {
            postcodeInput.setCustomValidity(`Postcode must be 5 digits.`);
        }
    }

    const phoneInput = document.getElementById('phone_input');
    phoneInput.addEventListener('focus', function() { if (this.value === '') this.value = '01'; });
    phoneInput.addEventListener('input', function (e) {
        let val = this.value;
        if (!val.startsWith('01')) val = '01' + val.replace(/^0?1?/, '');
        val = val.replace(/\D/g, ''); 
        if (val.length > 11) val = val.substring(0, 11);
        if (val.length > 3) this.value = val.substring(0, 3) + ' ' + val.substring(3);
        else this.value = val;
    });

    function prepareAddModal() {
        document.getElementById('modalTitle').innerText = 'New Address';
        document.getElementById('action_type').value = 'add_new_addr';
        document.getElementById('edit_addr_id').value = '';
        document.getElementById('addressForm').reset();
        document.getElementById('city_select').innerHTML = '<option value="" disabled selected>Select City First</option>';
        document.getElementById('city_select').disabled = true;
        postcodeHint.innerText = '';
    }

    function prepareEditModal(id, name, phone, fullAddress, cityState, postcode, label) {
        event.stopPropagation(); 
        document.getElementById('modalTitle').innerText = 'Edit Address';
        document.getElementById('action_type').value = 'edit_addr';
        document.getElementById('edit_addr_id').value = id;
        document.getElementById('receiver_name').value = name;
        document.getElementById('phone_input').value = phone;
        document.getElementById('postcode_input').value = postcode;

        let addrParts = fullAddress.split(',').map(s => s.trim());
        document.getElementById('unit_no').value = addrParts[0] || '';
        document.getElementById('building_name').value = addrParts[1] || '';
        document.getElementById('street_name').value = addrParts.slice(2).join(', ') || '';

        if(cityState) {
            let csParts = cityState.split(',').map(s => s.trim());
            let city = csParts[0];
            let state = csParts[1];
            document.getElementById('state_select').value = state;
            document.getElementById('state_select').dispatchEvent(new Event('change'));
            document.getElementById('city_select').value = city;
        }

        if (label === 'Office') document.getElementById('btnradio2').checked = true;
        else document.getElementById('btnradio1').checked = true;

        var myModal = new bootstrap.Modal(document.getElementById('addressModal'));
        myModal.show();
    }

    function selectAddress(element) {
        document.querySelectorAll('.addr-card').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        element.querySelector('input[type="radio"]').checked = true;
    }

    document.getElementById('btn-place-order').addEventListener('click', function() {
        const selectedAddressRadio = document.querySelector('input[name="selected_address"]:checked');
        if (!selectedAddressRadio || selectedAddressRadio.value === "") {
            alert("Please add and select a delivery address first!");
            return;
        }
        
        const addrId = selectedAddressRadio.value;
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const grandTotal = "<?php echo number_format($grand_total, 2, '.', ''); ?>";
        const ids = "<?php echo $ids_str; ?>";
        const qtys = "<?php echo $qtys_str; ?>";

        if (paymentMethod === 'fpx') {
            window.location.href = `fpx_payment.php?total=${grandTotal}&ids=${ids}&qtys=${qtys}&addr_id=${addrId}`;
        } else if (paymentMethod === 'tng') {
            window.location.href = `tng_payment.php?total=${grandTotal}&ids=${ids}&qtys=${qtys}&addr_id=${addrId}`;
        }
    });
</script>
</body>
</html>
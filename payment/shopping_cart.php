<?php
session_start();
require_once 'db_config.php'; 

// 1. 检查登录状态
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to view your cart.'); window.location.href='../login_register/login_page.php';</script>";
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. 处理删除购物车商品的请求
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['cart_id'])) {
    $del_id = intval($_GET['cart_id']);
    $conn->query("DELETE FROM cart_items WHERE id = $del_id AND user_id = $user_id");
    header("Location: shopping_cart.php");
    exit();
}

// ----------------------------------------------------
// 🎯 新增：处理地址管理 (与 check_out.php 完全一致)
// ----------------------------------------------------
// 删除地址
if (isset($_GET['delete_addr_id'])) {
    $del_id = intval($_GET['delete_addr_id']);
    $stmt = $conn->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $del_id, $user_id);
    $stmt->execute();
    header("Location: shopping_cart.php");
    exit();
}
// 新增地址
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_type']) && $_POST['action_type'] == 'add_new_addr') {
    $unit = trim($_POST['unit_no']);
    $building = trim($_POST['building_name']);
    $street = trim($_POST['street_name']);
    $full_address = "$unit, $building, $street";
    $city_state = trim($_POST['city']) . ", " . trim($_POST['state']);

    $stmt = $conn->prepare("INSERT INTO addresses (user_id, receiver_name, receiver_phone, full_address, postcode, city_state, label) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $_POST['name'], $_POST['phone'], $full_address, $_POST['postcode'], $city_state, $_POST['label']);
    $stmt->execute();
    header("Location: shopping_cart.php");
    exit();
}
// 修改地址
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
    header("Location: shopping_cart.php");
    exit();
}
// ----------------------------------------------------

// 3. 处理编辑购物车商品配置的请求 (Edit Modal 的后台逻辑)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_cart') {
    $edit_cart_id = intval($_POST['edit_cart_id']);
    $new_variant_id = intval($_POST['edit_variant_id']);
    $new_custom_name = trim($_POST['edit_custom_name']);
    
    $stmt = $conn->prepare("UPDATE cart_items SET variant_id = ?, custom_name = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("isii", $new_variant_id, $new_custom_name, $edit_cart_id, $user_id);
    $stmt->execute();
    
    header("Location: shopping_cart.php");
    exit();
}

// 4. 获取真实的购物车数据
$sql = "SELECT 
            c.id AS cart_id, 
            c.quantity, 
            c.custom_name, 
            p.id AS product_id, 
            p.name, 
            p.price AS base_price, 
            p.image_url, 
            v.id AS variant_id,
            v.spec_value,
            s1.option_name AS string_name, IFNULL(s1.additional_price, 0) AS string_price,
            s2.option_name AS tension_name, IFNULL(s2.additional_price, 0) AS tension_price
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        JOIN product_variants v ON c.variant_id = v.id
        LEFT JOIN service_options s1 ON c.string_option_id = s1.id
        LEFT JOIN service_options s2 ON c.tension_option_id = s2.id
        WHERE c.user_id = $user_id
        ORDER BY c.added_at DESC";

$result = $conn->query($sql);
$cart_items = [];
$product_ids = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['final_price'] = $row['base_price'] + $row['string_price'] + $row['tension_price'];
        if (!empty($row['custom_name'])) {
            $row['final_price'] += 15;
        }
        $cart_items[] = $row;
        $product_ids[] = $row['product_id'];
    }
}

// 5. 智能获取购物车中商品的所有 Variant
$variants_dict = [];
if (!empty($product_ids)) {
    $p_ids_str = implode(',', array_unique($product_ids));
    $var_res = $conn->query("SELECT id, product_id, spec_value FROM product_variants WHERE product_id IN ($p_ids_str) AND stock_quantity > 0");
    if ($var_res) {
        while($v = $var_res->fetch_assoc()) {
            $variants_dict[$v['product_id']][] = $v;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart | Yonex Official</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --primary-blue: #003366; 
            --primary-gold: #FFD700; 
            --bg-light: #f4f7f9; 
            --card-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }
        body { 
            background-color: var(--bg-light); 
            font-family: 'Inter', sans-serif; 
            padding-bottom: 110px; 
            color: #333;
        }
        
        .page-title { font-family: 'Oswald', sans-serif; color: var(--primary-blue); letter-spacing: 1px; }
        
        .cart-container { max-width: 1000px; margin: 40px auto; }
        .section-card { 
            background: white; border-radius: 16px; 
            box-shadow: var(--card-shadow); margin-bottom: 25px; 
            padding: 30px; border: 1px solid #eef2f6; 
        }

        /* 🎯 新增的地址样式 */
        .address-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
        .addr-card {
            border: 2px solid #eef2f6; border-radius: 12px; padding: 20px;
            cursor: pointer; position: relative; transition: all 0.2s ease; background: #fff;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .addr-card:hover { border-color: #b3cce6; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,51,102,0.05); }
        .addr-card.selected { border-color: var(--primary-blue); background: #f8fbff; }
        .addr-card.selected::before {
            content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
            color: var(--primary-blue); position: absolute; top: 15px; right: 20px;
            font-size: 1.4rem; animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes popIn { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        
        .addr-name { font-weight: 700; font-size: 1.05rem; color: #1a1a1a; margin-bottom: 5px; }
        .addr-phone { color: #6c757d; font-weight: 500; font-size: 0.9rem; }
        .addr-detail { font-size: 0.9rem; color: #555; line-height: 1.5; margin-top: 10px; }

        /* 🎯 新增：地址的操作按钮样式 */
        .addr-actions { border-top: 1px dashed #e2e8f0; padding-top: 12px; margin-top: 15px; display: flex; justify-content: flex-end; gap: 15px; }
        .action-link { font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.2s; position: relative; z-index: 2;}
        .action-link.edit { color: var(--primary-blue); }
        .action-link.edit:hover { color: #00509e; text-decoration: underline; }
        .action-link.delete { color: #dc3545; }
        .action-link.delete:hover { color: #a71d2a; text-decoration: underline; }

        .empty-address { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 40px 20px; text-align: center; background: #f8fafc; cursor: pointer; transition: 0.3s; }
        .empty-address:hover { border-color: var(--primary-blue); background: #f0f7ff; }

        .cart-table th { 
            text-transform: uppercase; font-size: 0.85rem; color: #94a3b8; 
            font-weight: 600; border-bottom: 2px solid #eef2f6; padding-bottom: 15px;
        }
        .cart-table td { padding: 25px 10px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .cart-table tr:last-child td { border-bottom: none; }
        
        .product-img { 
            width: 90px; height: 90px; object-fit: contain; 
            background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; margin-right: 20px; 
            padding: 5px;
        }
        
        .product-title { font-weight: 700; font-size: 1.1rem; color: #1e293b; margin-bottom: 6px; }
        .spec-info { font-size: 0.85rem; color: #64748b; line-height: 1.6; }
        .spec-badge { 
            background: #f1f5f9; color: #475569; padding: 3px 8px; 
            border-radius: 6px; font-weight: 500; margin-right: 5px; display: inline-block; margin-bottom: 4px;
        }
        .item-price { color: var(--primary-blue); font-size: 1.2rem; font-weight: 700; }
        
        .form-check-input { width: 1.3em; height: 1.3em; cursor: pointer; border-color: #cbd5e1; }
        .form-check-input:checked { background-color: var(--primary-blue); border-color: var(--primary-blue); }
        
        .qty-wrapper { display: inline-flex; align-items: center; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; }
        .qty-btn { background: #f8fafc; border: none; padding: 6px 14px; font-weight: bold; color: #475569; transition: 0.2s; cursor: pointer; }
        .qty-btn:hover { background: #e2e8f0; color: var(--primary-blue); }
        .qty-input { width: 45px; text-align: center; border: none; font-weight: 600; outline: none; background: #fff; }
        
        .btn-action-icon { background: none; border: none; font-size: 1.1rem; color: #94a3b8; transition: 0.2s; cursor: pointer; padding: 5px; }
        .btn-action-icon:hover.edit { color: var(--primary-blue); transform: scale(1.1); }
        .btn-action-icon:hover.delete { color: #dc3545; transform: scale(1.1); }

        .checkout-bar { 
            position: fixed; bottom: 0; left: 0; right: 0; 
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            box-shadow: 0 -10px 30px rgba(0,0,0,0.05); padding: 20px 0; z-index: 1000; 
            border-top: 1px solid #eef2f6;
        }
        .total-label { color: #64748b; font-weight: 600; font-size: 1rem; margin-right: 15px; }
        .total-price { color: var(--primary-blue); font-size: 2.2rem; font-weight: 800; font-family: 'Oswald', sans-serif; line-height: 1; }
        
        .btn-pay { 
            background: var(--primary-blue); color: white; padding: 15px 50px; 
            border: none; font-weight: 700; font-size: 1.1rem; text-transform: uppercase; 
            border-radius: 10px; transition: 0.3s; letter-spacing: 1px;
        }
        .btn-pay:hover:not(:disabled) { background: #001f3f; color: var(--primary-gold); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,51,102,0.2); }
        .btn-pay:disabled { background: #cbd5e1; cursor: not-allowed; color: #fff; }
        .btn-light:hover { background-color: var(--primary-blue) !important; color: white !important; border-color: var(--primary-blue) !important; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,51,102,0.15); }
        
        .modal-body .form-control, .modal-body .form-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem; background-color: #f8fafc; transition: all 0.2s ease-in-out; }
        .modal-body .form-control:focus, .modal-body .form-select:focus { background-color: #ffffff; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1); }
    </style>
</head>
<body>

<div class="cart-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold page-title"><i class="fas fa-shopping-cart me-2"></i>MY SHOPPING CART</h2>
        
        <a href="../index.php" class="btn btn-light rounded-pill px-4 fw-bold" style="color: var(--primary-blue); border: 2px solid #eef2f6; transition: 0.3s;">
            <i class="fas fa-arrow-left me-2"></i>Back to Home
        </a>
    </div>
    
    <div class="section-card" id="address-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>Select Delivery Address</h5>
            <button class="btn btn-sm btn-outline-dark fw-medium rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="prepareAddModal()">
                <i class="fas fa-plus me-1"></i> Add New
            </button>
        </div>

        <?php
        $addr_res = $conn->query("SELECT * FROM addresses WHERE user_id = '$user_id' ORDER BY id DESC");
        $all_addresses = [];
        if($addr_res && $addr_res->num_rows > 0) {
            while($row = $addr_res->fetch_assoc()) {
                $all_addresses[] = $row;
            }
        }
        ?>

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
                            <span class="badge rounded-pill px-2 py-1" style="font-size: 0.7rem; background-color: #64748b;"><?php echo htmlspecialchars($addr['label'] ?? 'Home'); ?></span>
                        </div>
                        
                        <div class="addr-detail mt-2">
                            <?php echo htmlspecialchars($addr['full_address']); ?><br>
                            <?php echo htmlspecialchars($addr['postcode']); ?>, <?php echo htmlspecialchars($addr['city_state'] ?? ''); ?>
                        </div>

                        <div class="addr-actions mt-3">
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
                            <a href="shopping_cart.php?delete_addr_id=<?php echo $addr['id']; ?>" class="action-link delete" onclick="event.stopPropagation(); return confirm('Delete this address?');">
                                <i class="fas fa-trash-alt me-1"></i> Remove
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="section-card">
        <?php if (empty($cart_items)): ?>
            <div class="text-center py-5">
                <div class="mb-4"><i class="fas fa-box-open fa-4x" style="color: #cbd5e1;"></i></div>
                <h5 class="fw-bold text-dark mb-2">Your cart is empty</h5>
                <p class="text-muted mb-4">Looks like you haven't added any Yonex gear to your cart yet.</p>
                <a href="../index.php" class="btn btn-primary px-4 py-2 fw-bold rounded-pill" style="background: var(--primary-blue); border: none;">Shop Now</a>
            </div>
        <?php else: ?>
            <table class="table cart-table m-0">
                <thead>
                    <tr>
                        <th width="50" class="text-center"><input class="form-check-input" type="checkbox" id="selectAll" checked></th>
                        <th>Product Details</th>
                        <th width="150">Unit Price</th>
                        <th width="130" class="text-center">Quantity</th>
                        <th width="110" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td class="text-center">
                            <input class="form-check-input item-checkbox" type="checkbox" 
                                   data-price="<?php echo $item['final_price']; ?>" 
                                   data-id="<?php echo $item['cart_id']; ?>" checked>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../images/<?php echo basename($item['image_url']); ?>" class="product-img" onerror="this.src='../images/placeholder.jpg'">
                                <div>
                                    <div class="product-title"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="spec-info">
                                        <span class="spec-badge"><i class="fas fa-tag me-1"></i> <?php echo htmlspecialchars($item['spec_value']); ?></span>
                                        <?php if(!empty($item['string_name'])): ?>
                                            <span class="spec-badge"><i class="fas fa-table-tennis me-1"></i> <?php echo htmlspecialchars($item['string_name']); ?></span>
                                            <?php if(!empty($item['tension_name'])): ?>
                                                <span class="spec-badge"><i class="fas fa-weight-hanging me-1"></i> <?php echo htmlspecialchars($item['tension_name']); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($item['custom_name'])): ?>
                                            <br>
                                            <div style="font-size: 0.85rem; color: #d0021b; font-weight: 700; margin-top: 8px; background: #fff1f2; padding: 4px 8px; border-radius: 4px; display: inline-block;">
                                                Name Printing: <?php echo htmlspecialchars($item['custom_name']); ?> (+RM 15.00)
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="item-price">RM <?php echo number_format($item['final_price'], 2); ?></td>
                        <td>
                            <div class="qty-wrapper mx-auto">
                                <button type="button" class="qty-btn" onclick="changeQty(this, -1)">-</button>
                                <input type="text" class="qty-input" value="<?php echo $item['quantity']; ?>" readonly>
                                <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn-action-icon edit" title="Edit Item" 
                                        onclick="openEditItemModal(<?php echo $item['cart_id']; ?>, <?php echo $item['product_id']; ?>, <?php echo $item['variant_id']; ?>, '<?php echo addslashes($item['custom_name']); ?>', '<?php echo addslashes($item['name']); ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="shopping_cart.php?action=remove&cart_id=<?php echo $item['cart_id']; ?>" class="btn-action-icon delete text-decoration-none" title="Remove Item" onclick="return confirm('Remove this item from your cart?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="checkout-bar">
    <div class="container cart-container my-0 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="me-4 pe-4 border-end">
                <span class="text-muted fw-bold">Selected Items: <span id="count" class="text-dark fs-5 mx-1">0</span></span>
            </div>
            <div class="d-flex align-items-center">
                <span class="total-label">Grand Total:</span>
                <span class="total-price" id="totalDisplay">RM 0.00</span>
            </div>
        </div>
        <button class="btn btn-pay" id="payBtn" disabled>CHECK OUT <i class="fas fa-arrow-right ms-2"></i></button>
    </div>
</div>

<div class="modal fade" id="addressModal" tabindex="-1">
  <div class="modal-content modal-dialog modal-dialog-centered border-0 rounded-4 shadow-lg">
    <form method="POST" action="shopping_cart.php" id="addressForm">
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

<div class="modal fade" id="editCartModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <form method="POST" action="shopping_cart.php">
        <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
          <h5 class="modal-title fw-bold" id="editItemTitle" style="color: var(--primary-blue);">Edit Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body px-4 py-3">
          <input type="hidden" name="action" value="update_cart">
          <input type="hidden" name="edit_cart_id" id="edit_cart_item_id">
          
          <div class="mb-4">
              <label class="form-label small fw-bold text-muted text-uppercase tracking-wide">Variant (Size/Color)</label>
              <select name="edit_variant_id" id="edit_variant_select" class="form-select bg-light border-0 py-2" required>
                  </select>
          </div>

          <div class="mb-4">
              <label class="form-label small fw-bold text-muted text-uppercase tracking-wide">Name Printing (Optional)</label>
              <input type="text" name="edit_custom_name" id="edit_custom_name" class="form-control bg-light border-0 py-2" placeholder="Leave blank if no printing">
              <div class="form-text text-danger mt-2" style="font-size: 0.8rem; font-weight: 500;"><i class="fas fa-exclamation-circle me-1"></i> +RM 15.00 will be charged if name is added.</div>
          </div>
          
          <div class="alert alert-secondary py-2 border-0" style="font-size: 0.8rem; border-radius: 8px;">
              <i class="fas fa-info-circle me-1"></i> To change stringing or tension, please re-add the product from the shop page to ensure correct stock calculation.
          </div>
        </div>
        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3" style="background: var(--primary-blue); border:none; padding: 14px; letter-spacing: 1px;">SAVE CHANGES</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ----------------------------------------------------
    //  🎯 商品编辑弹窗 JS 逻辑
    // ----------------------------------------------------
    const variantsData = <?php echo json_encode($variants_dict); ?>;
    
    function openEditItemModal(cartId, productId, currentVariantId, currentCustomName, productName) {
        document.getElementById('editItemTitle').innerText = "Edit: " + productName;
        document.getElementById('edit_cart_item_id').value = cartId;
        document.getElementById('edit_custom_name').value = currentCustomName;
        
        const select = document.getElementById('edit_variant_select');
        select.innerHTML = '';
        if (variantsData[productId]) {
            variantsData[productId].forEach(v => {
                const option = document.createElement('option');
                option.value = v.id;
                option.text = v.spec_value;
                if (v.id == currentVariantId) option.selected = true;
                select.appendChild(option);
            });
        }
        var editModal = new bootstrap.Modal(document.getElementById('editCartModal'));
        editModal.show();
    }

    // ----------------------------------------------------
    //  🎯 地址增改弹窗 JS 逻辑 + 马来西亚邮编引擎
    // ----------------------------------------------------
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

    Object.keys(malaysiaData).forEach(state => { stateSelect.add(new Option(state, state)); });

    stateSelect.addEventListener('change', function() {
        citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';
        citySelect.disabled = false;
        const selectedState = this.value;
        const cityData = malaysiaData[selectedState].cities;
        postcodeHint.innerText = `(Valid range: ${malaysiaData[selectedState].hint})`;
        Object.keys(cityData).forEach(city => { citySelect.add(new Option(city, city)); });
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
            if (!regex.test(pc)) { postcodeInput.setCustomValidity(`Invalid postcode for ${state}. Please use range ${malaysiaData[state].hint}`); }
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
        event.stopPropagation(); // 防止点编辑时选中卡片
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


    // ----------------------------------------------------
    //  🎯 原有购物车核心逻辑
    // ----------------------------------------------------
    function selectAddress(element) {
        document.querySelectorAll('.addr-card').forEach(card => card.classList.remove('selected'));
        element.classList.add('selected');
        element.querySelector('input[type="radio"]').checked = true;
    }

    function changeQty(btn, delta) {
        const input = btn.parentElement.querySelector('.qty-input');
        let val = Math.max(1, parseInt(input.value) + delta);
        if (val > 10) val = 10;
        input.value = val;
        calc();
    }

    function calc() {
        let total = 0, n = 0;
        let ids = [], qtys = [];
        
        document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
            const row = cb.closest('tr');
            const price = parseFloat(cb.dataset.price);
            const qty = parseInt(row.querySelector('.qty-input').value);
            
            total += price * qty;
            n++;
            ids.push(cb.dataset.id);
            qtys.push(qty);
        });

        document.getElementById('totalDisplay').innerText = 'RM ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('count').innerText = n;
        document.getElementById('payBtn').disabled = n === 0;

        document.getElementById('payBtn').onclick = () => {
            const addr = document.querySelector('input[name="selected_address"]:checked');
            if (!addr || addr.value === "") {
                alert("Please add and select a delivery address first!");
                return;
            }
            const addrId = addr.value;
            window.location.href = `check_out.php?ids=${ids.join(',')}&qtys=${qtys.join(',')}&addr_id=${addrId}`;
        };
    }

    document.getElementById('selectAll').onclick = (e) => {
        document.querySelectorAll('.item-checkbox').forEach(c => c.checked = e.target.checked);
        calc();
    };

    document.addEventListener('change', (e) => { 
        if(e.target.classList.contains('item-checkbox')) {
            const allCheckboxes = document.querySelectorAll('.item-checkbox');
            const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            document.getElementById('selectAll').checked = (allCheckboxes.length === checkedBoxes.length);
            calc(); 
        }
    });

    window.onload = calc;
</script>

</body>
</html>
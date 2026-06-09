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

// 3. 获取真实的购物车数据 (连接变体、球线、磅数)
$sql = "SELECT 
            c.id AS cart_id, 
            c.quantity, 
            p.id AS product_id, 
            p.name, 
            p.price AS base_price, 
            p.image_url, 
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
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $row['final_price'] = $row['base_price'] + $row['string_price'] + $row['tension_price'];
        $cart_items[] = $row;
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
        
        /* 容器与卡片 */
        .cart-container { max-width: 1000px; margin: 40px auto; }
        .section-card { 
            background: white; border-radius: 16px; 
            box-shadow: var(--card-shadow); margin-bottom: 25px; 
            padding: 30px; border: 1px solid #eef2f6; 
        }

        /* === 顶级地址卡片 UI (与 Checkout 保持一致) === */
        .address-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
        .addr-card {
            border: 2px solid #eef2f6; border-radius: 12px; padding: 20px;
            cursor: pointer; position: relative; transition: all 0.2s ease; background: #fff;
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

        /* === 购物车列表 UI === */
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
        
        /* 自定义大号 Checkbox */
        .form-check-input { width: 1.3em; height: 1.3em; cursor: pointer; border-color: #cbd5e1; }
        .form-check-input:checked { background-color: var(--primary-blue); border-color: var(--primary-blue); }
        
        /* 数量调节器 */
        .qty-wrapper { display: inline-flex; align-items: center; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; }
        .qty-btn { background: #f8fafc; border: none; padding: 6px 14px; font-weight: bold; color: #475569; transition: 0.2s; cursor: pointer; }
        .qty-btn:hover { background: #e2e8f0; color: var(--primary-blue); }
        .qty-input { width: 45px; text-align: center; border: none; font-weight: 600; outline: none; background: #fff; }
        
        .btn-remove { color: #cbd5e1; transition: 0.2s; font-size: 1.2rem; }
        .btn-remove:hover { color: #dc3545; transform: scale(1.1); }

        /* === 悬浮底部结账栏 === */
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
    </style>
</head>
<body>

<div class="cart-container">
    <h2 class="mb-4 fw-bold page-title"><i class="fas fa-shopping-cart me-2"></i>MY SHOPPING CART</h2>
    
    <div class="section-card">
        <h5 class="fw-bold mb-4"><i class="fas fa-map-marker-alt text-danger me-2"></i>Select Delivery Address</h5>
        <?php
        $addr_res = $conn->query("SELECT * FROM addresses WHERE user_id = '$user_id'");
        if ($addr_res && $addr_res->num_rows > 0): 
        ?>
            <div class="address-grid">
                <?php 
                $first = true;
                while($addr = $addr_res->fetch_assoc()): 
                ?>
                    <div class="addr-card <?php echo $first ? 'selected' : ''; ?>" onclick="selectAddress(this)">
                        <input type="radio" name="selected_address" value="<?php echo $addr['id']; ?>" <?php echo $first ? 'checked' : ''; ?> style="display:none;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="addr-name"><?php echo htmlspecialchars($addr['receiver_name']); ?></span>
                                <span class="addr-phone ms-2"><?php echo htmlspecialchars($addr['receiver_phone']); ?></span>
                            </div>
                            <span class="badge bg-secondary rounded-pill px-2" style="font-size: 0.7rem;"><?php echo htmlspecialchars($addr['label']); ?></span>
                        </div>
                        <div class="addr-detail">
                            <?php echo htmlspecialchars($addr['full_address']); ?><br>
                            <?php echo htmlspecialchars($addr['postcode']); ?>, <?php echo htmlspecialchars($addr['city_state']); ?>
                        </div>
                    </div>
                <?php 
                $first = false; 
                endwhile; 
                ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4 rounded-3" style="background: #f8fafc; border: 2px dashed #cbd5e1;">
                <p class="text-muted mb-0"><i class="fas fa-info-circle me-1"></i>No addresses found. You can add a new address during the checkout step.</p>
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
                        <th width="150" class="text-center">Quantity</th>
                        <th width="80" class="text-center">Action</th>
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
                            <a href="shopping_cart.php?action=remove&cart_id=<?php echo $item['cart_id']; ?>" class="btn-remove text-decoration-none" onclick="return confirm('Remove this item from your cart?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
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

<script>
    // 1. 卡片地址选择逻辑
    function selectAddress(element) {
        document.querySelectorAll('.addr-card').forEach(card => card.classList.remove('selected'));
        element.classList.add('selected');
        element.querySelector('input[type="radio"]').checked = true;
    }

    // 2. 数量修改器
    function changeQty(btn, delta) {
        const input = btn.parentElement.querySelector('.qty-input');
        let val = Math.max(1, parseInt(input.value) + delta);
        if (val > 10) val = 10; // 限制最大购买数量
        input.value = val;
        calc();
    }

    // 3. 核心计算器
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

        // 绑定跳转
        document.getElementById('payBtn').onclick = () => {
            const addr = document.querySelector('input[name="selected_address"]:checked');
            const addrId = addr ? addr.value : '';
            window.location.href = `check_out.php?ids=${ids.join(',')}&qtys=${qtys.join(',')}&addr_id=${addrId}`;
        };
    }

    // 全选逻辑
    document.getElementById('selectAll').onclick = (e) => {
        document.querySelectorAll('.item-checkbox').forEach(c => c.checked = e.target.checked);
        calc();
    };

    // 单选关联全选逻辑
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
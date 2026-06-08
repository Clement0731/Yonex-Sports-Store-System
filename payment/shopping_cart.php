<?php
session_start();
require_once 'db_config.php'; 

// 1. 如果用户没登录，踢回登录页
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

// 3. 💥 真正的购物车查询！连接了 4 个表来获取完整信息
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
        // 计算：商品原价 + 穿线费 + 磅数费 = 最终单价
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
    <title>Shopping Cart | Yonex Official</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --yonex-blue: #003366;
            --yonex-gold: #FFD700;
            --bg-light: #f5f7f9;
        }
        
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; }
        .header-banner { background-color: var(--yonex-blue); color: white; padding: 20px 0; text-align: center; font-family: 'Oswald', sans-serif; text-transform: uppercase; letter-spacing: 2px; }
        .cart-container { max-width: 1000px; margin: 40px auto; }
        .cart-item { background: white; border-radius: 10px; padding: 20px; margin-bottom: 15px; display: flex; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .item-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; margin-right: 20px; }
        .item-details { flex-grow: 1; }
        .item-title { font-size: 1.1rem; font-weight: 700; color: #333; margin-bottom: 5px; }
        .item-price { color: var(--yonex-blue); font-weight: 700; font-size: 1.2rem; margin-top: 5px;}
        .qty-control { display: flex; align-items: center; background: #f8f9fa; border-radius: 20px; padding: 5px 10px; width: fit-content; }
        .qty-btn { border: none; background: none; font-size: 1.2rem; font-weight: bold; color: var(--yonex-blue); cursor: pointer; padding: 0 10px; }
        .qty-input { width: 40px; text-align: center; border: none; background: transparent; font-weight: bold; }
        .summary-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); position: sticky; top: 20px; }
        .btn-checkout { background-color: var(--yonex-blue); color: white; border: none; width: 100%; padding: 12px; border-radius: 8px; font-weight: 700; text-transform: uppercase; margin-top: 20px; transition: 0.3s; }
        .btn-checkout:hover { background-color: #002244; }
        .btn-checkout:disabled { background-color: #ccc; cursor: not-allowed; }
        .custom-checkbox { width: 20px; height: 20px; cursor: pointer; }
        .btn-delete { color: #dc3545; transition: color 0.2s; margin-left: 15px;}
        .btn-delete:hover { color: #a71d2a; }
        .item-actions { display: flex; align-items: center; }
    </style>
</head>
<body>

    <div class="header-banner">
        <h2>My Shopping Cart</h2>
    </div>

    <div class="container cart-container">
        <div class="row">
            <div class="col-md-8">
                
                <div class="d-flex align-items-center mb-3 bg-white p-3 rounded shadow-sm">
                    <input type="checkbox" id="selectAll" class="form-check-input custom-checkbox me-3" checked>
                    <label for="selectAll" class="fw-bold mb-0">Select All Items</label>
                </div>

                <?php if(empty($cart_items)): ?>
                    <div class="text-center p-5 bg-white rounded shadow-sm">
                        <h4 class="text-muted">Your cart is empty.</h4>
                        <a href="../index.php" class="btn btn-outline-primary mt-3">Go Shopping</a>
                    </div>
                <?php else: ?>
                    <?php foreach($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="me-3">
                                <input type="checkbox" class="form-check-input item-checkbox custom-checkbox" 
                                       data-id="<?php echo $item['cart_id']; ?>" 
                                       data-price="<?php echo $item['final_price']; ?>" checked>
                            </div>
                            
                            <img src="../images/<?php echo basename($item['image_url']); ?>" alt="Product" class="item-img" onerror="this.src='../images/placeholder.jpg'">
                            
                            <div class="item-details">
                                <div class="item-title"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-meta" style="line-height: 1.5; font-size: 0.9rem; color: #555;">
                                    <span style="color: var(--yonex-blue); font-weight: 600;">Spec:</span> <?php echo htmlspecialchars($item['spec_value']); ?><br>
                                    
                                    <?php if(!empty($item['string_name'])): ?>
                                        <span style="color: var(--yonex-blue); font-weight: 600;">String:</span> <?php echo htmlspecialchars($item['string_name']); ?> 
                                        <?php if(!empty($item['tension_name'])) echo " (" . htmlspecialchars($item['tension_name']) . ")"; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="item-price">RM <?php echo number_format($item['final_price'], 2); ?></div>
                            </div>
                            
                            <div class="item-actions">
                                <div class="qty-control">
                                    <button type="button" class="qty-btn" onclick="updateQty(this, -1)">-</button>
                                    <input type="text" class="qty-input" value="<?php echo $item['quantity']; ?>" readonly>
                                    <button type="button" class="qty-btn" onclick="updateQty(this, 1)">+</button>
                                </div>
                                <a href="shopping_cart.php?action=remove&cart_id=<?php echo $item['cart_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to remove this item?');">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <div class="col-md-4">
                <div class="summary-card">
                    <h4 class="mb-4 fw-bold">Order Summary</h4>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Selected Items (<span id="itemCount">0</span>)</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5" style="color: var(--yonex-blue);" id="totalPrice">RM 0.00</span>
                    </div>
                    <button class="btn-checkout" id="checkoutBtn" disabled>Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const totalPriceDisplay = document.getElementById('totalPrice');
        const itemCountDisplay = document.getElementById('itemCount');
        const checkoutBtn = document.getElementById('checkoutBtn');

        function updateQty(btn, delta) {
            const input = btn.parentElement.querySelector('.qty-input');
            let val = parseInt(input.value) + delta;
            if(val < 1) val = 1;
            if(val > 10) val = 10;
            input.value = val;
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            let count = 0;
            let selectedIds = [];
            let selectedQtys = [];

            checkboxes.forEach(cb => {
                if(cb.checked) {
                    const price = parseFloat(cb.dataset.price);
                    // 找到当前商品对应的数量输入框
                    const qty = parseInt(cb.closest('.cart-item').querySelector('.qty-input').value);
                    total += price * qty;
                    count++;
                    selectedIds.push(cb.dataset.id); 
                    selectedQtys.push(qty);
                }
            });

            totalPriceDisplay.innerText = 'RM ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            itemCountDisplay.innerText = count;
            checkoutBtn.disabled = count === 0;

            if(selectAll && checkboxes.length > 0) {
                selectAll.checked = (count === checkboxes.length);
            }

            // 更新 checkout 按钮的跳转链接，传递 cart_id
            if (count > 0) {
                checkoutBtn.onclick = () => {
                    window.location.href = 'check_out.php?ids=' + selectedIds.join(',') + '&qtys=' + selectedQtys.join(',');
                };
            }
        }

        if(selectAll) {
            selectAll.onclick = () => {
                checkboxes.forEach(c => c.checked = selectAll.checked);
                calculateTotal();
            };
        }

        document.addEventListener('change', (e) => {
            if(e.target.classList.contains('item-checkbox')) {
                calculateTotal();
            }
        });

        // 页面加载时自动计算一次
        calculateTotal();
    </script>
</body>
</html>
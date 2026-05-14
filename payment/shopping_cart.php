<?php
session_start();
require_once 'db_config.php'; 

// 从数据库获取产品 (模拟购物车数据)
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$cart_items = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
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
            --yonex-green: #00A650;
            --bg-light: #f5f7f9;
        }
        
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; }

        .header-wrapper { background: white; border-bottom: 1px solid #e0e0e0; margin-bottom: 30px; }
        .top-line { background: var(--yonex-blue); height: 4px; }
        .main-header { padding: 20px 0; display: flex; align-items: center; }
        .brand-logo { font-family: 'Oswald', sans-serif; font-size: 2rem; font-weight: 700; color: var(--yonex-blue); font-style: italic; letter-spacing: -1px; text-decoration: none; border-right: 1px solid #ddd; padding-right: 20px; margin-right: 20px; }
        .brand-logo span { color: var(--yonex-green); }
        .page-title { font-size: 1.4rem; color: #444; margin: 0; font-weight: 400; }
        
        .cart-card { background: white; border-radius: 4px; box-shadow: 0 1px 10px rgba(0,0,0,0.05); margin-bottom: 100px; }
        .product-img { width: 70px; height: 70px; object-fit: contain; background: #fff; border: 1px solid #eee; border-radius: 4px; margin-right: 15px; }
        .product-name { font-weight: 700; color: #222; margin-bottom: 2px; }
        
        .qty-control { display: flex; align-items: center; border: 1px solid #ddd; width: fit-content; }
        .qty-btn { background: white; border: none; width: 30px; height: 30px; cursor: pointer; }
        .qty-input { width: 40px; height: 30px; text-align: center; border: none; border-left: 1px solid #ddd; border-right: 1px solid #ddd; font-size: 0.9rem; }
        
        .checkout-bar { position: fixed; bottom: 0; left: 0; right: 0; background: white; box-shadow: 0 -5px 15px rgba(0,0,0,0.08); padding: 15px 0; z-index: 1000; }
        .total-price { color: var(--yonex-blue); font-size: 2rem; font-weight: 800; margin-left: 10px; font-family: 'Oswald', sans-serif; }
        .btn-pay { background: var(--yonex-blue); color: white; padding: 12px 60px; border: none; font-weight: 700; text-transform: uppercase; border-radius: 4px; }
        .btn-pay:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="header-wrapper">
    <div class="top-line"></div>
    <div class="container">
        <div class="main-header">
            <a href="#" class="brand-logo">YONEX<span>.</span></a>
            <h1 class="page-title">Shopping Cart</h1>
        </div>
    </div>
</div>

<div class="container">
    <div class="cart-card">
        <?php if (empty($cart_items)): ?>
            <div class="p-5 text-center">
                <p class="text-muted">Your cart is currently empty.</p>
            </div>
        <?php else: ?>
            <table class="table align-middle m-0">
                <thead>
                    <tr>
                        <th width="50"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                        <th>Product Details</th>
                        <th width="120">Price</th>
                        <th width="150" class="text-center">Quantity</th>
                        <th width="100" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <input class="form-check-input item-checkbox" type="checkbox" 
                                   data-price="<?php echo $item['price']; ?>" 
                                   data-id="<?php echo $item['id']; ?>">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="images/<?php echo $item['id']; ?>.jpg" class="product-img">
                                <div>
                                    <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <small class="text-muted">ID: <?php echo $item['id']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td class="fw-bold text-primary">RM <?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="qty-control mx-auto">
                                <button class="qty-btn" onclick="changeQty(this, -1)">-</button>
                                <input type="text" class="qty-input" value="1" readonly>
                                <button class="qty-btn" onclick="changeQty(this, 1)">+</button>
                            </div>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-link text-danger text-decoration-none" onclick="removeRow(this)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="checkout-bar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="text-muted fw-bold">Total (<span id="count">0</span> items):</span>
                <span class="total-price" id="totalDisplay">RM 0.00</span>
            </div>
            <button class="btn btn-pay" id="payBtn" disabled>Check Out</button>
        </div>
    </div>
</div>

<script>
    const totalDisplay = document.getElementById('totalDisplay');
    const countDisplay = document.getElementById('count');
    const payBtn = document.getElementById('payBtn');
    const selectAll = document.getElementById('selectAll');

    // 修改数量并触发重新计算
    function changeQty(btn, delta) {
        const input = btn.parentElement.querySelector('.qty-input');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        input.value = val;
        calc(); 
    }

    // 核心计算与跳转逻辑
    function calc() {
        let total = 0;
        let n = 0;
        let selectedIds = []; 
        let selectedQtys = []; 

        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => {
            if (cb && cb.checked) {
                const row = cb.closest('tr');
                const price = parseFloat(cb.dataset.price);
                const qty = parseInt(row.querySelector('.qty-input').value);
                
                total += price * qty;
                n++;
                selectedIds.push(cb.dataset.id); 
                selectedQtys.push(qty); // 记录对应数量
            }
        });

        // 更新界面显示
        totalDisplay.innerText = 'RM ' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        countDisplay.innerText = n;
        payBtn.disabled = n === 0;

        // 处理全选框自动打勾
        if (selectAll && checkboxes.length > 0) {
            selectAll.checked = (n === checkboxes.length);
        }

        // 重要：动态设置按钮点击事件，带上 ids 和 qtys 参数
        if (n > 0) {
            payBtn.onclick = () => {
                window.location.href = 'check_out.php?ids=' + selectedIds.join(',') + '&qtys=' + selectedQtys.join(',');
            };
        }
    }

    // 全选逻辑
    if(selectAll) {
        selectAll.onclick = () => {
            document.querySelectorAll('.item-checkbox').forEach(c => {
                c.checked = selectAll.checked;
            });
            calc();
        };
    }

    // 监听单个勾选框变化
    document.addEventListener('change', (e) => {
        if(e.target.classList.contains('item-checkbox')) {
            calc();
        }
    });

    // 删除行
    function removeRow(btn) {
        if(confirm("Remove this item?")) {
            btn.closest('tr').remove();
            calc();
            if(document.querySelectorAll('.item-checkbox').length === 0) location.reload();
        }
    }
</script>

</body>
</html>
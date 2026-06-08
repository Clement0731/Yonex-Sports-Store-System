<?php
session_start();
require_once 'db_config.php'; 

// 从数据库获取产品
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --yonex-blue: #003366; --yonex-gold: #FFD700; --yonex-green: #00A650; --bg-light: #f5f7f9; }
        body { background-color: var(--bg-light); font-family: 'Roboto', sans-serif; }
        .cart-card { background: white; border-radius: 4px; box-shadow: 0 1px 10px rgba(0,0,0,0.05); margin-bottom: 20px; padding: 20px; }
        .product-img { width: 70px; height: 70px; object-fit: contain; background: #fff; border: 1px solid #eee; border-radius: 4px; margin-right: 15px; }
        .checkout-bar { position: fixed; bottom: 0; left: 0; right: 0; background: white; box-shadow: 0 -5px 15px rgba(0,0,0,0.08); padding: 15px 0; z-index: 1000; }
        .total-price { color: var(--yonex-blue); font-size: 2rem; font-weight: 800; font-family: 'Oswald', sans-serif; }
        .btn-pay { background: var(--yonex-blue); color: white; padding: 12px 60px; border: none; font-weight: 700; text-transform: uppercase; border-radius: 4px; }
        .btn-pay:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="container py-4">
    <h2 class="mb-4">Shopping Cart</h2>
    
    <div class="cart-card">
        <h5 class="text-primary mb-3"><i class="fas fa-map-marker-alt"></i> Select Delivery Address</h5>
        <?php
        $user_id = $_SESSION['user_id'] ?? 0;
        $addr_res = $conn->query("SELECT * FROM addresses WHERE user_id = '$user_id'");
        if ($addr_res && $addr_res->num_rows > 0): 
            $first = true;
            while($addr = $addr_res->fetch_assoc()): ?>
                <div class="form-check p-3 mb-2 border rounded">
                    <input class="form-check-input" type="radio" name="selected_address" 
                           id="addr_<?php echo $addr['id']; ?>" 
                           value="<?php echo $addr['id']; ?>" 
                           <?php echo $first ? 'checked' : ''; ?>>
                    <label class="form-check-label w-100 ps-2" for="addr_<?php echo $addr['id']; ?>">
                        <strong><?php echo htmlspecialchars($addr['receiver_name']); ?></strong> | 
                        <?php echo htmlspecialchars($addr['receiver_phone']); ?>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($addr['label']); ?></span><br>
                        <small class="text-muted"><?php echo htmlspecialchars($addr['full_address'] . ', ' . $addr['postcode'] . ', ' . $addr['city_state']); ?></small>
                    </label>
                </div>
            <?php $first = false; endwhile; 
        else: ?>
            <p>No addresses found. <a href="../login_register/manage_addresses.php">Add one now</a>.</p>
        <?php endif; ?>
    </div>

    <div class="cart-card">
        <?php if (empty($cart_items)): ?>
            <p class="text-center p-4">Your cart is empty.</p>
        <?php else: ?>
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th><input class="form-check-input" type="checkbox" id="selectAll"></th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><input class="form-check-input item-checkbox" type="checkbox" data-price="<?php echo $item['price']; ?>" data-id="<?php echo $item['id']; ?>"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="images/<?php echo $item['id']; ?>.jpg" class="product-img">
                                <div><?php echo htmlspecialchars($item['name']); ?></div>
                            </div>
                        </td>
                        <td class="fw-bold">RM <?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="input-group" style="width: 120px;">
                                <button class="btn btn-outline-secondary" onclick="changeQty(this, -1)">-</button>
                                <input type="text" class="form-control text-center qty-input" value="1" readonly>
                                <button class="btn btn-outline-secondary" onclick="changeQty(this, 1)">+</button>
                            </div>
                        </td>
                        <td><button class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Delete</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="checkout-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <div>Total Items: <span id="count">0</span> | <span class="total-price" id="totalDisplay">RM 0.00</span></div>
        <button class="btn btn-pay" id="payBtn" disabled>Check Out</button>
    </div>
</div>

<script>
    function changeQty(btn, delta) {
        const input = btn.parentElement.querySelector('.qty-input');
        let val = Math.max(1, parseInt(input.value) + delta);
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

        document.getElementById('totalDisplay').innerText = 'RM ' + total.toFixed(2);
        document.getElementById('count').innerText = n;
        document.getElementById('payBtn').disabled = n === 0;

        // 更新按钮点击事件以包含选中的地址ID
        document.getElementById('payBtn').onclick = () => {
            const addr = document.querySelector('input[name="selected_address"]:checked');
            const addrId = addr ? addr.value : '';
            window.location.href = `check_out.php?ids=${ids.join(',')}&qtys=${qtys.join(',')}&addr_id=${addrId}`;
        };
    }

    document.getElementById('selectAll').onclick = (e) => {
        document.querySelectorAll('.item-checkbox').forEach(c => c.checked = e.target.checked);
        calc();
    };
    document.addEventListener('change', (e) => { if(e.target.classList.contains('item-checkbox')) calc(); });
    function removeRow(btn) { btn.closest('tr').remove(); calc(); }
</script>

</body>
</html>
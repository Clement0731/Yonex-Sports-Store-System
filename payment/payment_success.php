<?php
session_start();
require_once 'db_config.php';

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; 
$db_order_id = 0;
$amount = isset($_GET['amount']) ? (float)$_GET['amount'] : 0.00;

if ($user_id > 0) {
    // 1. Anti-Refresh & Verification Check
    $cart_check = $conn->query("SELECT COUNT(*) FROM cart_items WHERE user_id = '$user_id'");
    $cart_count = ($cart_check && $cart_check->num_rows > 0) ? (int)$cart_check->fetch_row()[0] : 0;

    if ($cart_count > 0) {
        // --- 核心修复：通过传递过来的 addr_id 抓取完整地址 ---
        $shipping_address_str = "No shipping address specified.";
        $addr_id = isset($_GET['addr_id']) ? (int)$_GET['addr_id'] : 0;
        
        if ($addr_id > 0) {
            $addr_stmt = $conn->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
            $addr_stmt->bind_param("ii", $addr_id, $user_id);
            $addr_stmt->execute();
            $addr_res = $addr_stmt->get_result();
            if ($addr_row = $addr_res->fetch_assoc()) {
                $city_state = isset($addr_row['city_state']) ? $addr_row['city_state'] : '';
                $shipping_address_str = $addr_row['receiver_name'] . " | " . $addr_row['receiver_phone'] . "\n" . $addr_row['full_address'] . ", " . $addr_row['postcode'] . " " . $city_state . " (" . $addr_row['label'] . ")";
            }
        }

        $method = !empty($_GET['method']) ? $_GET['method'] : 'Online Payment';
        $bank = !empty($_GET['bank']) ? $_GET['bank'] : '';
        $final_method = empty($bank) ? $method : $method . " (" . $bank . ")";

        // 计算 subtotal (总价扣掉 10 块运费)
        $subtotal = max(0, $amount - 10.00);

        // 4. Secure Transaction Insertion (完全匹配你的原生列名)
        $stmt = $conn->prepare("INSERT INTO orders (USER_ID, ORDER_DATE, TOTAL_PRICE, subtotal, shipping_fee, STATUS, payment_status, shipping_address, payment_method) VALUES (?, NOW(), ?, ?, 10.00, 'Paid', 'Paid', ?, ?)");
        $stmt->bind_param("iddss", $user_id, $amount, $subtotal, $shipping_address_str, $final_method);
        
        if ($stmt->execute()) {
            $db_order_id = $conn->insert_id;
            $_SESSION['last_order_id'] = $db_order_id;
            $_SESSION['last_order_amount'] = $amount;
            
            // 5. Bulletproof Itemized Data Mapping
            $cart_sql = "SELECT c.variant_id, c.product_id, c.quantity, p.price, c.string_option_id, c.tension_option_id 
                         FROM cart_items c
                         JOIN products p ON c.product_id = p.id
                         WHERE c.user_id = '$user_id'";
            
            $cart_res = $conn->query($cart_sql);
            if ($cart_res && $cart_res->num_rows > 0) {
                while ($item = $cart_res->fetch_assoc()) {
                    $p_id = (int)$item['product_id'];
                    $v_id = (int)$item['variant_id'];
                    $qty = (int)$item['quantity'];
                    $u_price = (float)$item['price'];
                    $item_sub = $qty * $u_price;
                    
                    // Safely handle optional services
                    $string_val = !empty($item['string_option_id']) ? (int)$item['string_option_id'] : 'NULL';
                    $tension_val = !empty($item['tension_option_id']) ? (int)$item['tension_option_id'] : 'NULL';
                    
                    // Native Query Execution (完全使用你的 string_option_id, tension_option_id, unit_price)
                    $conn->query("INSERT INTO order_items (order_id, product_id, variant_id, string_option_id, tension_option_id, quantity, unit_price, subtotal) 
                                  VALUES ($db_order_id, $p_id, $v_id, $string_val, $tension_val, $qty, $u_price, $item_sub)");
                    
                    // Deplete Inventory
                    $conn->query("UPDATE product_variants SET stock_quantity = stock_quantity - $qty WHERE id = '$v_id' AND stock_quantity >= $qty");
                }
            }
            
            // 6. Purge Cart
            $conn->query("DELETE FROM cart_items WHERE user_id = '$user_id'");
        }
    } else {
        // Fallback to Session on Page Refresh
        $db_order_id = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 0;
        $amount = isset($_SESSION['last_order_amount']) ? $_SESSION['last_order_amount'] : 0.00;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmed | YONEX Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --premium-navy: #002d56; --premium-border: #e2e8f0; }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .success-card { background: white; width: 95%; max-width: 400px; padding: 45px 35px; border: 1px solid var(--premium-border); text-align: center; }
        .success-indicator { font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #0f172a; text-transform: uppercase; margin-bottom: 10px; display: block; }
        h2 { font-size: 1.6rem; font-weight: 700; color: var(--premium-navy); letter-spacing: -0.02em; margin-bottom: 30px; }
        .order-summary { background: #f8fafc; border: 1px solid var(--premium-border); padding: 20px; text-align: left; margin-bottom: 35px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.85rem; }
        .summary-item:last-child { margin-bottom: 0; padding-top: 12px; border-top: 1px dashed var(--premium-border); }
        .label { color: #64748b; font-weight: 500; }
        .value { color: #0f172a; font-weight: 600; }
        .btn-continue { background: var(--premium-navy); color: white; border: none; width: 100%; padding: 14px; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: block; margin-bottom: 15px; letter-spacing: 0.05em; text-transform: uppercase; text-align: center; }
        .btn-continue:hover { background: #001f3f; color: white; }
    </style>
</head>
<body>
<div class="success-card">
    <span class="success-indicator">Transaction Authorized</span>
    <h2>PAYMENT SUCCESSFUL</h2>
    <div class="order-summary">
        <div class="summary-item"><span class="label">Order ID</span><span class="value">YNX-<?php echo $db_order_id; ?></span></div>
        <div class="summary-item"><span class="label">Payment Status</span><span class="value">Paid</span></div>
        <div class="summary-item"><span class="label">Total Charged</span><span class="value" style="color: var(--premium-navy);">RM <?php echo number_format($amount, 2); ?></span></div>
    </div>
    <a href="order_history.php" class="btn-continue">View Order History</a>
</div>
</body>
</html>
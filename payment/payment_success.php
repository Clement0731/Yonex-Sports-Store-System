<?php
session_start();
require_once 'db_config.php';

// --- 1. 获取支付信息 ---
$amount = isset($_GET['amount']) ? $_GET['amount'] : '0.00';
$method = (isset($_GET['method']) && !empty($_GET['method'])) ? $_GET['method'] : 'Online Payment';
$bank = isset($_GET['bank']) ? $_GET['bank'] : '';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; 
$db_order_id = "N/A";

// --- 2. 核心逻辑：生成详细账单 + 扣除库存 + 写入订单 + 清空购物车 ---
if ($user_id > 0) {
    $detailed_items = "";
    
    // 【A】抓取购物车里的商品、规格、穿线服务等详细信息
    $cart_sql = "SELECT c.variant_id, c.quantity, p.name, v.spec_value, s1.option_name AS string_name, s2.option_name AS tension_name
                 FROM cart_items c
                 JOIN products p ON c.product_id = p.id
                 JOIN product_variants v ON c.variant_id = v.id
                 LEFT JOIN service_options s1 ON c.string_option_id = s1.id
                 LEFT JOIN service_options s2 ON c.tension_option_id = s2.id
                 WHERE c.user_id = '$user_id'";
    
    $cart_res = $conn->query($cart_sql);
    if ($cart_res && $cart_res->num_rows > 0) {
        while ($item = $cart_res->fetch_assoc()) {
            // 自动扣除库存
            $v_id = $item['variant_id'];
            $qty = $item['quantity'];
            $conn->query("UPDATE product_variants SET stock_quantity = stock_quantity - $qty WHERE id = '$v_id' AND stock_quantity >= $qty");
            
            // 拼凑商品明细
            $detailed_items .= "• <b>" . htmlspecialchars($item['name']) . "</b> (" . htmlspecialchars($item['spec_value']) . ")";
            if (!empty($item['string_name'])) {
                $detailed_items .= " <br>&nbsp;&nbsp;<small class='text-muted'>[Service: " . htmlspecialchars($item['string_name']);
                if (!empty($item['tension_name'])) $detailed_items .= ", " . htmlspecialchars($item['tension_name']);
                $detailed_items .= "]</small>";
            }
            $detailed_items .= " <span style='color:var(--yonex-blue);'><b> x" . $qty . "</b></span><br>";
        }
    } else {
        $detailed_items = "• Yonex Badminton Items<br>";
    }

    // 【B】抓取用户最新填写的收货地址
    $addr_str = "No address specified.";
    $addr_res = $conn->query("SELECT * FROM addresses WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
    if ($addr_res && $addr_res->num_rows > 0) {
        $addr = $addr_res->fetch_assoc();
        $addr_str = htmlspecialchars($addr['receiver_name']) . " | " . htmlspecialchars($addr['receiver_phone']) . "<br>" . htmlspecialchars($addr['full_address']) . ", " . htmlspecialchars($addr['postcode']) . " " . htmlspecialchars($addr['city_state']);
    }

    // 【C】把所有信息包装成漂亮的 HTML 存进 ORDER_DESC
    $pay_method_str = htmlspecialchars($method);
    if ($bank) $pay_method_str .= " (" . htmlspecialchars($bank) . ")";

    $product_details = "<div class='detailed-order-info'>";
    $product_details .= "<div class='mb-3'><span class='badge bg-secondary mb-2'>🛒 Items Purchased</span><br>" . $detailed_items . "</div>";
    $product_details .= "<div class='mb-3'><span class='badge bg-secondary mb-2'>📍 Delivery Address</span><br><span class='text-dark'>" . $addr_str . "</span></div>";
    $product_details .= "<div><span class='badge bg-secondary mb-2'>💳 Payment Method</span><br><span class='text-dark'>" . $pay_method_str . "</span></div>";
    $product_details .= "</div>";

    // 【D】写入订单表
    $stmt = $conn->prepare("INSERT INTO orders (USER_ID, ORDER_DATE, TOTAL_PRICE, STATUS, ORDER_DESC) VALUES (?, NOW(), ?, 'Completed', ?)");
    $stmt->bind_param("iss", $user_id, $amount, $product_details);
    
    if ($stmt->execute()) {
        $db_order_id = $conn->insert_id; 
        // 结账成功后清空购物车
        $conn->query("DELETE FROM cart_items WHERE user_id = '$user_id'");
    } else {
        die("Database Error: " . $stmt->error);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | Yonex Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --success-green: #2ecc71; --yonex-blue: #002d56; }
        body { background-color: #f0f4f8; font-family: 'Inter', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .success-card { background: white; width: 95%; max-width: 420px; padding: 40px 30px; border-radius: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); text-align: center; }
        .check-container { width: 80px; height: 80px; background: var(--success-green); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 25px; box-shadow: 0 10px 20px rgba(46, 204, 113, 0.3); }
        h2 { font-family: 'Montserrat', sans-serif; font-weight: 700; color: #1a1a1a; margin-bottom: 10px; }
        .thank-you-msg { color: #777; font-size: 0.95rem; margin-bottom: 30px; }
        .order-summary { background: #f8fafc; border-radius: 16px; padding: 20px; text-align: left; margin-bottom: 30px; border: 1px solid #edf2f7; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.85rem; }
        .label { color: #94a3b8; font-weight: 500; }
        .value { color: #1e293b; font-weight: 600; }
        .btn-continue { background: var(--yonex-blue); color: white; border: none; width: 100%; padding: 16px; border-radius: 14px; font-weight: 700; font-size: 1rem; text-decoration: none; display: block; margin-bottom: 15px; }
        .btn-history { display: block; color: var(--yonex-blue); font-weight: 600; font-size: 0.9rem; text-decoration: none; }
    </style>
</head>
<body>
<div class="success-card">
    <div class="check-container">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
    </div>
    <h2>Payment Successful!</h2>
    <p class="thank-you-msg">Your order has been confirmed.</p>
    <div class="order-summary">
        <div class="summary-item"><span class="label">Transaction Status</span><span class="value text-success">Approved</span></div>
        <div class="summary-item"><span class="label">Order ID</span><span class="value">#YNX-<?php echo $db_order_id; ?></span></div>
        <div class="summary-item"><span class="label">Total Paid</span><span class="value" style="color: var(--yonex-blue);">RM <?php echo number_format($amount, 2); ?></span></div>
    </div>
    <a href="../index.php" class="btn-continue">Continue Shopping</a>
    <a href="order_history.php" class="btn-history">View Order History</a>
</div>
</body>
</html>
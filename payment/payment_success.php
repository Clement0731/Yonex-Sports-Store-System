<?php
session_start();
require_once 'db_config.php';

// --- 数据库逻辑开始 ---
// 1. 获取支付信息
$amount = isset($_GET['amount']) ? $_GET['amount'] : '0.00';

// 优先获取具体的支付方式，如果没有传，则默认显示 Online Payment
if (isset($_GET['method']) && !empty($_GET['method'])) {
    $method = $_GET['method'];
} else {
    $method = 'Online Payment';
}

$bank = isset($_GET['bank']) ? $_GET['bank'] : '';

// 2. 组合商品详情描述
// 确保格式为 "物品名 via 支付方式"，这样 order_history.php 才能正确拆分显示
$item_info = "Yonex Badminton Items"; 
$product_details = $item_info . " via " . $method;

if ($bank) {
    $product_details .= " (" . $bank . ")";
}

// 3. 💥 核心修复：将数据写入 orders 表 (使用了正确的列名，并加入了买家ID和当前时间)
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; 
// 使用 mysqli_real_escape_string 防止特殊字符（如单引号）导致数据库崩溃
$clean_amount = $conn->real_escape_string($amount);
$clean_details = $conn->real_escape_string($product_details);

$sql = "INSERT INTO orders (USER_ID, ORDER_DATE, TOTAL_PRICE, STATUS) 
        VALUES ('$user_id', NOW(), '$amount', 'Pending')";

$db_order_id = "N/A";
if ($conn->query($sql)) {
    $db_order_id = $conn->insert_id; 
} else {
    // 如果这里报错，说明数据库字段类型不够或者 SQL 语句格式有错
    die("Database Error: " . $conn->error);
}
// --- 数据库逻辑结束 ---
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
        :root {
            --success-green: #2ecc71;
            --yonex-blue: #002d56;
        }

        body {
            background-color: #f0f4f8;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .success-card {
            background: white;
            width: 95%;
            max-width: 420px;
            padding: 40px 30px;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            text-align: center;
            position: relative;
        }

        /* 成功勾选图标动画 */
        .check-container {
            width: 80px;
            height: 80px;
            background: var(--success-green);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 25px;
            box-shadow: 0 10px 20px rgba(46, 204, 113, 0.3);
            animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .thank-you-msg {
            color: #777;
            font-size: 0.95rem;
            margin-bottom: 30px;
        }

        /* 订单摘要详情 */
        .order-summary {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            text-align: left;
            margin-bottom: 30px;
            border: 1px solid #edf2f7;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.85rem;
        }

        .summary-item:last-child { margin-bottom: 0; }

        .label { color: #94a3b8; font-weight: 500; }
        .value { color: #1e293b; font-weight: 600; }

        .btn-continue {
            background: var(--yonex-blue);
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
        }

        .btn-continue:hover {
            background: #004080;
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 8px 15px rgba(0, 45, 86, 0.2);
        }

        .btn-history {
            display: block;
            margin-top: 15px;
            color: var(--yonex-blue);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .print-link {
            display: block;
            margin-top: 20px;
            color: #94a3b8;
            font-size: 0.8rem;
            text-decoration: none;
            transition: 0.2s;
        }
        .print-link:hover { color: var(--yonex-blue); }
    </style>
</head>
<body>

<div class="success-card">
    <div class="check-container">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>

    <h2>Payment Successful!</h2>
    <p class="thank-you-msg">Your order has been confirmed. <br>Get ready to hit the court!</p>

    <div class="order-summary">
        <div class="summary-item">
            <span class="label">Transaction Status</span>
            <span class="value text-success">Approved</span>
        </div>
        <div class="summary-item">
            <span class="label">Order ID</span>
            <span class="value">#YNX-<?php echo $db_order_id; ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Date & Time</span>
            <span class="value"><?php echo date("d M Y, h:i A"); ?></span>
        </div>
        <hr style="border-top: 1px dashed #cbd5e1; margin: 12px 0;">
        <div class="summary-item">
            <span class="label" style="color: #1e293b; font-size: 1rem;">Total Paid</span>
            <span class="value" style="color: var(--yonex-blue); font-size: 1rem;">RM <?php echo number_format($amount, 2); ?></span>
        </div>  
    </div>

    <a href="../index.php" class="btn-continue">Continue Shopping</a>
    
    <a href="order_history.php" class="btn-history">View Order History</a>
    
    <a href="javascript:window.print()" class="print-link">
        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20" style="margin-right:4px;"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2 2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"></path></svg>
        Download E-Receipt
    </a>
</div>

</body>
</html>
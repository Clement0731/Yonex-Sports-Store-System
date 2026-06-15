<?php
session_start();
require_once 'db_config.php';

// 验证用户登录
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}
$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    die("Invalid Order ID.");
}

// 获取订单基本信息和用户Email (假设 users 表里有 email，如果没有会留空)
$order_sql = "SELECT o.*, u.email, u.username 
              FROM orders o 
              JOIN users u ON o.USER_ID = u.id 
              WHERE o.ORDER_ID = ? AND o.USER_ID = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows == 0) {
    die("Order not found or access denied.");
}
$order = $order_result->fetch_assoc();

// 分割地址信息
$shipping_lines = explode("\n", $order['shipping_address']);
$contact_info = isset($shipping_lines[0]) ? explode("|", $shipping_lines[0]) : ['N/A', 'N/A'];
$receiver_name = trim($contact_info[0]);
$receiver_phone = isset($contact_info[1]) ? trim($contact_info[1]) : 'N/A';
$address_detail = isset($shipping_lines[1]) ? trim($shipping_lines[1]) : 'N/A';

// 获取商品列表 (带有原价用于对比服务费)
$items_sql = "SELECT oi.*, p.name, p.price as base_price, pv.spec_value, 
              s1.option_name as string_name, IFNULL(s1.additional_price, 0) as string_price,
              s2.option_name as tension_name, IFNULL(s2.additional_price, 0) as tension_price
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              LEFT JOIN product_variants pv ON oi.variant_id = pv.id
              LEFT JOIN service_options s1 ON oi.string_option_id = s1.id
              LEFT JOIN service_options s2 ON oi.tension_option_id = s2.id
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($items_sql);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - YNX-<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --brand-color: #003366; }
        body { background-color: #f1f5f9; font-family: 'Inter', Arial, sans-serif; color: #334155; }
        
        .receipt-container { 
            max-width: 800px; 
            margin: 40px auto; 
            background: #ffffff; 
            padding: 50px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
            border-top: 8px solid var(--brand-color);
        }
        
        .brand-logo h2 { font-weight: 800; color: var(--brand-color); letter-spacing: -0.5px; margin: 0; }
        .doc-title { font-size: 2rem; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 2px; }
        
        .info-block { margin-top: 30px; }
        .info-title { font-size: 0.8rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .info-text { font-size: 0.95rem; line-height: 1.6; color: #1e293b; font-weight: 500; }
        
        .item-table { margin-top: 40px; width: 100%; border-collapse: collapse; }
        .item-table th { background: #f8fafc; padding: 12px 15px; font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
        .item-table td { padding: 15px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .item-title { font-weight: 700; color: #0f172a; display: block; margin-bottom: 4px; }
        .item-specs { font-size: 0.85rem; color: #64748b; line-height: 1.5; }
        
        .totals-box { width: 300px; margin-left: auto; margin-top: 20px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.95rem; color: #475569; }
        .totals-row.grand-total { border-top: 2px solid #e2e8f0; margin-top: 10px; padding-top: 15px; font-size: 1.25rem; font-weight: 800; color: var(--brand-color); }
        
        .status-badge { display: inline-block; padding: 6px 12px; background: #dcfce7; color: #166534; border-radius: 4px; font-weight: 700; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; }
        
        .action-buttons { text-align: center; margin-top: 30px; }
        .btn-print { background: var(--brand-color); color: white; border: none; padding: 10px 25px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-print:hover { background: #001f3f; }
        
        /* 打印时隐藏按钮和背景 */
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .receipt-container { box-shadow: none; margin: 0; padding: 20px; max-width: 100%; border-top: none; }
            .action-buttons { display: none; }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-4">
        <div class="brand-logo">
            <h2>YONEX OFFICIAL</h2>
            <span style="font-size: 0.85rem; color: #64748b;">Premium Badminton Equipment</span>
        </div>
        <div class="text-end">
            <div class="doc-title">RECEIPT</div>
            <div class="mt-2 text-muted fw-medium">Order #: YNX-<?php echo str_pad($order_id, 6, "0", STR_PAD_LEFT); ?></div>
        </div>
    </div>

    <div class="row info-block">
        <div class="col-sm-6 mb-4">
            <div class="info-title">Billed To / Shipping Address</div>
            <div class="info-text">
                <strong><?php echo htmlspecialchars($receiver_name); ?></strong><br>
                <?php echo htmlspecialchars($address_detail); ?><br>
                <i class="fas fa-phone fa-sm text-muted me-1 mt-2"></i> <?php echo htmlspecialchars($receiver_phone); ?><br>
                <?php if(!empty($order['email'])): ?>
                    <i class="fas fa-envelope fa-sm text-muted me-1"></i> <?php echo htmlspecialchars($order['email']); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-6 text-sm-end">
            <div class="info-title">Transaction Details</div>
            <div class="info-text">
                Date: <?php echo date('d M Y, H:i A', strtotime($order['ORDER_DATE'])); ?><br>
                Payment Method: <?php echo htmlspecialchars($order['payment_method']); ?><br>
                Status: <div class="status-badge mt-2"><i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($order['payment_status']); ?></div>
            </div>
        </div>
    </div>

    <table class="item-table">
        <thead>
            <tr>
                <th width="45%">Description</th>
                <th width="15%" class="text-center">Qty</th>
                <th width="20%" class="text-end">Unit Price</th>
                <th width="20%" class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_base_items = 0;
            $total_all_services = 0;

            while($item = $items_result->fetch_assoc()): 
                $line_total = $item['unit_price'] * $item['quantity'];
                
                // 计算差价 (用来显示印字服务)
                $base_p = (float)$item['base_price'];
                $service_sum = (float)$item['string_price'] + (float)$item['tension_price'];
                $printing_fee = (float)$item['unit_price'] - ($base_p + $service_sum);
                
                $total_base_items += ($base_p * $item['quantity']);
                $total_all_services += (($service_sum + $printing_fee) * $item['quantity']);
            ?>
            <tr>
                <td>
                    <span class="item-title"><?php echo htmlspecialchars($item['name']); ?></span>
                    <div class="item-specs mt-1">
                        Variant: <strong><?php echo htmlspecialchars($item['spec_value']); ?></strong><br>
                        
                        <?php if($service_sum > 0 || $printing_fee >= 14): ?>
                            <div class="mt-2 p-2 bg-light rounded" style="border-left: 2px solid #003366; font-size: 0.8rem;">
                                <strong class="text-dark">Add-on Services:</strong><br>
                                <?php if(!empty($item['string_name'])): ?>
                                    - Stringing: <?php echo htmlspecialchars($item['string_name']); ?> (+RM <?php echo number_format($item['string_price'], 2); ?>)<br>
                                <?php endif; ?>
                                <?php if(!empty($item['tension_name'])): ?>
                                    - Tension: <?php echo htmlspecialchars($item['tension_name']); ?> (+RM <?php echo number_format($item['tension_price'], 2); ?>)<br>
                                <?php endif; ?>
                                <?php if($printing_fee >= 14): ?>
                                    - Name Printing (+RM 15.00)
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="text-center align-middle"><?php echo $item['quantity']; ?></td>
                <td class="text-end align-middle">
                    <span class="text-muted" style="font-size: 0.8rem;">Base: RM <?php echo number_format($base_p, 2); ?></span><br>
                    <strong class="text-dark">RM <?php echo number_format($item['unit_price'], 2); ?></strong>
                </td>
                <td class="text-end align-middle fw-bold text-dark">RM <?php echo number_format($line_total, 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="totals-box">
        <div class="totals-row">
            <span>Base Items Subtotal:</span>
            <span class="text-dark">RM <?php echo number_format($total_base_items, 2); ?></span>
        </div>
        <div class="totals-row">
            <span>Add-on Services:</span>
            <span class="text-dark">RM <?php echo number_format($total_all_services, 2); ?></span>
        </div>
        <div class="totals-row">
            <span>Shipping Fee:</span>
            <span class="text-dark">RM <?php echo number_format($order['shipping_fee'], 2); ?></span>
        </div>
        <div class="totals-row grand-total">
            <span>Total Paid:</span>
            <span>RM <?php echo number_format($order['TOTAL_PRICE'], 2); ?></span>
        </div>
    </div>
    
    <div class="mt-5 pt-4 border-top text-center text-muted" style="font-size: 0.8rem;">
        If you have any questions concerning this receipt, please contact <strong>support@yonex-store.com</strong>.<br>
        Thank you for shopping with YONEX!
    </div>
</div>

<div class="action-buttons mb-5">
    <button class="btn-print me-2" onclick="window.print()">
        <i class="fas fa-print me-1"></i> Print Receipt
    </button>
    <button class="btn btn-outline-secondary" onclick="window.close()">
        Close Window
    </button>
</div>

</body>
</html>
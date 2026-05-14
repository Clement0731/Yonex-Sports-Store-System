<?php
session_start();
require_once 'db_config.php';

// 查询所有订单，按日期从新到旧排列
$sql = "SELECT * FROM orders ORDER BY order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Order History | YONEX Official</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --yonex-blue: #002d56; }
        body { background-color: #f4f7f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .order-card { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
            margin-bottom: 20px; 
            background: white;
            overflow: hidden;
        }
        .order-header { 
            background: #f8f9fa; 
            padding: 15px 20px; 
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-pending { color: #f39c12; font-weight: bold; }
        .status-completed { color: #27ae60; font-weight: bold; }
        .payment-method-box {
            display: inline-block;
            background: #eef2f7;
            color: #555;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-top: 10px;
            border: 1px solid #d1d9e6;
        }
        .item-details {
            color: #333;
            font-size: 1rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h3 class="mb-4 fw-bold" style="color: var(--yonex-blue);">My Order History</h3>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php 
                // 解析数据库中的 product_details 字段
                // 逻辑：以 "via" 为分界点拆分物品名和付款方式
                $raw_data = $row['product_details'];
                $data_parts = explode('via', $raw_data);
                
                $item_info = trim($data_parts[0]); // 物品信息
                $pay_info = isset($data_parts[1]) ? trim($data_parts[1]) : 'Online Payment'; // 付款方式
            ?>
            <div class="card order-card">
                <div class="order-header">
                    <div>
                        <span class="fw-bold">Order #<?php echo $row['id']; ?></span>
                        <span class="text-muted ms-3 small"><?php echo $row['order_date']; ?></span>
                    </div>
                    <div class="<?php echo ($row['status'] == 'Completed') ? 'status-completed' : 'status-pending'; ?>">
                        ● <?php echo $row['status']; ?>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Items Information</label>
                            <div class="item-details">
                                <?php echo nl2br(htmlspecialchars($item_info)); ?>
                            </div>
                            
                            <div class="payment-method-box">
                                <strong>Payment Method:</strong> <?php echo htmlspecialchars($pay_info); ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-end d-flex flex-column justify-content-center">
                            <div class="text-muted small">Amount Paid</div>
                            <div class="h4 fw-bold" style="color: var(--yonex-blue);">
                                RM <?php echo number_format($row['total_amount'], 2); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-5 card border-0 shadow-sm">
            <p class="text-muted mb-0">No order history found.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
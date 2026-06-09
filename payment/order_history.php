<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_register/login_page.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE USER_ID = '$user_id' ORDER BY ORDER_DATE DESC";
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
            border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
            margin-bottom: 20px; background: white; overflow: hidden;
        }
        .order-header { 
            background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #eee;
            display: flex; justify-content: space-between; align-items: center;
        }
        .status-pending { color: #f39c12; font-weight: bold; }
        .status-completed { color: #27ae60; font-weight: bold; }
        .item-details { color: #444; font-size: 0.95rem; line-height: 1.6; background: #fafbfc; padding: 15px; border-radius: 8px; border: 1px solid #eef2f7; }
        .detailed-order-info .badge { font-weight: 600; padding: 6px 10px; font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0" style="color: var(--yonex-blue);">My Order History</h3>
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">← Back to Store</a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php 
                $raw_data = $row['ORDER_DESC'];
                
                // 智能判断：如果是新版本的详细订单，直接显示；如果是旧版本的，做兼容处理
                if (strpos($raw_data, 'detailed-order-info') !== false) {
                    $display_content = $raw_data;
                } else {
                    $data_parts = explode('via', $raw_data);
                    $item_info = trim($data_parts[0]); 
                    $pay_info = isset($data_parts[1]) ? trim($data_parts[1]) : 'Online Payment'; 
                    $display_content = "<div class='mb-2'><span class='badge bg-secondary mb-1'>Items</span><br>" . nl2br(htmlspecialchars($item_info)) . "</div>";
                    $display_content .= "<div><span class='badge bg-secondary mb-1'>Payment Method</span><br>" . htmlspecialchars($pay_info) . "</div>";
                }
            ?>
            <div class="card order-card">
                <div class="order-header">
                    <div>
                        <span class="fw-bold">Order #<?php echo $row['ORDER_ID']; ?></span>
                        <span class="text-muted ms-3 small"><?php echo $row['ORDER_DATE']; ?></span>
                    </div>
                    <div class="<?php echo ($row['STATUS'] == 'Completed') ? 'status-completed' : 'status-pending'; ?>">
                        ● <?php echo $row['STATUS']; ?>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Order Details</label>
                            <div class="item-details">
                                <?php echo $display_content; ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-end d-flex flex-column justify-content-center">
                            <div class="text-muted small">Amount Paid</div>
                            <div class="h4 fw-bold" style="color: var(--yonex-blue);">
                                RM <?php echo number_format($row['TOTAL_PRICE'], 2); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-5 card border-0 shadow-sm">
            <p class="text-muted mb-0">No order history found.</p>
            <a href="../index.php" class="btn btn-primary mt-3" style="background:var(--yonex-blue); border:none;">Go Shopping</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
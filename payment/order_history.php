<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../login_register/login_page.php");
    exit();
}
$user_id = $_SESSION['user_id'];

try {
    $conn->query("UPDATE `orders` SET `expected_delivery` = DATE_ADD(ORDER_DATE, INTERVAL 2 DAY) WHERE `expected_delivery` IS NULL");
    $conn->query("UPDATE `orders` SET `STATUS` = 'Completed' WHERE `STATUS` NOT IN ('Completed', 'Cancelled') AND NOW() >= DATE_ADD(ORDER_DATE, INTERVAL 2 DAY)");
    $conn->query("UPDATE `orders` SET `STATUS` = 'Shipped', `tracking_number` = CONCAT('NJX', DATE_FORMAT(ORDER_DATE, '%d%m'), 'MY', ORDER_ID) WHERE `STATUS` NOT IN ('Completed', 'Shipped', 'Cancelled') AND NOW() >= DATE_ADD(ORDER_DATE, INTERVAL 1 DAY)");
    $conn->query("UPDATE `orders` SET `STATUS` = 'Processing' WHERE `STATUS` IN ('Pending', 'Paid') AND NOW() >= DATE_ADD(ORDER_DATE, INTERVAL 5 MINUTE)");
} catch (Exception $e) {}

$sql = "SELECT * FROM `orders` WHERE `USER_ID` = '$user_id' ORDER BY ORDER_ID DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Tracking | YONEX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --premium-navy: #002d56; --premium-muted: #64748b; --premium-border: #e2e8f0; }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: #1e293b; }
        .page-title { color: var(--premium-navy); font-size: 1.5rem; font-weight: 700; letter-spacing: -0.03em; }
        .order-card { border: 1px solid var(--premium-border); margin-bottom: 30px; background: white; }
        .order-header { background: #ffffff; padding: 20px 25px; border-bottom: 1px solid var(--premium-border); display: flex; justify-content: space-between; align-items: center; }
        .order-id-text { font-size: 1rem; font-weight: 600; color: var(--premium-navy); }
        .item-details { color: #334155; font-size: 0.9rem; line-height: 1.7; background: #f8fafc; padding: 20px; border: 1px solid var(--premium-border); }
        .status-text-tag { font-size: 0.85rem; font-weight: 700; color: var(--premium-navy); text-transform: uppercase; border-bottom: 2px solid var(--premium-navy); padding-bottom: 2px; }
        .status-completed-tag { color: var(--premium-muted); border-bottom: 2px solid var(--premium-muted); }
        .tracking-container { padding: 35px 25px 20px; border-top: 1px dashed var(--premium-border); margin-top: 10px; }
        .track-stepper { display: flex; justify-content: space-between; position: relative; margin-bottom: 25px; }
        .track-stepper::before { content: ''; position: absolute; top: 7px; left: 12%; right: 12%; height: 1px; background: var(--premium-border); z-index: 1; }
        .step { position: relative; z-index: 2; text-align: center; width: 25%; }
        .step .node { width: 14px; height: 14px; border-radius: 50%; background: #ffffff; border: 2px solid var(--premium-border); margin: 0 auto 12px; }
        .step.active .node { background: var(--premium-navy); border-color: var(--premium-navy); transform: scale(1.2); }
        .step.completed .node { background: #0f172a; border-color: #0f172a; }
        .step .text { font-size: 0.75rem; font-weight: 500; color: var(--premium-muted); text-transform: uppercase; }
        .step.active .text { color: var(--premium-navy); font-weight: 700; }
        .step.completed .text { color: #0f172a; font-weight: 600; }
        .logistics-info { display: flex; justify-content: space-between; background: #ffffff; padding: 15px 0; border-top: 1px solid var(--premium-border); align-items: center; }
        .info-block-label { display: block; color: var(--premium-muted); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
        .info-block-value { font-size: 0.85rem; font-weight: 600; color: #0f172a; }
        .track-num { font-family: monospace; font-size: 0.9rem; font-weight: 700; color: var(--premium-navy); }
        .info-heading { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--premium-muted); margin-bottom: 8px; display: block; border-left: 2px solid var(--premium-navy); padding-left: 8px; }
    </style>
</head>
<body>
<div class="container py-5" style="max-width: 850px;">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="page-title">ORDER HISTORY</h3>
        <a href="../index.php" class="text-muted text-decoration-none small" style="letter-spacing: 0.05em;">RETURN TO SHOP</a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($raw_row = $result->fetch_assoc()): ?>
            <?php 
                $row = array_change_key_case($raw_row, CASE_UPPER);
                $o_id = $row['ORDER_ID'] ?? 'N/A';
                $o_date = $row['ORDER_DATE'] ?? null;
                $eta_val = $row['EXPECTED_DELIVERY'] ?? null;
                $o_price = $row['TOTAL_PRICE'] ?? 0;
                $status = ucfirst(strtolower(trim($row['STATUS'] ?? 'Pending')));
                $track_val = $row['TRACKING_NUMBER'] ?? '';
                
                $date_str = $o_date ? date('d M Y, h:i A', strtotime($o_date)) : 'N/A';
                $eta = $eta_val ? date('d M Y', strtotime($eta_val)) : 'Pending';

                $s1 = $s2 = $s3 = $s4 = '';
                if (in_array($status, ['Pending', 'Paid'])) {
                    $s1 = 'active'; 
                } elseif (in_array($status, ['Processing', 'Shipped', 'Completed'])) {
                    $s1 = 'completed'; 
                    if ($status == 'Processing') { $s2 = 'active'; } 
                    else { 
                        $s2 = 'completed'; 
                        if ($status == 'Shipped') { $s3 = 'active'; } 
                        else { $s3 = 'completed'; $s4 = 'completed'; }
                    }
                }
            ?>
            <div class="card order-card" style="border-radius: 0px;">
                <div class="order-header">
                    <div>
                        <span class="order-id-text">YNX-<?php echo $o_id; ?></span>
                        <span class="text-muted ms-3 small" style="font-size: 0.8rem;"><?php echo $date_str; ?></span>
                    </div>
                    <div>
                        <span class="status-text-tag <?php echo ($status=='Completed') ? 'status-completed-tag' : ''; ?>">
                            <?php echo $status; ?>
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 pt-0">
                    <div class="tracking-container">
                        <div class="track-stepper">
                            <div class="step <?php echo $s1; ?>"><div class="node"></div><div class="text">Ordered</div></div>
                            <div class="step <?php echo $s2; ?>"><div class="node"></div><div class="text">Processing</div></div>
                            <div class="step <?php echo $s3; ?>"><div class="node"></div><div class="text">Shipped</div></div>
                            <div class="step <?php echo $s4; ?>"><div class="node"></div><div class="text">Delivered</div></div>
                        </div>
                        
                        <div class="logistics-info">
                            <div>
                                <span class="info-block-label">Expected Delivery</span>
                                <span class="info-block-value"><?php echo $eta; ?></span>
                            </div>
                            <div class="text-end">
                                <span class="info-block-label">Tracking Number</span>
                                <?php if(!empty($track_val)): ?>
                                    <span class="track-num"><?php echo htmlspecialchars($track_val); ?></span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic" style="font-size: 0.8rem;">Pending allocation</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-9">
                            <div class="item-details" style="border-radius: 0px;">
                                <span class='info-heading'>Items Purchased</span>
                                <?php 
                                $items_sql = "SELECT oi.quantity, p.name, pv.spec_value, s1.option_name as string_name, s2.option_name as tension_name 
                                              FROM order_items oi
                                              JOIN products p ON oi.product_id = p.id
                                              JOIN product_variants pv ON oi.variant_id = pv.id
                                              LEFT JOIN service_options s1 ON oi.string_option_id = s1.id
                                              LEFT JOIN service_options s2 ON oi.tension_option_id = s2.id
                                              WHERE oi.order_id = '$o_id'";
                                $items_res = $conn->query($items_sql);
                                
                                if($items_res && $items_res->num_rows > 0) {
                                    while($item = $items_res->fetch_assoc()) {
                                        echo "• <b>" . htmlspecialchars($item['name']) . "</b> (" . htmlspecialchars($item['spec_value']) . ") <span style='color:var(--premium-navy);'><b>x" . $item['quantity'] . "</b></span><br>";
                                        if(!empty($item['string_name'])) {
                                            echo "&nbsp;&nbsp;<small class='text-muted'>[Service: " . htmlspecialchars($item['string_name']) . ", " . htmlspecialchars($item['tension_name']) . "]</small><br>";
                                        }
                                    }
                                } else {
                                    echo "<span class='text-muted' style='font-style:italic;'>Standard Product Pack (Legacy Data Mapping)</span>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3 text-end d-flex flex-column justify-content-center">
                            <span class="info-block-label">Total Amount</span>
                            <div class="fs-4 fw-bold" style="color: var(--premium-navy); letter-spacing: -0.02em;">
                                RM <?php echo number_format((float)$o_price, 2); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-5 card order-card" style="border-radius: 0px;">
            <h5 class="text-muted mb-0" style="letter-spacing: 0.05em;">NO ORDERS RECORDED</h5>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
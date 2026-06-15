<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

try {
    $conn->query("UPDATE `orders` SET `expected_delivery` = DATE_ADD(ORDER_DATE, INTERVAL 2 DAY) WHERE `expected_delivery` IS NULL");
    $conn->query("UPDATE `orders` SET `STATUS` = 'Completed' WHERE `STATUS` NOT IN ('Completed', 'Cancelled') AND NOW() >= DATE_ADD(ORDER_DATE, INTERVAL 2 DAY)");
    $conn->query("UPDATE `orders` SET `STATUS` = 'Shipped', `tracking_number` = CONCAT('NJX', DATE_FORMAT(ORDER_DATE, '%d%m'), 'MY', ORDER_ID) WHERE `STATUS` NOT IN ('Completed', 'Shipped', 'Cancelled') AND NOW() >= DATE_ADD(ORDER_DATE, INTERVAL 1 DAY)");
    $conn->query("UPDATE `orders` SET `STATUS` = 'Processing' WHERE `STATUS` IN ('Pending', 'Paid') AND NOW() >= DATE_ADD(ORDER_DATE, INTERVAL 5 MINUTE)");
} catch (Exception $e) {}

if(isset($_POST['update_shipping'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['order_status'];
    $tracking_no = $_POST['tracking_number'];
    $search = $_POST['current_search'] ?? '';
    $conn->query("UPDATE `orders` SET `STATUS` = '$status', `tracking_number` = '$tracking_no' WHERE `ORDER_ID` = '$order_id'");
    header("Location: order.php?search=" . urlencode($search));
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logistics & Fulfillment Center | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root { --premium-navy: #002d56; --slate-dark: #0f172a; --slate-muted: #64748b; --border-fine: #e2e8f0; }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--slate-dark); }
        .main-content { padding: 40px; width: 100%; }
        .header-title { font-size: 1.6rem; font-weight: 800; color: var(--premium-navy); letter-spacing: -0.02em; text-transform: uppercase; margin-bottom: 25px; }
        
        .search-container { display: flex; gap: 10px; margin-bottom: 25px; background: #ffffff; padding: 15px; border: 1px solid var(--border-fine); }
        .search-input { padding: 10px 15px; border: 1px solid var(--border-fine); font-size: 0.9rem; width: 320px; outline: none; }
        .btn-search { background: var(--premium-navy); color: white; border: none; padding: 10px 24px; font-size: 0.85rem; font-weight: 700; cursor: pointer; text-transform: uppercase; }
        .btn-clear { background: #f1f5f9; color: var(--slate-dark); border: 1px solid var(--border-fine); padding: 10px 20px; font-size: 0.85rem; font-weight: 700; text-decoration: none; text-transform: uppercase; display: flex; align-items: center; }

        .fulfillment-box { background: #f8fafc; border: 1px solid var(--border-fine); padding: 15px 20px; font-size: 0.85rem; line-height: 1.6; text-align: left; color: #334155; }
        .info-heading { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--slate-muted); margin-top: 12px; margin-bottom: 4px; display: block; border-left: 2px solid var(--premium-navy); padding-left: 6px; }
        .fulfillment-box div:first-of-type .info-heading { margin-top: 0; }
        .status-tag { font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 1px; display: inline-block; }
        .btn-override { background: var(--premium-navy); color: white; border: none; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; }
        .table-box th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--slate-muted); padding: 15px 20px; }
        .table-box td { padding: 20px; vertical-align: top; border-bottom: 1px solid var(--border-fine); }
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: #fff; padding: 30px; width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); position: relative; border: 1px solid var(--border-fine); }
        .close-btn { position: absolute; right: 20px; top: 20px; font-size: 20px; cursor: pointer; color: #888; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--premium-navy); margin-bottom: 8px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid var(--border-fine); box-sizing: border-box; font-size: 0.9rem; outline: none; }
        .btn-submit { width: 100%; background: var(--premium-navy); color: white; padding: 14px; border: none; font-weight: 700; text-transform: uppercase; cursor: pointer; }
        
        .user-status-badge { font-size: 0.65rem; font-weight: 700; padding: 3px 6px; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-top: 6px;}
        .badge-active { color: #10b981; background: #ecfdf5; border: 1px solid #6ee7b7; }
        .badge-deactivated { color: #ef4444; background: #fef2f2; border: 1px solid #fca5a5; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1 class="header-title">Logistics & Fulfillment Center</h1>

        <form method="GET" action="" class="search-container">
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Order ID, name, address...">
            <button type="submit" class="btn-search">Search</button>
            <?php if (!empty($search)): ?>
                <a href="order.php" class="btn-clear">Clear</a>
            <?php endif; ?>
        </form>

        <div class="table-box" style="background: #ffffff; border: 1px solid var(--border-fine);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-fine);">
                        <th style="text-align: left; width: 15%;">Order Reference</th>
                        <th style="text-align: left; width: 18%;">Customer Identity</th>
                        <th style="text-align: left; width: 45%;">Fulfillment & Dispatch Specifications</th>
                        <th style="text-align: left; width: 12%;">Net Revenue</th>
                        <th style="text-align: right; width: 10%;">Dispatch Control</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_query = "";
                    if (!empty($search)) {
                        $clean_search = $conn->real_escape_string($search);
                        $numeric_order_id = (int)str_ireplace('YNX-', '', $clean_search);
                        $search_query = " WHERE (o.ORDER_ID = '$numeric_order_id' OR u.username LIKE '%$clean_search%' OR o.SHIPPING_ADDRESS LIKE '%$clean_search%' OR o.PAYMENT_METHOD LIKE '%$clean_search%')";
                    }

                    $sql = "SELECT o.*, u.username, u.status AS user_status FROM `orders` o LEFT JOIN `users` u ON o.USER_ID = u.id $search_query ORDER BY o.ORDER_ID DESC";
                    $orders = $conn->query($sql);
                    if ($orders && $orders->num_rows > 0) {
                        while($raw_row = $orders->fetch_assoc()) {
                            $row = array_change_key_case($raw_row, CASE_UPPER);
                            $o_id = $row['ORDER_ID'];
                            $status = ucfirst(strtolower(trim($row['STATUS'] ?? 'Pending')));
                            $track_no = !empty($row['TRACKING_NUMBER']) ? $row['TRACKING_NUMBER'] : 'Awaiting Allocation';
                            
                            $user_status = $row['USER_STATUS'] ?? 'Active';
                            $badge_class = ($user_status === 'Deactivated') ? 'badge-deactivated' : 'badge-active';
                            
                            echo "<tr>
                                    <td>
                                        <div style='font-weight: 700; color: var(--premium-navy);'>YNX-{$o_id}</div>
                                        <div style='color: var(--slate-muted); font-size: 0.75rem; margin-top: 4px;'>".date('d M Y', strtotime($row['ORDER_DATE']))."</div>
                                    </td>
                                    <td>
                                        <div style='font-weight: 700;'>".htmlspecialchars($row['USERNAME'] ?? 'Guest Account')."</div>
                                        <div style='font-size:0.75rem; color:var(--slate-muted); margin-top:4px;'>Client ID: #".$row['USER_ID']."</div>
                                        <div class='user-status-badge {$badge_class}'>{$user_status}</div>
                                    </td>
                                    <td>
                                        <div class='fulfillment-box'>
                                            <span class='info-heading'>Items Purchased</span>";
                            
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
                                    echo "• <b>" . htmlspecialchars($item['name']) . "</b> (" . htmlspecialchars($item['spec_value']) . ") <b>x" . $item['quantity'] . "</b><br>";
                                    if(!empty($item['string_name'])) {
                                        echo "&nbsp;&nbsp;<small style='color:var(--slate-muted);'>[Service: " . htmlspecialchars($item['string_name']) . " @ " . htmlspecialchars($item['tension_name']) . "]</small><br>";
                                    }
                                }
                            } else {
                                echo "<span style='color:var(--slate-muted); font-style:italic;'>Standard Product Pack (Legacy Data Mapping)</span><br>";
                            }

                            echo "          <span class='info-heading'>Delivery Coordinates</span>
                                            <div style='white-space: pre-line; color:var(--slate-dark);'>".htmlspecialchars($row['SHIPPING_ADDRESS'])."</div>
                                            
                                            <span class='info-heading'>Payment Method</span>
                                            <div>".htmlspecialchars($row['PAYMENT_METHOD'])."</div>

                                            <span class='info-heading'>Courier Assignment</span>
                                            <span style='font-family: monospace; font-weight: 700; color: var(--premium-navy);'>{$track_no}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style='font-weight: 700; color: #000;'>RM ".number_format((float)$row['TOTAL_PRICE'], 2)."</div>
                                        <div style='margin-top: 8px;'><span class='status-tag'>{$status}</span></div>
                                    </td>
                                    <td style='text-align: right;'>
                                        <button class='btn-override' onclick=\"openShipModal('{$o_id}', '{$status}', '{$track_no}')\">Override</button>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; padding: 40px; color: var(--slate-muted);'>No entries indexed.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="shipModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 style="font-size: 1.1rem; color: var(--premium-navy); margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid var(--border-fine); padding-bottom: 10px; font-weight:800;">LOGISTICS OVERRIDE</h2>
            <form method="POST" action="">
                <input type="hidden" name="order_id" id="modal_order_id">
                <input type="hidden" name="current_search" value="<?php echo htmlspecialchars($search); ?>">
                <div class="form-group">
                    <label>Fulfillment Milestone</label>
                    <select name="order_status" id="modal_status" required>
                        <option value="Paid">Paid</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Consignment Waybill Number</label>
                    <input type="text" name="tracking_number" id="modal_tracking" placeholder="e.g. NJX123456MY">
                </div>
                <button type="submit" name="update_shipping" class="btn-submit">Commit Changes</button>
            </form>
        </div>
    </div>

    <script>
        function openShipModal(id, status, tracking) {
            document.getElementById('modal_order_id').value = id;
            document.getElementById('modal_status').value = status;
            document.getElementById('modal_tracking').value = (tracking === 'Awaiting Allocation') ? '' : tracking;
            document.getElementById('shipModal').style.display = 'flex';
        }
        function closeModal() { document.getElementById('shipModal').style.display = 'none'; }
    </script>
</body>
</html>
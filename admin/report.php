<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$start_datetime = $conn->real_escape_string($start_date . ' 00:00:00');
$end_datetime = $conn->real_escape_string($end_date . ' 23:59:59');

// 1. Overall Summary
$summary_sql = "SELECT 
                    COUNT(ORDER_ID) as total_orders, 
                    SUM(TOTAL_PRICE) as gross_revenue, 
                    COUNT(DISTINCT USER_ID) as unique_customers 
                FROM `orders` 
                WHERE ORDER_DATE >= '$start_datetime' AND ORDER_DATE <= '$end_datetime' AND STATUS != 'Cancelled'";
$summary_res = $conn->query($summary_sql);
$summary = $summary_res->fetch_assoc();

$gross_revenue = $summary['gross_revenue'] ? (float)$summary['gross_revenue'] : 0.00;
$total_orders = $summary['total_orders'] ? (int)$summary['total_orders'] : 0;
$unique_customers = $summary['unique_customers'] ? (int)$summary['unique_customers'] : 0;

// 2. Items Sold Breakdown
$items_sql = "SELECT 
                p.name, 
                p.category, 
                pv.spec_value, 
                SUM(oi.quantity) as total_qty, 
                SUM(oi.subtotal) as total_revenue 
              FROM order_items oi 
              JOIN orders o ON oi.order_id = o.ORDER_ID 
              JOIN products p ON oi.product_id = p.id 
              JOIN product_variants pv ON oi.variant_id = pv.id 
              WHERE o.ORDER_DATE >= '$start_datetime' AND o.ORDER_DATE <= '$end_datetime' AND o.STATUS != 'Cancelled' 
              GROUP BY p.id, pv.id 
              ORDER BY total_qty DESC, total_revenue DESC";
$items_res = $conn->query($items_sql);

$total_items_sold = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enterprise Reports & Analytics | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root { --premium-navy: #002d56; --slate-dark: #0f172a; --slate-muted: #64748b; --border-fine: #e2e8f0; }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--slate-dark); }
        .main-content { padding: 40px; width: 100%; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-fine); padding-bottom: 20px; margin-bottom: 30px; }
        .header-title { font-size: 1.6rem; font-weight: 800; color: var(--premium-navy); letter-spacing: -0.02em; text-transform: uppercase; margin: 0; }
        
        .filter-box { background: #ffffff; border: 1px solid var(--border-fine); padding: 20px; margin-bottom: 30px; display: flex; align-items: flex-end; gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--slate-muted); letter-spacing: 0.05em; }
        .form-group input { padding: 10px 15px; border: 1px solid var(--border-fine); outline: none; font-family: inherit; font-size: 0.9rem; }
        .btn-generate { background: var(--premium-navy); color: white; border: none; padding: 11px 24px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: 0.2s; }
        .btn-generate:hover { background: #001f3f; }
        .btn-print { background: transparent; color: var(--premium-navy); border: 1px solid var(--premium-navy); padding: 11px 24px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: 0.2s; }
        .btn-print:hover { background: var(--premium-navy); color: white; }

        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; }
        .metric-card { background: #ffffff; border: 1px solid var(--border-fine); padding: 25px; border-top: 4px solid var(--premium-navy); }
        .metric-label { font-size: 0.7rem; font-weight: 700; color: var(--slate-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px; display: block; }
        .metric-value { font-size: 1.8rem; font-weight: 800; color: var(--slate-dark); }

        .report-section { background: #ffffff; border: 1px solid var(--border-fine); margin-bottom: 40px; }
        .section-header { background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid var(--border-fine); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--premium-navy); }
        .table-box { width: 100%; border-collapse: collapse; }
        .table-box th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-muted); padding: 15px 20px; text-align: left; border-bottom: 1px solid var(--border-fine); background: #ffffff; }
        .table-box td { padding: 15px 20px; border-bottom: 1px solid var(--border-fine); font-size: 0.85rem; }
        .table-box tr:last-child td { border-bottom: none; }

        @media print {
            .sidebar, .filter-box, .btn-print { display: none !important; }
            .main-content { padding: 0 !important; width: 100% !important; margin: 0 !important; }
            body { background: white; }
            .metric-card, .report-section { border: 1px solid #ccc; break-inside: avoid; }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        
        <div class="header-flex">
            <h1 class="header-title">Enterprise Analytics Report</h1>
            <button class="btn-print" onclick="window.print()">Export / Print PDF</button>
        </div>

        <form class="filter-box" method="GET" action="">
            <div class="form-group">
                <label>Period Start Date</label>
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
            </div>
            <div class="form-group">
                <label>Period End Date</label>
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
            </div>
            <button type="submit" class="btn-generate">Generate Report</button>
        </form>

        <div class="metrics-grid">
            <div class="metric-card">
                <span class="metric-label">Gross Revenue</span>
                <div class="metric-value">RM <?php echo number_format($gross_revenue, 2); ?></div>
            </div>
            <div class="metric-card">
                <span class="metric-label">Completed Orders</span>
                <div class="metric-value"><?php echo number_format($total_orders); ?></div>
            </div>
            <div class="metric-card">
                <span class="metric-label">Active Customers</span>
                <div class="metric-value"><?php echo number_format($unique_customers); ?></div>
            </div>
            <div class="metric-card">
                <span class="metric-label">Total Units Sold</span>
                <div class="metric-value" id="total_units_display">0</div>
            </div>
        </div>

        <div class="report-section">
            <div class="section-header">Itemized Sales Breakdown</div>
            <table class="table-box">
                <thead>
                    <tr>
                        <th style="width: 35%;">Product Name</th>
                        <th style="width: 15%;">Category</th>
                        <th style="width: 25%;">Variant / Specification</th>
                        <th style="width: 10%; text-align: center;">Units Sold</th>
                        <th style="width: 15%; text-align: right;">Net Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($items_res && $items_res->num_rows > 0) {
                        while($item = $items_res->fetch_assoc()) {
                            $total_items_sold += (int)$item['total_qty'];
                            echo "<tr>
                                    <td style='font-weight: 600; color: var(--premium-navy);'>".htmlspecialchars($item['name'])."</td>
                                    <td><span style='background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;'>".htmlspecialchars($item['category'])."</span></td>
                                    <td style='color: var(--slate-muted); font-size: 0.8rem;'>".htmlspecialchars($item['spec_value'])."</td>
                                    <td style='text-align: center; font-weight: 700;'>".htmlspecialchars($item['total_qty'])."</td>
                                    <td style='text-align: right; font-weight: 700; color: #000;'>RM ".number_format((float)$item['total_revenue'], 2)."</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; padding: 40px; color: var(--slate-muted);'>No transactional data available for the selected period.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            document.getElementById('total_units_display').innerText = "<?php echo number_format($total_items_sold); ?>";
        </script>

        <div class="report-section">
            <div class="section-header">Detailed Transaction Log</div>
            <table class="table-box">
                <thead>
                    <tr>
                        <th style="width: 15%;">Order ID</th>
                        <th style="width: 20%;">Date & Time</th>
                        <th style="width: 25%;">Customer Identity</th>
                        <th style="width: 20%;">Payment Channel</th>
                        <th style="width: 20%; text-align: right;">Transaction Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $log_sql = "SELECT o.ORDER_ID, o.ORDER_DATE, o.PAYMENT_METHOD, o.TOTAL_PRICE, u.username 
                                FROM `orders` o 
                                LEFT JOIN `users` u ON o.USER_ID = u.id 
                                WHERE o.ORDER_DATE >= '$start_datetime' AND o.ORDER_DATE <= '$end_datetime' AND o.STATUS != 'Cancelled' 
                                ORDER BY o.ORDER_DATE DESC";
                    $log_res = $conn->query($log_sql);

                    if ($log_res && $log_res->num_rows > 0) {
                        while($log = $log_res->fetch_assoc()) {
                            echo "<tr>
                                    <td style='font-weight: 700;'>YNX-{$log['ORDER_ID']}</td>
                                    <td style='color: var(--slate-muted); font-size: 0.8rem;'>".date('d M Y, H:i', strtotime($log['ORDER_DATE']))."</td>
                                    <td>".htmlspecialchars($log['username'] ?? 'Guest')."</td>
                                    <td><span style='background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;'>".htmlspecialchars($log['PAYMENT_METHOD'])."</span></td>
                                    <td style='text-align: right; font-weight: 700; color: #000;'>RM ".number_format((float)$log['TOTAL_PRICE'], 2)."</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; padding: 40px; color: var(--slate-muted);'>No transactional logs recorded.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
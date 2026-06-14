<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// 🚀 Bulletproof Numerical Fetching: 彻底无视列名大小写，直接通过位置索引提取数据
$sales_q = $conn->query("SELECT SUM(TOTAL_PRICE) FROM `orders` WHERE `STATUS` != 'Cancelled'");
$total_sales = ($sales_q && $sales_q->num_rows > 0) ? (float)$sales_q->fetch_row()[0] : 0.00;

$orders_q = $conn->query("SELECT COUNT(*) FROM `orders`");
$total_orders = ($orders_q && $orders_q->num_rows > 0) ? (int)$orders_q->fetch_row()[0] : 0;

$cust_q = $conn->query("SELECT COUNT(*) FROM `users`");
$total_cust = ($cust_q && $cust_q->num_rows > 0) ? (int)$cust_q->fetch_row()[0] : 0;

$prod_q = $conn->query("SELECT COUNT(*) FROM `products`");
$total_products = ($prod_q && $prod_q->num_rows > 0) ? (int)$prod_q->fetch_row()[0] : 0;

// 图表数据同样采用位置索引安全提取
$chart_labels = [];
$chart_data = [];
$trend_q = $conn->query("SELECT DATE(ORDER_DATE), SUM(TOTAL_PRICE) FROM `orders` WHERE `STATUS` != 'Cancelled' GROUP BY DATE(ORDER_DATE) ORDER BY DATE(ORDER_DATE) ASC LIMIT 7");
if ($trend_q && $trend_q->num_rows > 0) {
    while($row = $trend_q->fetch_row()) {
        $chart_labels[] = date('d M', strtotime($row[0]));
        $chart_data[] = (float)$row[1];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Executive Dashboard | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --navy-base: #002d56; --dark-slate: #0f172a; --text-muted: #64748b; --border-fine: #e2e8f0; }
        body { background-color: #fafafa; color: var(--dark-slate); font-family: -apple-system, BlinkMacSystemFont, sans-serif; }
        .main-content { padding: 40px; width: 100%; }
        .dashboard-header { border-bottom: 1px solid var(--border-fine); padding-bottom: 20px; margin-bottom: 35px; }
        .dashboard-title { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.02em; color: var(--navy-base); text-transform: uppercase; }
        .dashboard-subtitle { font-size: 0.85rem; color: var(--text-muted); letter-spacing: 0.05em; text-transform: uppercase; margin-top: 4px; }
        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 40px; }
        .metric-card { background: #ffffff; border: 1px solid var(--border-fine); padding: 25px 30px; position: relative; }
        .metric-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; display: block; }
        .metric-value { font-size: 1.8rem; font-weight: 700; letter-spacing: -0.03em; color: var(--dark-slate); line-height: 1.2; }
        .analytics-section { display: grid; grid-template-columns: 1.6fr 1fr; gap: 30px; }
        .block-container { background: #ffffff; border: 1px solid var(--border-fine); padding: 30px; }
        .block-title { font-size: 0.85rem; font-weight: 700; letter-spacing: 0.06em; color: var(--navy-base); text-transform: uppercase; border-left: 2px solid var(--navy-base); padding-left: 10px; margin-bottom: 25px; }
        .recent-order-row { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid var(--border-fine); }
        .recent-order-row:last-child { border-bottom: none; }
        .order-meta-id { font-size: 0.85rem; font-weight: 700; color: var(--navy-base); }
        .order-meta-cust { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }
        .order-meta-price { font-size: 0.9rem; font-weight: 700; color: var(--dark-slate); text-align: right; }
        .order-meta-status { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; margin-top: 3px; }
        .view-all-link { display: inline-block; font-size: 0.8rem; font-weight: 700; color: var(--navy-base); text-decoration: none; letter-spacing: 0.05em; text-transform: uppercase; margin-top: 20px; transition: color 0.2s; }
        .view-all-link:hover { color: #001f3f; text-decoration: underline; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Executive Overview</h1>
            <p class="dashboard-subtitle">Enterprise Administration Control Panel</p>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <span class="metric-label">Gross Revenue</span>
                <div class="metric-value">RM <?php echo number_format($total_sales, 2); ?></div>
            </div>
            <div class="metric-card">
                <span class="metric-label">Total Transactions</span>
                <div class="metric-value"><?php echo number_format($total_orders); ?></div>
            </div>
            <div class="metric-card">
                <span class="metric-label">Registered Clients</span>
                <div class="metric-value"><?php echo number_format($total_cust); ?></div>
            </div>
            <div class="metric-card">
                <span class="metric-label">Active Inventory Assets</span>
                <div class="metric-value"><?php echo number_format($total_products); ?></div>
            </div>
        </div>

        <div class="analytics-section">
            <div class="block-container">
                <h2 class="block-title">Financial Performance Analytics (7 Days)</h2>
                <div style="width: 100%; height: 320px; position: relative;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="block-container">
                <h2 class="block-title">Recent Real-Time Activity</h2>
                <div class="recent-orders-list">
                    <?php
                    $recent_q = $conn->query("SELECT o.*, u.username FROM `orders` o LEFT JOIN `users` u ON o.USER_ID = u.id ORDER BY o.ORDER_ID DESC LIMIT 5");
                    if ($recent_q && $recent_q->num_rows > 0) {
                        while($raw_row = $recent_q->fetch_assoc()) {
                            $row = array_change_key_case($raw_row, CASE_UPPER);
                            
                            $status = strtoupper(trim($row['STATUS'] ?? 'PENDING'));
                            $status_color = ($status == 'COMPLETED') ? '#64748b' : '#002d56';
                            $o_id = $row['ORDER_ID'] ?? 'N/A';
                            $o_price = $row['TOTAL_PRICE'] ?? 0;
                            
                            echo "<div class='recent-order-row'>
                                    <div>
                                        <div class='order-meta-id'>YNX-{$o_id}</div>
                                        <div class='order-meta-cust'>".htmlspecialchars($row['USERNAME'] ?? 'Guest')."</div>
                                    </div>
                                    <div>
                                        <div class='order-meta-price'>RM ".number_format((float)$o_price, 2)."</div>
                                        <div class='order-meta-status' style='color: {$status_color};'>".$status."</div>
                                    </div>
                                  </div>";
                        }
                    } else {
                        echo "<p style='color:var(--text-muted); font-size:0.85rem; text-align:center; padding: 40px 0;'>No recent transaction data logs available.</p>";
                    }
                    ?>
                </div>
                <a href="order.php" class="view-all-link">Access Logistics Center →</a>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Revenue (RM)',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: '#002d56',
                    backgroundColor: 'rgba(0, 45, 86, 0.02)',
                    borderWidth: 1.5,
                    tension: 0.2,
                    fill: true,
                    pointBackgroundColor: '#0f172a',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', borderDash: [2, 2] },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    }
                }
            }
        });
    </script>
</body>
</html>
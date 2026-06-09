<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// 获取数据统计
$sales_q = $conn->query("SELECT SUM(TOTAL_PRICE) as total FROM ORDERS WHERE STATUS != 'Cancelled'");
$total_sales = ($sales_q && $sales_q->num_rows > 0) ? $sales_q->fetch_assoc()['total'] : 0.00;

$orders_q = $conn->query("SELECT COUNT(ORDER_ID) as count FROM ORDERS");
$total_orders = ($orders_q && $orders_q->num_rows > 0) ? $orders_q->fetch_assoc()['count'] : 0;

$cust_q = $conn->query("SELECT COUNT(id) as count FROM users");
$total_cust = ($cust_q && $cust_q->num_rows > 0) ? $cust_q->fetch_assoc()['count'] : 0;

$prod_q = $conn->query("SELECT COUNT(id) as count FROM products");
$total_products = ($prod_q && $prod_q->num_rows > 0) ? $prod_q->fetch_assoc()['count'] : 0;

// 获取图表数据
$chart_labels = [];
$chart_data = [];
$trend_q = $conn->query("SELECT DATE(ORDER_DATE) as order_day, SUM(TOTAL_PRICE) as daily_sales FROM ORDERS GROUP BY DATE(ORDER_DATE) ORDER BY DATE(ORDER_DATE) DESC LIMIT 7");

// 因为是倒序查的最新7天，所以要反转回来让图表从左到右显示时间
$temp_labels = []; $temp_data = [];
if($trend_q && $trend_q->num_rows > 0){
    while($row = $trend_q->fetch_assoc()) {
        $temp_labels[] = date('M d', strtotime($row['order_day']));
        $temp_data[] = $row['daily_sales'];
    }
    $chart_labels = array_reverse($temp_labels);
    $chart_data = array_reverse($temp_data);
} else {
    $chart_labels = [date('M d')];
    $chart_data = [0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Yonex Pro</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; margin-top: 25px; }
        .chart-box, .feed-box { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .feed-box h3, .chart-box h3 { color: #0033a0; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; font-weight: 900; text-transform: uppercase;}
        
        .recent-order-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px dashed #e2e8f0; }
        .recent-order-item:last-child { border-bottom: none; }
        .roi-left { display: flex; flex-direction: column; }
        .roi-id { font-weight: bold; color: #0f172a; font-size: 14px; }
        .roi-time { font-size: 12px; color: #64748b; margin-top: 4px; }
        .roi-right { font-weight: 900; color: #e60012; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1 style="color: #0033a0; font-weight: 900; text-transform: uppercase; margin-bottom: 30px;">System Overview</h1>
        
        <div class="stats-container" style="gap: 25px;">
            <div class="stat-box" style="border-top: 5px solid #e60012; background: linear-gradient(to bottom, #fff, #fffcfc);">
                <h4 style="text-transform: uppercase; letter-spacing: 1px; color: #64748b;">Total Revenue</h4>
                <p style="font-size: 28px; color: #e60012;">RM <?php echo number_format($total_sales, 2); ?></p>
            </div>
            <div class="stat-box" style="border-top: 5px solid #0033a0;">
                <h4 style="text-transform: uppercase; letter-spacing: 1px; color: #64748b;">Total Orders</h4>
                <p style="font-size: 28px; color: #0033a0;"><?php echo $total_orders; ?></p>
            </div>
            <div class="stat-box" style="border-top: 5px solid #f59e0b;">
                <h4 style="text-transform: uppercase; letter-spacing: 1px; color: #64748b;">Registered Customers</h4>
                <p style="font-size: 28px; color: #b45309;"><?php echo $total_cust; ?></p>
            </div>
            <div class="stat-box" style="border-top: 5px solid #10b981;">
                <h4 style="text-transform: uppercase; letter-spacing: 1px; color: #64748b;">Active Products</h4>
                <p style="font-size: 28px; color: #047857;"><?php echo $total_products; ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="chart-box">
                <h3>📈 7-Day Sales Trend</h3>
                <canvas id="salesChart" height="120"></canvas>
            </div>
            
            <div class="feed-box">
                <h3>🔔 Recent Orders</h3>
                <div class="feed-list">
                    <?php
                    $recent = $conn->query("SELECT o.ORDER_ID, o.ORDER_DATE, o.TOTAL_PRICE, u.username FROM ORDERS o LEFT JOIN USERS u ON o.USER_ID = u.id ORDER BY o.ORDER_ID DESC LIMIT 6");
                    if ($recent && $recent->num_rows > 0) {
                        while($r = $recent->fetch_assoc()) {
                            // 格式化时间为友好格式 (e.g., Today, 14:30)
                            $time = date('d M, h:i A', strtotime($r['ORDER_DATE']));
                            $name = $r['username'] ? $r['username'] : 'Guest';
                            echo "
                            <div class='recent-order-item'>
                                <div class='roi-left'>
                                    <span class='roi-id'>#YNX-{$r['ORDER_ID']} - {$name}</span>
                                    <span class='roi-time'>🕒 {$time}</span>
                                </div>
                                <div class='roi-right'>RM ".number_format($r['TOTAL_PRICE'], 2)."</div>
                            </div>
                            ";
                        }
                    } else {
                        echo "<p style='color:#94a3b8; font-size:14px; text-align:center; padding:20px 0;'>No recent orders yet.</p>";
                    }
                    ?>
                </div>
                <a href="order.php" style="display:block; text-align:center; margin-top:15px; font-size:13px; font-weight:bold; color:#0033a0; text-decoration:none;">View All Orders →</a>
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
                    label: 'Daily Sales (RM)',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: '#0033a0',
                    backgroundColor: 'rgba(0, 51, 160, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#e60012',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false } // 隐藏多余的图例，看起来更干净
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
</body>
</html>
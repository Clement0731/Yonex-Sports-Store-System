<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$sales_q = $conn->query("SELECT SUM(TOTAL_PRICE) as total FROM ORDERS WHERE STATUS != 'Cancelled'");
$sales_data = $sales_q->fetch_assoc();
$total_sales = $sales_data['total'] ? $sales_data['total'] : 0.00;

$orders_q = $conn->query("SELECT COUNT(ORDER_ID) as count FROM ORDERS");
$orders_data = $orders_q->fetch_assoc();
$total_orders = $orders_data['count'];

$cust_q = $conn->query("SELECT COUNT(USER_ID) as count FROM admin WHERE ROLE = 'Customer'");
$cust_data = $cust_q->fetch_assoc();
$total_cust = $cust_data['count'];

$prod_q = $conn->query("SELECT COUNT(id) as count FROM products");
$prod_data = $prod_q->fetch_assoc();
$total_products = $prod_data['count'];

$chart_labels = [];
$chart_data = [];
$trend_q = $conn->query("SELECT DATE(ORDER_DATE) as order_day, SUM(TOTAL_PRICE) as daily_sales FROM ORDERS GROUP BY DATE(ORDER_DATE) ORDER BY DATE(ORDER_DATE) ASC LIMIT 7");

while($row = $trend_q->fetch_assoc()) {
    $chart_labels[] = $row['order_day'];
    $chart_data[] = $row['daily_sales'];
}

if (empty($chart_labels)) {
    $chart_labels = [date('Y-m-d')];
    $chart_data = [0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1>Dashboard Overview</h1>
        <br>
        <div class="stats-container">
            <div class="stat-box"><h4>Total Revenue</h4><p>RM <?php echo number_format($total_sales, 2); ?></p></div>
            <div class="stat-box"><h4>Total Orders</h4><p><?php echo $total_orders; ?></p></div>
            <div class="stat-box"><h4>Total Customers</h4><p><?php echo $total_cust; ?></p></div>
            <div class="stat-box"><h4>Active Products</h4><p><?php echo $total_products; ?></p></div>
        </div>

        <div class="table-box">
            <h3>Real-time Sales Trend</h3>
            <canvas id="salesChart" height="80"></canvas>
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
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
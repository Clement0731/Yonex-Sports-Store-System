<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if(isset($_GET['complete'])) {
    $order_id = $_GET['complete'];
    $conn->query("UPDATE ORDERS SET STATUS = 'Completed' WHERE ORDER_ID = $order_id");
    header("Location: order.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .bg-success { background: #d4edda; color: #155724; }
        .bg-warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1>All Orders</h1>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $orders = $conn->query("SELECT ORDERS.ORDER_ID, USERS.username AS USERNAME, ORDERS.ORDER_DATE, ORDERS.TOTAL_PRICE, ORDERS.STATUS FROM ORDERS JOIN USERS ON ORDERS.USER_ID = USERS.id ORDER BY ORDERS.ORDER_ID DESC");
                    if ($orders->num_rows > 0) {
                        while($row = $orders->fetch_assoc()) {
                            $badge_class = ($row['STATUS'] == 'Completed' || $row['STATUS'] == 'Paid') ? 'bg-success' : 'bg-warning';
                            
                            $action_btn = "";
                            if($row['STATUS'] == 'Pending') {
                                $action_btn = "<a href='order.php?complete=".$row['ORDER_ID']."' class='btn btn-edit' style='background:#28a745;'>Mark Completed</a>";
                            } else {
                                $action_btn = "<span style='color:#ccc; font-size:12px;'>Done</span>";
                            }

                            echo "<tr>
                                    <td>#".$row['ORDER_ID']."</td>
                                    <td>".$row['USERNAME']."</td>
                                    <td>".$row['ORDER_DATE']."</td>
                                    <td>RM ".number_format($row['TOTAL_PRICE'], 2)."</td>
                                    <td><span class='badge ".$badge_class."'>".$row['STATUS']."</span></td>
                                    <td>".$action_btn."</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No orders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
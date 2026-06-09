<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// 标记订单为已完成
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
    <title>Manage Orders | Yonex Pro Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;}
        .bg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb;}
        .bg-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba;}
        
        .btn-view { background: #0033a0; color: white; padding: 6px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; border: none; cursor: pointer; transition: 0.3s;}
        .btn-view:hover { background: #002277; }
        .btn-action { background: #28a745; color: white; padding: 6px 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; margin-left: 5px; transition: 0.3s;}
        .btn-action:hover { background: #218838; }

        /* 订单明细弹窗专属 UI */
        .order-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .order-modal-content { background-color: #fff; padding: 0; border-radius: 12px; width: 600px; max-height: 85vh; overflow-y: auto; box-shadow: 0 20px 50px rgba(0,0,0,0.3); position: relative; animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .modal-header { background: #0033a0; color: white; padding: 20px 25px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10; }
        .modal-header h2 { margin: 0; font-size: 20px; font-weight: 900; letter-spacing: 1px;}
        .close-btn { color: white; font-size: 28px; cursor: pointer; font-weight: bold; line-height: 1; transition: 0.2s; }
        .close-btn:hover { color: #e60012; transform: scale(1.1); }
        
        .modal-body { padding: 25px; }
        .customer-card { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .customer-card p { margin: 5px 0; color: #334155; font-size: 14px;}
        
        /* 继承前台漂亮的详细信息样式 */
        .detailed-order-info { color: #333; font-size: 14.5px; line-height: 1.6; }
        .detailed-order-info .badge { font-weight: bold; padding: 6px 12px; font-size: 12px; border-radius: 4px; background: #e2e8f0 !important; color: #1e293b !important; border: none; letter-spacing: 0; display: inline-block; margin-bottom: 8px; margin-top: 10px;}
        .detailed-order-info .text-muted { color: #64748b !important; }
        
        .modal-footer { background: #f8fafc; padding: 20px 25px; border-top: 1px solid #e2e8f0; border-radius: 0 0 12px 12px; display: flex; justify-content: space-between; align-items: center; }
        .total-amount { font-size: 24px; font-weight: 900; color: #e60012; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1 style="color: #0033a0; font-weight: 900; text-transform: uppercase;">Order Management</h1>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Order Date & Time</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 联表查询，抓取订单和用户的详细信息（包括电话和邮箱）
                    $sql = "SELECT o.ORDER_ID, o.ORDER_DATE, o.TOTAL_PRICE, o.STATUS, o.ORDER_DESC, u.username, u.email, u.phone 
                            FROM ORDERS o 
                            LEFT JOIN USERS u ON o.USER_ID = u.id 
                            ORDER BY o.ORDER_ID DESC";
                    $orders = $conn->query($sql);

                    if ($orders && $orders->num_rows > 0) {
                        while($row = $orders->fetch_assoc()) {
                            $badge_class = ($row['STATUS'] == 'Completed' || $row['STATUS'] == 'Paid') ? 'bg-success' : 'bg-warning';
                            $modal_id = "modal_" . $row['ORDER_ID'];
                            
                            $action_btn = "";
                            if($row['STATUS'] == 'Pending') {
                                $action_btn = "<a href='order.php?complete=".$row['ORDER_ID']."' class='btn-action'>✔ Mark Done</a>";
                            }
                            
                            // 表格行
                            echo "<tr>
                                    <td style='font-weight: bold; color:#0033a0;'># YNX-".$row['ORDER_ID']."</td>
                                    <td style='font-weight: bold;'>".htmlspecialchars($row['username'])."</td>
                                    <td style='color:#64748b; font-size:14px;'>".$row['ORDER_DATE']."</td>
                                    <td style='font-weight: bold; color:#e60012;'>RM ".number_format($row['TOTAL_PRICE'], 2)."</td>
                                    <td><span class='badge ".$badge_class."'>".$row['STATUS']."</span></td>
                                    <td style='text-align: right;'>
                                        <button onclick=\"openOrderModal('$modal_id')\" class='btn-view'>📄 View Full Details</button>
                                        $action_btn
                                    </td>
                                  </tr>";
                            
                            // 🚀 隐藏的专业版弹窗 (每一行都有一个自己的弹窗)
                            ?>
                            <div id="<?php echo $modal_id; ?>" class="order-modal">
                                <div class="order-modal-content">
                                    <div class="modal-header">
                                        <h2>Order #YNX-<?php echo $row['ORDER_ID']; ?> Details</h2>
                                        <span class="close-btn" onclick="closeOrderModal('<?php echo $modal_id; ?>')">&times;</span>
                                    </div>
                                    <div class="modal-body">
                                        <div class="customer-card">
                                            <h4 style="color: #0033a0; border-bottom: 2px solid #cbd5e1; padding-bottom: 5px; margin-top: 0; margin-bottom: 10px;">👤 Customer Profile</h4>
                                            <p><b>Name:</b> <?php echo htmlspecialchars($row['username'] ?? 'Guest'); ?></p>
                                            <p><b>Email:</b> <a href="mailto:<?php echo htmlspecialchars($row['email'] ?? ''); ?>" style="color:#0033a0;"><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></a></p>
                                            <p><b>Registered Phone:</b> <?php echo htmlspecialchars($row['phone'] ?? 'Not provided'); ?></p>
                                        </div>
                                        
                                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                                            <?php 
                                            // 智能兼容旧订单和新订单
                                            $desc = $row['ORDER_DESC'];
                                            if (strpos($desc, 'detailed-order-info') !== false) {
                                                echo $desc; // 新版本：直接输出前台生成的完美明细 (地址、商品、支付方式)
                                            } else {
                                                // 旧版本订单兼容显示
                                                echo "<div class='detailed-order-info'>";
                                                echo "<span class='badge'>🛒 Legacy Order Data</span><br>";
                                                echo nl2br(htmlspecialchars($desc));
                                                echo "</div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div>
                                            <span style="color:#64748b; font-size:12px; display:block;">Order Status</span>
                                            <span class="badge <?php echo $badge_class; ?>"><?php echo $row['STATUS']; ?></span>
                                        </div>
                                        <div style="text-align: right;">
                                            <span style="color:#64748b; font-size:12px; display:block; font-weight:bold;">Total Paid by Customer</span>
                                            <span class="total-amount">RM <?php echo number_format($row['TOTAL_PRICE'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center; padding: 40px; color: #94a3b8;'>No orders found in the system.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function openOrderModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
            document.body.style.overflow = 'hidden'; // 防止背景滚动
        }
        function closeOrderModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        // 点击黑色半透明背景也能关闭
        window.onclick = function(event) {
            if (event.target.classList.contains('order-modal')) {
                event.target.style.display = "none";
                document.body.style.overflow = 'auto';
            }
        }
    </script>
</body>
</html>
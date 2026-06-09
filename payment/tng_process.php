<?php
session_start();
require_once 'db_config.php'; // 确保你的数据库连接文件正确

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. 获取表单传过来的数据
    $total = $_POST['total_amount'];
    $ids = $_POST['product_ids'];
    $phone = $_POST['phone_no'];
    $pin = $_POST['payment_pin'];

    // 2. 这里是模拟支付逻辑 (在实际项目中，你会在这里写 SQL 存入 orders table)
    // 示例：$sql = "INSERT INTO orders (user_id, total_amount, status) VALUES (...)";
    
    // 3. 假设支付成功，跳转到成功页面，并把金额和支付方式传过去显示
    // 添加了 method 参数，使得收据页面能正确显示支付方式
    header("Location: payment_success.php?amount=" . $total . "&method=TNG%20eWallet");
    exit();
} else {
    // 如果不是 POST 提交，直接退回首页
    header("Location: index.php");
    exit();
}
?>
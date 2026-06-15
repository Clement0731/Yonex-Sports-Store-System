<?php
session_start();
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $total = $_POST['total_amount'];
    $ids = $_POST['product_ids'];
    $phone = $_POST['phone_no'];
    $pin = $_POST['payment_pin'];
    $addr_id = isset($_POST['addr_id']) ? $_POST['addr_id'] : '';

    // 假设支付成功，跳转到成功页面，并把金额、支付方式、地址ID传过去
    header("Location: payment_success.php?amount=" . $total . "&method=TNG%20eWallet&addr_id=" . $addr_id);
    exit();
} else {
    // 如果不是 POST 提交，直接退回首页
    header("Location: index.php");
    exit();
}
?>
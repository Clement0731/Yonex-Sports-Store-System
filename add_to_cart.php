<?php
session_start();
include 'db_connect.php'; 

// 1. 检查用户是否已经登录（只有登录才能加购物车）
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first to add items to your cart.'); window.location.href='login_register/login_page.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $spec_value = $conn->real_escape_string($_POST['selected_spec']);
    $quantity = intval($_POST['quantity']);
    
    // 如果没有选线或磅数，设为 NULL
    $string_option_id = !empty($_POST['string_option_id']) ? intval($_POST['string_option_id']) : "NULL";
    $tension_option_id = !empty($_POST['tension_option_id']) ? intval($_POST['tension_option_id']) : "NULL";

    // 2. 去 product_variants 表里，找出对应规格的 variant_id
    $variant_query = "SELECT id FROM product_variants WHERE product_id = $product_id AND spec_value = '$spec_value' LIMIT 1";
    $variant_res = $conn->query($variant_query);
    
    if ($variant_res && $variant_res->num_rows > 0) {
        $variant_row = $variant_res->fetch_assoc();
        $variant_id = $variant_row['id'];
        
        // 3. 把商品写入我们之前新建的 cart_items 表！
        $insert_sql = "INSERT INTO cart_items (user_id, product_id, variant_id, quantity, string_option_id, tension_option_id) 
                       VALUES ($user_id, $product_id, $variant_id, $quantity, $string_option_id, $tension_option_id)";
                       
        if ($conn->query($insert_sql)) {
            // 添加成功，跳转到购物车页面！
            // 请确保 payment/shopping_cart.php 路径正确，如果不在 payment 文件夹请自行去掉 'payment/'
            echo "<script>alert('Item successfully added to your cart!'); window.location.href='payment/shopping_cart.php';</script>";
        } else {
            echo "Error adding to cart: " . $conn->error;
        }
    } else {
        echo "<script>alert('Error: The selected specification is currently out of stock or invalid.'); window.history.back();</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
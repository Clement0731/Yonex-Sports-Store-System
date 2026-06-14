<?php
session_start();
include 'db_connect.php'; 

// 1. 检查用户是否已经登录（只有登录才能加购物车或直接购买）
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first to proceed.'); window.location.href='login_register/login_page.php';</script>";
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

    // 获取印字名字，如果为空则设为 NULL
    $custom_name_val = (isset($_POST['custom_name']) && !empty(trim($_POST['custom_name']))) ? strtoupper(trim($_POST['custom_name'])) : null;
    $custom_name_sql = $custom_name_val ? "'" . $conn->real_escape_string($custom_name_val) . "'" : "NULL";

    // 2. 去 product_variants 表里，找出对应规格的 variant_id
    $variant_query = "SELECT id FROM product_variants WHERE product_id = $product_id AND spec_value = '$spec_value' LIMIT 1";
    $variant_res = $conn->query($variant_query);
    
    if ($variant_res && $variant_res->num_rows > 0) {
        $variant_row = $variant_res->fetch_assoc();
        $variant_id = $variant_row['id'];
        
        // 3. 把商品写入 cart_items 表
        $insert_sql = "INSERT INTO cart_items (user_id, product_id, variant_id, quantity, string_option_id, tension_option_id, custom_name) 
                       VALUES ($user_id, $product_id, $variant_id, $quantity, $string_option_id, $tension_option_id, $custom_name_sql)";
                       
        if ($conn->query($insert_sql)) {
            // 💡 核心修改：获取刚刚存入购物车的记录 ID
            $new_cart_id = $conn->insert_id;
            
            // 判断用户点击的是哪一个按钮
            if (isset($_POST['action_type']) && $_POST['action_type'] === 'buy_now') {
                // 如果是 Buy Now，直接静默跳转到结账页面，带上商品数据
                echo "<script>window.location.href='payment/check_out.php?ids=" . $new_cart_id . "&qtys=" . $quantity . "';</script>";
                exit();
            } else {
                // 如果是正常的 Add to Cart，弹窗提示并跳转到购物车列表
                echo "<script>alert('Item successfully added to your cart!'); window.location.href='payment/shopping_cart.php';</script>";
                exit();
            }
            
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
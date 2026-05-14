<?php
$host = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "yonex_db"; 

// 创建连接
$conn = new mysqli($host, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

// 设置编码
$conn->set_charset("utf8mb4");
?>
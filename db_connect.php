<?php
$servername = "localhost";
$username = "root"; // 默认通常是 root
$password = "";     // 如果你有设置密码请填入，XAMPP/WAMP 默认通常为空
$dbname = "yonex_db"; // 你的数据库名称

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}
?>
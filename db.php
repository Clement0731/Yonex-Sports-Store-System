<?php
$host = 'localhost';
$dbname = 'yonex_db';        // 改成你的数据库名
$username = 'root';           // 改成你的 MySQL 用户名（XAMPP 默认是 root）
$password = '';               // 改成你的 MySQL 密码（XAMPP 默认是空）

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
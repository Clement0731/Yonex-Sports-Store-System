<?php
$host = "localhost";
$dbname = "yonex_db"; // 确保这和你 phpMyAdmin 里的数据库名一致
$username = "root";   // XAMPP 默认是 root
$password = "";       // XAMPP 默认密码为空

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // 设置错误模式，方便出错时排查
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
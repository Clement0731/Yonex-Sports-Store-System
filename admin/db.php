<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yonex_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed");
}
?>
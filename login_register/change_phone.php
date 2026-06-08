<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$conn = new mysqli("localhost", "root", "", "yonex_db");
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $conn->query("UPDATE users SET phone='$new_phone' WHERE id='$user_id'");
    header("Location: edit_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Phone</title>
    <style>
        :root { --blue: #002B7F; }
        body { font-family: sans-serif; background: #fff; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 400px; margin: 0 auto; }
        
        .back-btn { text-decoration: none; color: #333; font-size: 20px; }
        h2 { margin: 30px 0; font-size: 22px; }
        
        label { font-size: 12px; color: #888; margin-bottom: 5px; display: block; }
        input { 
            width: 100%; border: none; border-bottom: 1px solid #ddd; 
            padding: 10px 0; font-size: 16px; margin-bottom: 30px; outline: none;
        }
        input:focus { border-bottom: 1px solid var(--blue); }
        
        button { 
            width: 100%; background: var(--blue); color: #fff; border: none; 
            padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="edit_profile.php" class="back-btn">←</a>
    <h2>Change Phone Number</h2>
    
    <form method="POST">
        <label>Country/Region</label>
        <input type="text" value="Malaysia (+60)" readonly style="color:#888;">
        
        <label>New Phone Number</label>
        <input type="tel" name="phone" placeholder="Enter new number" required autofocus>
        
        <button type="submit">Continue</button>
    </form>
</div>

</body>
</html>
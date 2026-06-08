<?php
session_start();
$conn = new mysqli("localhost", "root", "", "yonex_db");
if (!isset($_SESSION['user_id'])) { header("Location: login_page.php"); exit(); }
$user_id = $_SESSION['user_id'];

// 从数据库获取属于该用户的地址
$addresses = $conn->query("SELECT * FROM addresses WHERE user_id = '$user_id'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Addresses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7f9; padding: 20px; }
        .container { max-width: 480px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 15px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .address-info { margin-bottom: 10px; color: #333; line-height: 1.5; }
        .name-phone { font-weight: bold; display: block; }
        .label-badge { font-size: 0.8rem; background: #edf2f7; padding: 2px 8px; border-radius: 4px; color: #4a5568; }
        .btn-add { display: block; text-align: center; padding: 15px; background: #003366; color: white; border-radius: 12px; text-decoration: none; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <a href="user_profile.php" style="color: #333; text-decoration: none;"><i class="fas fa-arrow-left"></i> Back</a>
    <h2 style="margin: 20px 0;">My Addresses</h2>
    
    <?php if ($addresses && $addresses->num_rows > 0): ?>
        <?php while($row = $addresses->fetch_assoc()): ?>
            <div class="card">
                <div class="address-info">
                    <span class="name-phone">
                        <?php echo htmlspecialchars($row['receiver_name']); ?> | 
                        <?php echo htmlspecialchars($row['receiver_phone']); ?>
                        <span class="label-badge"><?php echo htmlspecialchars($row['label']); ?></span>
                    </span>
                    <p style="margin: 5px 0;">
                        <?php echo htmlspecialchars($row['full_address']); ?><br>
                        <?php echo htmlspecialchars($row['postcode'] . ', ' . $row['city_state']); ?>
                    </p>
                </div>
                <a href="edit_address.php?id=<?php echo $row['id']; ?>" style="color: #003366; font-weight: bold; text-decoration: none;">Edit</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card" style="text-align: center; color: #777;">No addresses saved yet.</div>
    <?php endif; ?>

    <a href="edit_address.php" class="btn-add">+ Add New Address</a>
</div>
</body>
</html>
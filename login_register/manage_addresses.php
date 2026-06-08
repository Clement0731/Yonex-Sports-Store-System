<?php
session_start();
$conn = new mysqli("localhost", "root", "", "yonex_db");
if (!isset($_SESSION['user_id'])) { header("Location: login_page.php"); exit(); }
$user_id = $_SESSION['user_id'];

// --- 新增：处理删除逻辑 ---
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // 使用 prepare 确保删除的地址确实属于当前用户，防止越权删除
    $stmt = $conn->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    
    // 删除后重定向到当前页面，防止刷新重复触发
    header("Location: manage_addresses.php");
    exit();
}

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
        /* 新增样式 */
        .actions { margin-top: 10px; }
        .delete-link { color: #e53e3e; font-weight: bold; text-decoration: none; margin-left: 15px; }
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
                <div class="actions">
                    <a href="edit_address.php?id=<?php echo $row['id']; ?>" style="color: #003366; font-weight: bold; text-decoration: none;">Edit</a>
                    <a href="manage_addresses.php?delete_id=<?php echo $row['id']; ?>" 
                       class="delete-link" 
                       onclick="return confirm('Are you sure you want to delete this address?');">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card" style="text-align: center; color: #777;">No addresses saved yet.</div>
    <?php endif; ?>

    <a href="edit_address.php" class="btn-add">+ Add New Address</a>
</div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// ==========================================
// 1. 处理删除请求 (Delete)
// ==========================================
if(isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = '$del_id'");
    header("Location: manage_customer.php");
    exit();
}

// ==========================================
// 2. 处理更新请求 (Edit / Save)
// ==========================================
if(isset($_POST['update_customer'])) {
    $id = $_POST['customer_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $is_verified = $_POST['is_verified'];
    
    $conn->query("UPDATE users SET username='$username', email='$email', phone='$phone', gender='$gender', is_verified='$is_verified' WHERE id='$id'");
    header("Location: manage_customer.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-form-box { background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); border-top: 4px solid #0033a0; }
        .edit-form-box h3 { margin-top: 0; color: #0033a0; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: bold; color: #555; margin-bottom: 8px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 14px;}
        .form-group input[readonly] { background: #f5f5f5; color: #777; cursor: not-allowed; }
        .btn-save { background: #0033a0; color: white; padding: 10px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;}
        .btn-cancel { background: #ccc; color: #333; padding: 10px 25px; text-decoration: none; border-radius: 4px; margin-left: 10px; font-weight: bold;}
        
        .btn-edit { background: #f39c12; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-size: 12px; display: inline-block;}
        .btn-delete { background: #e74c3c; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 12px; display: inline-block;}
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; color: white; }
        .bg-green { background: #2ecc71; }
        .bg-red { background: #e74c3c; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1>Manage Customers</h1>
        </div>

        <?php
        if(isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $edit_result = $conn->query("SELECT * FROM users WHERE id = '$edit_id'");
            if($edit_result->num_rows > 0) {
                $user_data = $edit_result->fetch_assoc();
        ?>
            <div class="edit-form-box">
                <h3>View & Edit Customer: <?php echo htmlspecialchars($user_data['username']); ?></h3>
                <form method="POST" action="manage_customer.php">
                    <input type="hidden" name="customer_id" value="<?php echo $user_data['id']; ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="" <?php if(empty($user_data['gender'])) echo 'selected'; ?>>Not Set</option>
                                <option value="Male" <?php if(($user_data['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if(($user_data['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                                <option value="Private" <?php if(($user_data['gender'] ?? '') == 'Private') echo 'selected'; ?>>Private</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Registration Date</label>
                            <input type="text" value="<?php echo $user_data['created_at']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Account Status</label>
                            <select name="is_verified">
                                <option value="1" <?php if($user_data['is_verified'] == 1) echo 'selected'; ?>>Verified</option>
                                <option value="0" <?php if($user_data['is_verified'] == 0) echo 'selected'; ?>>Pending OTP</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="update_customer" class="btn-save">Save Changes</button>
                    <a href="manage_customer.php" class="btn-cancel">Cancel</a>
                </form>
            </div>
        <?php 
            } 
        } 
        ?>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $customers = $conn->query("SELECT * FROM users ORDER BY id DESC");
                    
                    if ($customers->num_rows > 0) {
                        while($row = $customers->fetch_assoc()) {
                            $status_badge = ($row['is_verified'] == 1) 
                                ? "<span class='badge bg-green'>Verified</span>" 
                                : "<span class='badge bg-red'>Pending</span>";
                            
                            echo "<tr>
                                    <td>#".$row['id']."</td>
                                    <td>".htmlspecialchars($row['username'])."</td>
                                    <td>".htmlspecialchars($row['email'])."</td>
                                    <td>".htmlspecialchars($row['phone'] ?? '-')."</td>
                                    <td>".$status_badge."</td>
                                    <td>
                                        <a href='manage_customer.php?edit=".$row['id']."' class='btn-edit'>View / Edit</a>
                                        <a href='manage_customer.php?delete=".$row['id']."' class='btn-delete' onclick=\"return confirm('Are you sure you want to completely remove this customer?');\">Remove</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding:20px;'>No customers registered yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
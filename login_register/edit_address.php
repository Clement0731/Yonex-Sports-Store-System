<?php
session_start();
$conn = new mysqli("localhost", "root", "", "yonex_db");
if (!isset($_SESSION['user_id'])) { header("Location: login_page.php"); exit(); }
$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

// 处理保存逻辑
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 关键修改：变量名依然由表单接收，但 SQL 语句中使用数据库的实际列名
    $receiver_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $receiver_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $full_address = mysqli_real_escape_string($conn, $_POST['address']);
    $postcode = mysqli_real_escape_string($conn, $_POST['postcode']);
    $city_state = mysqli_real_escape_string($conn, $_POST['city_state']);
    $label = mysqli_real_escape_string($conn, $_POST['label']);

    if ($id) {
        // 使用数据库实际的列名：receiver_name, receiver_phone, full_address
        $conn->query("UPDATE addresses SET receiver_name='$receiver_name', receiver_phone='$receiver_phone', full_address='$full_address', postcode='$postcode', city_state='$city_state', label='$label' WHERE id='$id' AND user_id='$user_id'");
    } else {
        // 使用数据库实际的列名：receiver_name, receiver_phone, full_address
        $conn->query("INSERT INTO addresses (user_id, receiver_name, receiver_phone, full_address, postcode, city_state, label) VALUES ('$user_id', '$receiver_name', '$receiver_phone', '$full_address', '$postcode', '$city_state', '$label')");
    }
    header("Location: manage_addresses.php");
    exit();
}

// 获取当前地址信息用于回填表单
$current = ['receiver_name'=>'', 'receiver_phone'=>'', 'full_address'=>'', 'postcode'=>'', 'city_state'=>'', 'label'=>'Home'];
if ($id) {
    $res = $conn->query("SELECT * FROM addresses WHERE id='$id' AND user_id='$user_id'");
    if ($res && $res->num_rows > 0) $current = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Address Options</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .modal { background: white; width: 90%; max-width: 500px; padding: 25px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        h2 { font-size: 20px; margin-bottom: 20px; }
        input, textarea, select { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #e2e8f0; border-radius: 6px; box-sizing: border-box; }
        .row { display: flex; gap: 10px; }
        .row > * { flex: 1; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        button { flex: 1; padding: 15px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; }
        .btn-back { background: #f4f4f4; }
        .btn-save { background: #002B7F; color: white; }
    </style>
</head>
<body>

<div class="modal">
    <h2><?php echo $id ? 'Edit Address' : 'Add New Address'; ?></h2>
    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" value="<?php echo htmlspecialchars($current['receiver_name']); ?>" required>
        <input type="tel" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($current['receiver_phone']); ?>" required>
        <textarea name="address" placeholder="Address Details" rows="3" required><?php echo htmlspecialchars($current['full_address']); ?></textarea>
        <div class="row">
            <input type="text" name="postcode" placeholder="Postcode" value="<?php echo htmlspecialchars($current['postcode']); ?>" required>
            <input type="text" name="city_state" placeholder="City, State" value="<?php echo htmlspecialchars($current['city_state']); ?>" required>
        </div>
        <select name="label">
            <option value="Home" <?php if($current['label']=='Home') echo 'selected'; ?>>Home</option>
            <option value="Office" <?php if($current['label']=='Office') echo 'selected'; ?>>Office</option>
        </select>
        <div class="btn-group">
            <button type="button" class="btn-back" onclick="history.back()">Back</button>
            <button type="submit" class="btn-save">Save Address</button>
        </div>
    </form>
</div>

</body>
</html>
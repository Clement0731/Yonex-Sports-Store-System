<?php
include '../db_connect.php'; 

// ==========================================
// 💡 1. 接收和处理表单数据 (CRUD 逻辑)
// ==========================================

// [删除 Delete] - 当点击 Delete 按钮时触发
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM service_options WHERE id = $delete_id");
    header("Location: admin_service.php"); // 刷新页面
    exit();
}

// [添加 Add] - 当提交 Add 表单时触发
if (isset($_POST['add_service'])) {
    $type = $_POST['service_type'];
    $name = $_POST['option_name'];
    $price = $_POST['additional_price'];
    $conn->query("INSERT INTO service_options (service_type, option_name, additional_price) VALUES ('$type', '$name', '$price')");
    header("Location: admin_service.php");
    exit();
}

// [修改 Edit] - 当提交 Edit 表单时触发
if (isset($_POST['edit_service'])) {
    $id = $_POST['edit_id'];
    $type = $_POST['service_type'];
    $name = $_POST['option_name'];
    $price = $_POST['additional_price'];
    $conn->query("UPDATE service_options SET service_type='$type', option_name='$name', additional_price='$price' WHERE id=$id");
    header("Location: admin_service.php");
    exit();
}

// 获取所有服务选项 (用于展示在表格里)
$sql = "SELECT * FROM service_options ORDER BY service_type, id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Services - Yonex Admin</title>
    <link rel="stylesheet" href="style.css"> 
    
    <style>
        /* 💡 弹窗 (Modal) 的专属样式 */
        .modal {
            display: none; /* 默认隐藏 */
            position: fixed; z-index: 1000; left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5); /* 半透明黑背景 */
            align-items: center; justify-content: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        .close-btn {
            position: absolute; right: 20px; top: 20px;
            font-size: 24px; cursor: pointer; color: #888;
        }
        .close-btn:hover { color: #e60012; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #0033a0; font-size: 14px;}
        .form-group input, .form-group select {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;
        }
        .modal h2 { color: #0033a0; margin-bottom: 20px; font-size: 20px; border-bottom: 2px solid #e60012; padding-bottom: 10px;}
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-flex">
            <h1 class="page-title">Manage Services</h1>
            <div>
                <button onclick="openModal('addModal')" class="btn btn-add">+ Add New Option</button>
            </div>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Option Name</th>
                        <th>Extra Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $badgeClass = ($row['service_type'] == 'string') ? 'badge-string' : 'badge-tension';
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo strtoupper($row['service_type']); ?></span></td>
                        <td><strong><?php echo $row['option_name']; ?></strong></td>
                        <td style="color: #e60012; font-weight: bold;">RM <?php echo number_format($row['additional_price'], 2); ?></td>
                        <td>
                            <button onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo $row['service_type']; ?>', '<?php echo addslashes($row['option_name']); ?>', <?php echo $row['additional_price']; ?>)" class="btn btn-edit">Edit</button>
                            
                            <a href="admin_service.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" style="margin-left: 5px;" onclick="return confirm('Are you sure you want to delete this option?');">Delete</a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; color: #888; padding: 30px;'>No service options found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            <h2>Add New Service Option</h2>
            <form method="POST" action="admin_service.php">
                <div class="form-group">
                    <label>Category</label>
                    <select name="service_type" required>
                        <option value="string">String</option>
                        <option value="tension">Tension</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Option Name</label>
                    <input type="text" name="option_name" placeholder="e.g. BG80 Power" required>
                </div>
                <div class="form-group">
                    <label>Extra Price</label>
                    <input type="number" step="0.01" name="additional_price" placeholder="e.g. 35.00" required>
                </div>
                <button type="submit" name="add_service" class="btn btn-add" style="width: 100%; margin-top: 10px;">Save Option</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Service Option</h2>
            <form method="POST" action="admin_service.php">
                <input type="hidden" name="edit_id" id="edit_id">
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="service_type" id="edit_type" required>
                        <option value="string">String</option>
                        <option value="tension">Tension</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Option Name</label>
                    <input type="text" name="option_name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Extra Price</label>
                    <input type="number" step="0.01" name="additional_price" id="edit_price" required>
                </div>
                <button type="submit" name="edit_service" class="btn btn-edit" style="width: 100%; margin-top: 10px; background: #0033a0;">Update Option</button>
            </form>
        </div>
    </div>

    <script>
        // 打开普通的 Modal (比如 Add)
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        // 关闭 Modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // 打开 Edit Modal 并自动填入现有数据
        function openEditModal(id, type, name, price) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            openModal('editModal'); // 填完资料后弹出视窗
        }
    </script>

</body>
</html>
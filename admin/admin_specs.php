<?php
include '../db_connect.php'; 

// ==========================================
// 💡 处理 CRUD (增删改) 逻辑
// ==========================================

// [删除 Delete]
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM product_specs WHERE id = $delete_id");
    header("Location: admin_specs.php");
    exit();
}

// [添加 Add]
if (isset($_POST['add_spec'])) {
    $cat = $_POST['category'];
    $val = $_POST['spec_value'];
    $conn->query("INSERT INTO product_specs (category, spec_value) VALUES ('$cat', '$val')");
    header("Location: admin_specs.php");
    exit();
}

// [修改 Edit]
if (isset($_POST['edit_spec'])) {
    $id = $_POST['edit_id'];
    $cat = $_POST['category'];
    $val = $_POST['spec_value'];
    $conn->query("UPDATE product_specs SET category='$cat', spec_value='$val' WHERE id=$id");
    header("Location: admin_specs.php");
    exit();
}

// 获取所有规格选项
$sql = "SELECT * FROM product_specs ORDER BY category, id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Specs - Yonex Admin</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* 弹窗样式补充 (因为这个没写进 style.css，所以留在这里) */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal-content { background-color: #fff; padding: 30px; border-radius: 8px; width: 400px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative; }
        .close-btn { position: absolute; right: 20px; top: 20px; font-size: 24px; cursor: pointer; color: #888; }
        .close-btn:hover { color: #e60012; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #0033a0; font-size: 14px;}
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .modal h2 { color: #0033a0; margin-bottom: 20px; font-size: 20px; border-bottom: 2px solid #e60012; padding-bottom: 10px;}
        
        /* 给不同的 Category 设置不同的颜色标签 */
        .badge-cat { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white; display: inline-block; }
        .bg-rackets { background: #e60012; } /* 红 */
        .bg-footwear { background: #0033a0; } /* 蓝 */
        .bg-apparel { background: #27ae60; } /* 绿 */
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-flex">
            <h1 class="page-title">Manage Product Specs (规格管理)</h1>
            <div>
                <button onclick="openModal('addModal')" class="btn btn-add">+ Add New Spec</button>
            </div>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category (分类)</th>
                        <th>Specification Value (规格值)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // 动态分配标签颜色
                            $bgClass = '';
                            if ($row['category'] == 'rackets') $bgClass = 'bg-rackets';
                            if ($row['category'] == 'footwear') $bgClass = 'bg-footwear';
                            if ($row['category'] == 'apparel') $bgClass = 'bg-apparel';
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><span class="badge-cat <?php echo $bgClass; ?>"><?php echo strtoupper($row['category']); ?></span></td>
                        <td><strong><?php echo $row['spec_value']; ?></strong></td>
                        <td>
                            <button onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo $row['category']; ?>', '<?php echo addslashes($row['spec_value']); ?>')" class="btn btn-edit">Edit</button>
                            <a href="admin_specs.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" style="margin-left: 5px;" onclick="return confirm('Delete this specification?');">Delete</a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align: center; color: #888; padding: 30px;'>No specs found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            <h2>Add New Specification</h2>
            <form method="POST" action="admin_specs.php">
                <div class="form-group">
                    <label>Category (应用分类)</label>
                    <select name="category" required>
                        <option value="rackets">Rackets (球拍)</option>
                        <option value="footwear">Footwear (鞋子)</option>
                        <option value="apparel">Apparel (衣服)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Specification Value (规格值)</label>
                    <input type="text" name="spec_value" placeholder="e.g. 44, XXL, 5U / G6" required>
                </div>
                <button type="submit" name="add_spec" class="btn btn-add" style="width: 100%; margin-top: 10px;">Save Specification</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Specification</h2>
            <form method="POST" action="admin_specs.php">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="form-group">
                    <label>Category (应用分类)</label>
                    <select name="category" id="edit_category" required>
                        <option value="rackets">Rackets (球拍)</option>
                        <option value="footwear">Footwear (鞋子)</option>
                        <option value="apparel">Apparel (衣服)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Specification Value (规格值)</label>
                    <input type="text" name="spec_value" id="edit_value" required>
                </div>
                <button type="submit" name="edit_spec" class="btn btn-edit" style="width: 100%; margin-top: 10px; background: #0033a0;">Update Specification</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) { document.getElementById(modalId).style.display = 'flex'; }
        function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; }
        function openEditModal(id, category, value) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_value').value = value;
            openModal('editModal'); 
        }
    </script>

</body>
</html>
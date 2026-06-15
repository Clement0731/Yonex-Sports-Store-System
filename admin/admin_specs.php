<?php
include '../db_connect.php'; 

// ==========================================
// 💡 处理 CRUD (增删改) 逻辑
// ==========================================
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM product_specs WHERE id = $delete_id");
    header("Location: admin_specs.php");
    exit();
}

if (isset($_POST['add_spec'])) {
    $cat = $_POST['category'];
    $val = $_POST['spec_value'];
    $conn->query("INSERT INTO product_specs (category, spec_value) VALUES ('$cat', '$val')");
    header("Location: admin_specs.php");
    exit();
}

if (isset($_POST['edit_spec'])) {
    $id = $_POST['edit_id'];
    $cat = $_POST['category'];
    $val = $_POST['spec_value'];
    $conn->query("UPDATE product_specs SET category='$cat', spec_value='$val' WHERE id=$id");
    header("Location: admin_specs.php");
    exit();
}

$sql = "SELECT ps.*, IFNULL(SUM(pv.stock_quantity), 0) as spec_total_stock 
        FROM product_specs ps 
        LEFT JOIN product_variants pv ON ps.spec_value = pv.spec_value 
        GROUP BY ps.id 
        ORDER BY ps.category, ps.id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Specs - Yonex Admin</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* Dashboard Enterprise Theme overrides */
        body { background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .main-content { padding: 40px; }
        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
        .page-title { color: #0033a0; font-weight: 900; font-size: 22px; text-transform: uppercase; margin-bottom: 5px; }
        .page-subtitle { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
        
        .admin-card { background: #fff; border: 1px solid #eaeaea; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .card-header-line { border-left: 3px solid #0033a0; padding-left: 10px; font-size: 13px; font-weight: bold; color: #0033a0; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; }

        .btn-dash { background: #0033a0; color: white; padding: 10px 20px; border: none; font-weight: bold; cursor: pointer; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; transition: 0.2s; }
        .btn-dash:hover { background: #002277; }
        .btn-sm-text { background: transparent; border: none; font-size: 12px; font-weight: bold; cursor: pointer; text-transform: uppercase; padding: 5px; }
        .text-blue { color: #0033a0; } .text-blue:hover { text-decoration: underline; }
        .text-red { color: #c62828; } .text-red:hover { text-decoration: underline; }

        /* Enterprise Table */
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .admin-table th { font-size: 11px; text-transform: uppercase; color: #888; border-bottom: 2px solid #eaeaea; padding: 12px 15px; text-align: left; font-weight: bold; }
        .admin-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 13px; color: #333; vertical-align: middle; }
        .admin-table tbody tr:hover { background-color: #fcfcfc; }

        .badge-cat { padding: 4px 10px; border-radius: 2px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #fff; display: inline-block; }
        .bg-rackets { background: #333; } 
        .bg-footwear { background: #555; }
        .bg-apparel { background: #777; } 

        /* Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); align-items: center; justify-content: center; }
        .modal-content { background-color: #fff; padding: 30px; width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); position: relative; border-radius: 0; border: 1px solid #eaeaea; }
        .close-btn { position: absolute; right: 20px; top: 20px; font-size: 24px; cursor: pointer; color: #888; line-height: 1;}
        .close-btn:hover { color: #000; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 11px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; background: #fafafa; font-size: 13px; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: #0033a0; background: #fff; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1 class="page-title">Product Specifications</h1>
                <p class="page-subtitle">Specification Management Panel</p>
            </div>
            <button onclick="openModal('addModal')" class="btn-dash">+ Add New Spec</button>
        </div>

        <div class="admin-card">
            <div class="card-header-line">Active Specifications List</div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>System ID</th>
                        <th>Category Sector</th>
                        <th>Specification Value</th>
                        <th>Associated Stock</th>
                        <th>Action Protocol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $bgClass = 'bg-apparel'; // default
                            if ($row['category'] == 'rackets') $bgClass = 'bg-rackets';
                            if ($row['category'] == 'footwear') $bgClass = 'bg-footwear';
                    ?>
                    <tr>
                        <td>#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><span class="badge-cat <?php echo $bgClass; ?>"><?php echo $row['category']; ?></span></td>
                        <td><strong><?php echo $row['spec_value']; ?></strong></td>
                        <td style="color: #666;"><?php echo $row['spec_total_stock']; ?> Units</td>
                        <td>
                            <button onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo $row['category']; ?>', '<?php echo addslashes($row['spec_value']); ?>')" class="btn-sm-text text-blue">Edit</button>
                            <span style="color:#ddd; margin: 0 5px;">|</span>
                            <a href="admin_specs.php?delete=<?php echo $row['id']; ?>" class="btn-sm-text text-red" style="text-decoration: none;" onclick="return confirm('Confirm deletion of this specification?');">Delete</a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; color: #888; padding: 40px;'>No specifications registered in the system.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            <div class="card-header-line" style="margin-bottom: 25px;">New Specification</div>
            <form method="POST" action="admin_specs.php">
                <div class="form-group">
                    <label>Category Sector</label>
                    <select name="category" class="form-control" required>
                        <option value="rackets">Rackets</option>
                        <option value="footwear">Footwear</option>
                        <option value="apparel">Apparel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Specification Value</label>
                    <input type="text" name="spec_value" class="form-control" placeholder="e.g. 44, XXL, 5U / G6" required>
                </div>
                <button type="submit" name="add_spec" class="btn-dash" style="width: 100%;">Save Specification</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <div class="card-header-line" style="margin-bottom: 25px;">Modify Specification</div>
            <form method="POST" action="admin_specs.php">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="form-group">
                    <label>Category Sector</label>
                    <select name="category" id="edit_category" class="form-control" required>
                        <option value="rackets">Rackets</option>
                        <option value="footwear">Footwear</option>
                        <option value="apparel">Apparel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Specification Value</label>
                    <input type="text" name="spec_value" id="edit_value" class="form-control" required>
                </div>
                <button type="submit" name="edit_spec" class="btn-dash" style="width: 100%;">Update Data</button>
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
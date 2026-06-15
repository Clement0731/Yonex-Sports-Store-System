<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$error_msg = "";
$success_msg = "";

// ==========================================
// 💡 1. 添加新分类 (Add)
// ==========================================
if (isset($_POST['add_category'])) {
    $cat_name = $_POST['category_name'];
    $target_dir = "../images/"; 
    $image_name = basename($_FILES["category_image"]["name"]);
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["category_image"]["tmp_name"], $target_file)) {
        $image_url = '../images/' . $image_name;
        $conn->query("INSERT INTO categories (category_name, image_url) VALUES ('$cat_name', '$image_url')");
        $success_msg = "<div class='alert alert-success'>Category added successfully!</div>";
    } else {
        $error_msg = "<div class='alert alert-danger'>Failed to upload image.</div>";
    }
}

// ==========================================
// 💡 2. 删除分类 (Delete)
// ==========================================
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $del_name = $_GET['cat_name'];

    $check_prod = $conn->query("SELECT COUNT(*) as cnt FROM PRODUCTS WHERE CATEGORY = '$del_name'");
    $prod_count = $check_prod->fetch_assoc()['cnt'];

    if ($prod_count > 0) {
        $error_msg = "<div class='alert alert-danger'>Cannot delete! There are still <b>$prod_count items</b> inside the '$del_name' category. Please move or delete the products first.</div>";
    } else {
        $conn->query("DELETE FROM categories WHERE id = $del_id");
        $success_msg = "<div class='alert alert-success'>Category '$del_name' deleted successfully.</div>";
    }
}

// ==========================================
// 💡 3. 修改分类 (Edit)
// ==========================================
if (isset($_POST['edit_category'])) {
    $edit_id = $_POST['edit_id'];
    $old_name = $_POST['old_category_name'];
    $new_name = $_POST['new_category_name'];
    $image_url = $_POST['current_image']; 

    if (!empty($_FILES["new_category_image"]["name"])) {
        $target_dir = "../images/"; 
        $image_name = basename($_FILES["new_category_image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["new_category_image"]["tmp_name"], $target_file)) {
            $image_url = '../images/' . $image_name;
        }
    }

    $conn->query("UPDATE categories SET category_name = '$new_name', image_url = '$image_url' WHERE id = $edit_id");

    if ($old_name != $new_name) {
        $conn->query("UPDATE PRODUCTS SET CATEGORY = '$new_name' WHERE CATEGORY = '$old_name'");
    }
    
    $success_msg = "<div class='alert alert-success'>Category updated successfully!</div>";
}

$cat_query = $conn->query("SELECT * FROM categories ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Categories Matrix | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root { --premium-navy: #002d56; --slate-dark: #0f172a; --slate-muted: #64748b; --border-fine: #e2e8f0; }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--slate-dark); }
        .main-content { padding: 40px; width: 100%; }
        .page-header-flex { border-bottom: 1px solid var(--border-fine); padding-bottom: 20px; margin-bottom: 35px; display: flex; justify-content: space-between; align-items: center; }
        .page-title-text { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; color: var(--premium-navy); text-transform: uppercase; }
        
        .btn-action-edit { background: var(--premium-navy); color: white; border: none; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; display: inline-block; transition: background 0.2s; border-radius: 4px; cursor: pointer;}
        .btn-action-edit:hover { background: #001f3f; color:white; }
        .btn-action-delete { background: transparent; color: var(--slate-muted); border: 1px solid var(--border-fine); padding: 5px 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; display: inline-block; margin-left: 5px; border-radius: 4px; }
        .btn-action-delete:hover { border-color: #0f172a; color: #0f172a; }
        
        .table-box th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--slate-muted); padding: 15px 20px; }
        .table-box td { padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid var(--border-fine); }
        .category-thumbnail { width: 55px; height: 55px; object-fit: cover; border: 1px solid var(--border-fine); border-radius: 6px; background: #fff; padding: 2px; }

        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .modal-content { background: #fff; padding: 30px; width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); position: relative; border: 1px solid var(--border-fine); }
        .close-btn { position: absolute; right: 20px; top: 20px; font-size: 20px; cursor: pointer; color: #888; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--premium-navy); margin-bottom: 8px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid var(--border-fine); box-sizing: border-box; font-size: 0.9rem; outline: none; }
        .btn-submit { width: 100%; background: var(--premium-navy); color: white; padding: 14px; border: none; font-weight: 700; text-transform: uppercase; cursor: pointer; }
        
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; font-size: 13px; border-left: 4px solid; }
        .alert-success { background: #ecfdf5; color: #10b981; border-color: #10b981; }
        .alert-danger { background: #fef2f2; color: #ef4444; border-color: #ef4444; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header-flex">
            <h1 class="page-title-text">Product Categories</h1>
            <button onclick="openModal('addCategoryModal')" class="btn-action-edit" style="padding: 10px 20px;">+ Register New Category</button>
        </div>

        <?php if($error_msg) echo $error_msg; ?>
        <?php if($success_msg) echo $success_msg; ?>

        <div class="table-box" style="background: #ffffff; border: 1px solid var(--border-fine); border-radius: 0px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-fine); background: #ffffff;">
                        <th style="text-align: left;">Category Nomenclature</th>
                        <th style="text-align: left;">Mapped Inventory Assets</th>
                        <th style="text-align: right;">Control Panel Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($cat_query->num_rows > 0) {
                        while($row = $cat_query->fetch_assoc()) {
                            $cat_id = $row['id'];
                            $cat_name = $row['category_name'];
                            $image_url = $row['image_url'];

                            $count_q = $conn->query("SELECT COUNT(*) as cnt FROM PRODUCTS WHERE CATEGORY = '$cat_name'");
                            $item_count = $count_q->fetch_assoc()['cnt'];
                            
                            $img_filename = basename($image_url);
                            $final_img_path = !empty($img_filename) ? "../images/" . $img_filename : "../images/default.png";
                    ?>
                    <tr>
                        <td>
                            <div style='display: flex; align-items: center; gap: 15px;'>
                                <img src='<?php echo htmlspecialchars($final_img_path); ?>' class='category-thumbnail' onerror="this.style.display='none'">
                                <div>
                                    <div style='font-weight: 700; color: var(--slate-dark);'><?php echo htmlspecialchars($cat_name); ?></div>
                                    <div style='font-size: 0.75rem; color: var(--slate-muted); text-transform: uppercase;'>Sector ID: #<?php echo $cat_id; ?></div>
                                </div>
                            </div>
                        </td>
                        <td style='font-size: 0.85rem; font-weight: 600; color: var(--slate-muted); text-transform: uppercase;'>
                            <?php echo $item_count; ?> Active Units
                        <td style='text-align: right;'>
    <a href="manage_product.php?category=<?php echo urlencode($cat_name); ?>" class="btn-action-edit" style="background: var(--slate-muted); margin-right: 5px;" title="View and delete products in this category">Manage Items</a>
    
    <button onclick="openEditModal(<?php echo $cat_id; ?>, '<?php echo addslashes($cat_name); ?>', '<?php echo $image_url; ?>')" class='btn-action-edit'>Modify Matrix</button>
    <a href="categories.php?delete_id=<?php echo $cat_id; ?>&cat_name=<?php echo urlencode($cat_name); ?>" class="btn-action-delete" onclick="return confirm('Confirm deletion of this category asset?');">Remove</a>
</td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding:40px; color: var(--slate-muted); font-size: 0.85rem;'>No categorical matrices mapped into the core index.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addCategoryModal')">&times;</span>
            <h2 style="font-size: 1.1rem; color: var(--premium-navy); margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid var(--border-fine); padding-bottom: 10px; font-weight:800;">Establish Category</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Category Nomenclature</label>
                    <input type="text" name="category_name" required placeholder="e.g. Rackets">
                </div>
                <div class="form-group">
                    <label>Visual Asset File</label>
                    <input type="file" name="category_image" accept="image/*" required style="background: #fff;">
                </div>
                <button type="submit" name="add_category" class="btn-submit">Commit Record</button>
            </form>
        </div>
    </div>

    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editCategoryModal')">&times;</span>
            <h2 style="font-size: 1.1rem; color: var(--premium-navy); margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid var(--border-fine); padding-bottom: 10px; font-weight:800;">Modify Matrix Sector</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id">
                <input type="hidden" name="old_category_name" id="old_category_name">
                <input type="hidden" name="current_image" id="current_image">
                
                <div class="form-group">
                    <label>Category Nomenclature</label>
                    <input type="text" name="new_category_name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Update Visual Asset</label>
                    <input type="file" name="new_category_image" accept="image/*" style="background: #fff;">
                    <small style="color: var(--slate-muted); font-size: 11px; display:block; margin-top:5px;">* Leave blank to retain current system asset mapping.</small>
                </div>
                <button type="submit" name="edit_category" class="btn-submit">Update Record</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) { document.getElementById(modalId).style.display = 'flex'; }
        function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; }
        
        function openEditModal(id, name, imgUrl) {
            document.getElementById('edit_id').value = id;
            document.getElementById('old_category_name').value = name;
            document.getElementById('edit_name').value = name;
            document.getElementById('current_image').value = imgUrl;
            openModal('editCategoryModal');
        }
    </script>
</body>
</html>
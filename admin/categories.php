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
// 💡 2. 删除分类 (Delete) + 防误删保护
// ==========================================
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $del_name = $_GET['cat_name'];

    // 🛡️ 高级防线：去产品库查一下，这个分类下还有没有商品？
    $check_prod = $conn->query("SELECT COUNT(*) as cnt FROM PRODUCTS WHERE CATEGORY = '$del_name'");
    $prod_count = $check_prod->fetch_assoc()['cnt'];

    if ($prod_count > 0) {
        // 如果有商品，坚决不让删！
        $error_msg = "<div class='alert alert-danger'>Cannot delete! There are still <b>$prod_count items</b> inside the '$del_name' category. Please move or delete the products first. (防误删拦截：分类下还有商品，禁止删除！)</div>";
    } else {
        // 如果是空的，允许删除
        $conn->query("DELETE FROM categories WHERE id = $del_id");
        $success_msg = "<div class='alert alert-success'>Category '$del_name' deleted successfully.</div>";
    }
}

// ==========================================
// 💡 3. 修改分类 (Edit) + 智能关联更新
// ==========================================
if (isset($_POST['edit_category'])) {
    $edit_id = $_POST['edit_id'];
    $old_name = $_POST['old_category_name'];
    $new_name = $_POST['new_category_name'];
    $image_url = $_POST['current_image']; // 默认保留旧图片

    // 如果上传了新图片，就处理新图片
    if (!empty($_FILES["new_category_image"]["name"])) {
        $target_dir = "../images/"; 
        $image_name = basename($_FILES["new_category_image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["new_category_image"]["tmp_name"], $target_file)) {
            $image_url = '../images/' . $image_name;
        }
    }

    // 更新分类表
    $conn->query("UPDATE categories SET category_name = '$new_name', image_url = '$image_url' WHERE id = $edit_id");

    // 🛡️ 高级连带更新：如果改了名字，必须把 PRODUCTS 表里对应的商品分类名字也一起改掉！
    if ($old_name != $new_name) {
        $conn->query("UPDATE PRODUCTS SET CATEGORY = '$new_name' WHERE CATEGORY = '$old_name'");
    }
    
    $success_msg = "<div class='alert alert-success'>Category updated successfully!</div>";
}

// 获取所有分类
$cat_query = $conn->query("SELECT * FROM categories ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories - Yonex Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 20px; }
        
        .cat-card { position: relative; height: 400px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; justify-content: flex-end; padding: 25px; border: none; background: white; }
        .cat-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        .cat-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; z-index: 1; transition: transform 0.6s ease; }
        .cat-card:hover .cat-bg { transform: scale(1.1); }
        .cat-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%); z-index: 2; }
        .cat-content { position: relative; z-index: 3; color: white; display: flex; justify-content: space-between; align-items: flex-end;}
        
        .cat-info h3 { font-size: 26px; font-weight: 900; margin-bottom: 8px; letter-spacing: 2px; color: white; text-transform: uppercase; }
        .cat-info p { background: #e60012; color: white; padding: 6px 14px; border-radius: 20px; font-size: 14px; font-weight: bold; display: inline-block; margin: 0; }

        /* 新增：卡片上的操作按钮 */
        .cat-actions { display: flex; gap: 8px; flex-direction: column;}
        .btn-sm { padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; text-decoration: none; text-align: center; border: none; }
        .btn-edit { background: #c9a84c; color: white; } /* 金色 Edit */
        .btn-edit:hover { background: #b5953b; }
        .btn-delete { background: #1e293b; color: white; } /* 深灰 Delete */
        .btn-delete:hover { background: #0f172a; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal-content { background-color: #fff; padding: 30px; border-radius: 8px; width: 400px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative; }
        .close-btn { position: absolute; right: 20px; top: 20px; font-size: 24px; cursor: pointer; color: #888; }
        .close-btn:hover { color: #e60012; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #0033a0; font-size: 14px;}
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; font-size: 14px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header-flex">
            <h1 style="color: #0033a0; font-weight: 900; text-transform: uppercase;">Product Categories</h1>
            <button onclick="openModal('addCategoryModal')" class="btn-sm" style="background: #0033a0; color: white; padding: 12px 20px; font-size: 14px;">+ Add New Category</button>
        </div>

        <?php if($error_msg) echo $error_msg; ?>
        <?php if($success_msg) echo $success_msg; ?>

        <div class="cat-grid">
            <?php
            if ($cat_query->num_rows > 0) {
                while($row = $cat_query->fetch_assoc()) {
                    $cat_id = $row['id'];
                    $cat_name = $row['category_name'];
                    $image_url = $row['image_url'];

                    $count_q = $conn->query("SELECT COUNT(*) as cnt FROM PRODUCTS WHERE CATEGORY = '$cat_name'");
                    $item_count = $count_q->fetch_assoc()['cnt'];
            ?>
            <div class="cat-card">
                <a href="manage_product.php?category=<?php echo urlencode($cat_name); ?>" class="cat-bg" style="background-image: url('<?php echo $image_url; ?>');"></a>
                <div class="cat-overlay" style="pointer-events: none;"></div>
                
                <div class="cat-content">
                    <div class="cat-info">
                        <h3><?php echo htmlspecialchars($cat_name); ?></h3>
                        <p><?php echo $item_count; ?> Items</p>
                    </div>
                    
                    <div class="cat-actions">
                        <button onclick="openEditModal(<?php echo $cat_id; ?>, '<?php echo addslashes($cat_name); ?>', '<?php echo $image_url; ?>')" class="btn-sm btn-edit">Edit</button>
                        <a href="categories.php?delete_id=<?php echo $cat_id; ?>&cat_name=<?php echo urlencode($cat_name); ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete the category: <?php echo $cat_name; ?>?');">Delete</a>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo "<p>No categories found.</p>";
            }
            ?>
        </div>
    </div>

    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addCategoryModal')">&times;</span>
            <h2 style="color: #0033a0; border-bottom: 2px solid #e60012; padding-bottom: 10px; margin-bottom: 20px;">Add New Category</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="category_name" required>
                </div>
                <div class="form-group">
                    <label>Background Image</label>
                    <input type="file" name="category_image" accept="image/*" required>
                </div>
                <button type="submit" name="add_category" class="btn-sm" style="width: 100%; background: #e60012; color: white; padding: 12px;">Save Category</button>
            </form>
        </div>
    </div>

    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editCategoryModal')">&times;</span>
            <h2 style="color: #c9a84c; border-bottom: 2px solid #c9a84c; padding-bottom: 10px; margin-bottom: 20px;">Edit Category</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id">
                <input type="hidden" name="old_category_name" id="old_category_name">
                <input type="hidden" name="current_image" id="current_image">
                
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="new_category_name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Update Background Image (Optional)</label>
                    <input type="file" name="new_category_image" accept="image/*">
                    <small style="color: #888;">* Leave empty if you want to keep the current image.</small>
                </div>
                <button type="submit" name="edit_category" class="btn-sm" style="width: 100%; background: #c9a84c; color: white; padding: 12px;">Update Category</button>
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
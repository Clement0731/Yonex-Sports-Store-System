<?php
session_start();
// 1. 基础登录拦截
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$current_admin_id = $_SESSION['admin_id'];

// 2. 绝对防御结界：查验当前账号是否为 Superadmin
$check_super = $conn->query("SELECT ROLE, EMAIL FROM admin WHERE USER_ID = '$current_admin_id'");
if ($check_super && $check_super->num_rows > 0) {
    $current_role_row = $check_super->fetch_assoc();
    if ($current_role_row['ROLE'] !== 'Superadmin') {
        echo "<script>alert('SECURITY ALERT: Root Superadmin Privileges Required! Access Denied.'); window.location.href='dashboard.php';</script>";
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

$msg = "";

// ==========================================
// 💡 功能：添加新管理员 (Staff选项已移除)
// ==========================================
if (isset($_POST['add_staff'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $role = $conn->real_escape_string($_POST['role']); // 默认已经是 Admin

    $check_email = $conn->query("SELECT * FROM admin WHERE EMAIL = '$email'");
    if ($check_email->num_rows > 0) {
        $msg = "<div class='alert alert-danger'>Email already exists in the system!</div>";
    } elseif (!preg_match("/^(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
        $msg = "<div class='alert alert-danger'>Weak Password! Minimum 8 chars, 1 Uppercase, 1 Number required.</div>";
    } else {
        $conn->query("INSERT INTO admin (USERNAME, EMAIL, PASSWORD, ROLE, STATUS) VALUES ('$username', '$email', '$password', '$role', 'Active')");
        header("Location: manage_admin.php?success=added");
        exit();
    }
}

// ==========================================
// 💡 功能：修改管理员资料
// ==========================================
if (isset($_POST['edit_staff'])) {
    $staff_id = (int)$_POST['edit_id'];
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);
    
    $password_update = "";
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        if (preg_match("/^(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
            $password_update = ", PASSWORD = '$password'";
        } else {
            header("Location: manage_admin.php?error=weak_pwd");
            exit();
        }
    }

    $conn->query("UPDATE admin SET USERNAME='$username', EMAIL='$email', ROLE='$role' $password_update WHERE USER_ID=$staff_id");
    header("Location: manage_admin.php?success=updated");
    exit();
}

// ==========================================
// 💡 功能：软删除 / 停用 (Deactivate) 与 恢复 (Reactivate)
// ==========================================
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];
    $action = $_GET['action'];

    // 绝对安全防御：禁止停用自己
    if ($target_id == $current_admin_id) {
        $msg = "<div class='alert alert-danger'>Action Blocked: You cannot deactivate your own Superadmin account!</div>";
    } else {
        if ($action === 'deactivate') {
            $conn->query("UPDATE admin SET STATUS = 'Deactivated' WHERE USER_ID = $target_id");
            header("Location: manage_admin.php?success=deactivated");
            exit();
        } elseif ($action === 'reactivate') {
            $conn->query("UPDATE admin SET STATUS = 'Active' WHERE USER_ID = $target_id");
            header("Location: manage_admin.php?success=reactivated");
            exit();
        }
    }
}

// 成功/失败通知
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'added') $msg = "<div class='alert alert-success'>New admin account provisioned successfully.</div>";
    if ($_GET['success'] == 'updated') $msg = "<div class='alert alert-success'>Admin credentials updated successfully.</div>";
    if ($_GET['success'] == 'deactivated') $msg = "<div class='alert alert-success'>Account has been suspended and greyed out.</div>";
    if ($_GET['success'] == 'reactivated') $msg = "<div class='alert alert-success'>Account access has been restored.</div>";
}
if (isset($_GET['error']) && $_GET['error'] == 'weak_pwd') {
    $msg = "<div class='alert alert-danger'>Action Rejected: Password did not meet security requirements.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Root Terminal | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root { --premium-navy: #002d56; --slate-dark: #0f172a; --slate-muted: #64748b; --border-fine: #e2e8f0; }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--slate-dark); }
        .main-content { padding: 40px; width: 100%; }
        .page-header-flex { border-bottom: 1px solid var(--border-fine); padding-bottom: 20px; margin-bottom: 35px; display: flex; justify-content: space-between; align-items: center; }
        .page-title-text { font-size: 1.5rem; font-weight: 900; letter-spacing: -0.02em; color: #b91c1c; text-transform: uppercase; margin: 0; }
        
        .btn-action-add { background: #b91c1c; color: white; border: none; padding: 10px 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; cursor: pointer; border-radius: 4px; box-shadow: 0 4px 6px rgba(185, 28, 28, 0.2);}
        .btn-action-add:hover { background: #991b1b; }
        
        .btn-action-edit { background: var(--premium-navy); color: white; border: none; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; border-radius: 4px; cursor: pointer; text-decoration: none;}
        .btn-action-delete { background: transparent; color: #ef4444; border: 1px solid #fca5a5; padding: 5px 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; text-decoration: none; border-radius: 4px; margin-left: 5px;}
        .btn-action-delete:hover { background: #fef2f2; }
        
        .btn-action-reactivate { background: transparent; color: #10b981; border: 1px solid #6ee7b7; padding: 5px 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; text-decoration: none; border-radius: 4px; margin-left: 5px; }
        .btn-action-reactivate:hover { background: #ecfdf5; }
        
        .table-box th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--slate-muted); padding: 15px 20px; }
        .table-box td { padding: 18px 20px; border-bottom: 1px solid var(--border-fine); transition: all 0.3s ease; }
        
        /* 状态与角色徽章 */
        .badge-role { font-size: 0.65rem; font-weight: 700; padding: 4px 8px; border-radius: 3px; text-transform: uppercase; margin-right: 5px;}
        .role-super { background: #fef2f2; color: #ef4444; border: 1px solid #fca5a5; }
        .role-admin { background: #ecfdf5; color: #10b981; border: 1px solid #6ee7b7; }
        .status-inactive { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }

        /* 被停用账号的视觉效果 (灰色半透明) */
        .row-deactivated { opacity: 0.5; background-color: #f8fafc; filter: grayscale(100%); }
        .row-deactivated:hover { opacity: 0.8; filter: grayscale(50%); }

        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
        .modal-content { background: white; padding: 30px; width: 420px; border: 1px solid #ef4444; border-top: 4px solid #ef4444; position: relative; }
        .close-btn { position: absolute; top: 20px; right: 20px; font-size: 20px; cursor: pointer; color: #888; border: none; background: none; }
        
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
            <div>
                <h1 class="page-title-text">Root Superadmin Terminal</h1>
                <p style="font-size: 0.85rem; color: var(--slate-muted); text-transform: uppercase; margin-top: 5px;">Exclusive Access Control Panel</p>
            </div>
            <button class="btn-action-add" onclick="openAddModal()">+ Provision New Admin</button>
        </div>

        <?php echo $msg; ?>

        <div class="table-box" style="background: #ffffff; border: 1px solid var(--border-fine); border-radius: 0px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-fine); background: #f8fafc;">
                        <th style="text-align: left;">Staff Identifier</th>
                        <th style="text-align: left;">Username</th>
                        <th style="text-align: left;">Email Address</th>
                        <th style="text-align: left;">Access Level & Status</th>
                        <th style="text-align: right;">Root Controls</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM admin ORDER BY FIELD(ROLE, 'Superadmin', 'Admin'), USER_ID ASC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $s_id = $row['USER_ID'];
                            $s_role = $row['ROLE'] ?? 'Admin';
                            $s_status = $row['STATUS'] ?? 'Active'; // 获取状态
                            
                            $role_class = (strcasecmp($s_role, 'Superadmin') == 0) ? 'role-super' : 'role-admin';
                            
                            $is_self = ($s_id == $current_admin_id);

                            // 如果是被停用的账号，给 <tr> 加上置灰的 CSS Class
                            $row_class = ($s_status === 'Deactivated') ? 'row-deactivated' : '';

                            echo "<tr class='{$row_class}'>";
                            echo "<td><strong># STF-" . str_pad($s_id, 4, '0', STR_PAD_LEFT) . "</strong> " . ($is_self ? "<small style='color:#ef4444; font-weight:bold;'>(You)</small>" : "") . "</td>";
                            echo "<td><span style='font-weight:700; color:var(--slate-dark);'>" . htmlspecialchars($row['USERNAME']) . "</span></td>";
                            echo "<td><span style='font-family:monospace;'>" . htmlspecialchars($row['EMAIL']) . "</span></td>";
                            
                            echo "<td>
                                    <span class='badge-role {$role_class}'>" . htmlspecialchars($s_role) . "</span>";
                            if ($s_status === 'Deactivated') {
                                echo "<span class='badge-role status-inactive'>Deactivated</span>";
                            }
                            echo "</td>";

                            echo "<td style='text-align: right;'>
                                    <button class='btn-action-edit' onclick=\"openEditModal('{$s_id}', '{$row['USERNAME']}', '{$row['EMAIL']}', '{$s_role}')\">Edit</button>";
                            
                            if (!$is_self) {
                                if ($s_status === 'Active') {
                                    // 显示停用按钮
                                    echo "<a href='manage_admin.php?action=deactivate&id={$s_id}' class='btn-action-delete' onclick='return confirm(\"Suspend this admin account?\");'>Deactivate</a>";
                                } else {
                                    // 显示恢复按钮
                                    echo "<a href='manage_admin.php?action=reactivate&id={$s_id}' class='btn-action-reactivate' onclick='return confirm(\"Restore access for this admin account?\");'>Reactivate</a>";
                                }
                            }
                            
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
            <h2 style="font-size: 1.1rem; color: #b91c1c; margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid var(--border-fine); padding-bottom: 10px; font-weight:800;">PROVISION NEW ADMIN</h2>
            <form method="POST" action="manage_admin.php">
                <div class="form-group">
                    <label>Admin Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Access Level</label>
                    <select name="role" required>
                        <option value="Admin">Admin (Full Control)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Initial Password</label>
                    <input type="password" name="password" placeholder="Min 8 chars, 1 Uppercase, 1 Number" required>
                </div>
                <button type="submit" name="add_staff" class="btn-submit" style="background:#b91c1c;">Create Account</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
            <h2 style="font-size: 1.1rem; color: var(--premium-navy); margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid var(--border-fine); padding-bottom: 10px; font-weight:800;">MODIFY ADMIN MATRIX</h2>
            <form method="POST" action="manage_admin.php">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="edit_username" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label>Access Level</label>
                    <select name="role" id="edit_role" required>
                        <option value="Superadmin">Superadmin (Root)</option>
                        <option value="Admin">Admin (Full Control)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="Min 8 chars, 1 Uppercase, 1 Number">
                </div>
                <button type="submit" name="edit_staff" class="btn-submit">Update Parameters</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() { document.getElementById('addModal').style.display = 'flex'; }
        function openEditModal(id, username, email, role) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            document.getElementById('editModal').style.display = 'flex';
        }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    </script>
</body>
</html>
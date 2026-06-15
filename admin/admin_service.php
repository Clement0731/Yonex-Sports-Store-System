<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../db_connect.php'; 

// [DELETE]
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM service_options WHERE id = $delete_id");
    header("Location: admin_service.php"); 
    exit();
}

// [ADD]
if (isset($_POST['add_service'])) {
    $type = $conn->real_escape_string($_POST['service_type']);
    $name = $conn->real_escape_string($_POST['option_name']);
    $price = (float)$_POST['additional_price'];
    $conn->query("INSERT INTO service_options (service_type, option_name, additional_price) VALUES ('$type', '$name', '$price')");
    header("Location: admin_service.php");
    exit();
}

// [EDIT]
if (isset($_POST['edit_service'])) {
    $id = (int)$_POST['edit_id'];
    $type = $conn->real_escape_string($_POST['service_type']);
    $name = $conn->real_escape_string($_POST['option_name']);
    $price = (float)$_POST['additional_price'];
    $conn->query("UPDATE service_options SET service_type='$type', option_name='$name', additional_price='$price' WHERE id=$id");
    header("Location: admin_service.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Configurations | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root { --premium-navy: #002d56; --slate-dark: #0f172a; --slate-muted: #64748b; --border-fine: #e2e8f0; }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--slate-dark); }
        .main-content { padding: 40px; width: 100%; }
        .page-header-flex { border-bottom: 1px solid var(--border-fine); padding-bottom: 20px; margin-bottom: 35px; display: flex; justify-content: space-between; align-items: center; }
        .page-title-text { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; color: var(--premium-navy); text-transform: uppercase; margin: 0; }
        
        .btn-action-edit { background: var(--premium-navy); color: white; border: none; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; display: inline-block; transition: background 0.2s; border-radius: 4px; cursor: pointer; }
        .btn-action-edit:hover { background: #001f3f; color: white; }
        .btn-action-delete { background: transparent; color: var(--slate-muted); border: 1px solid var(--border-fine); padding: 5px 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; display: inline-block; margin-left: 5px; border-radius: 4px; }
        .btn-action-delete:hover { border-color: #0f172a; color: #0f172a; }
        
        .table-box th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--slate-muted); padding: 15px 20px; }
        .table-box td { padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid var(--border-fine); }
        
        .badge-type { font-size: 0.65rem; font-weight: 700; padding: 4px 8px; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; }
        .type-string { background: #ecfdf5; color: #10b981; border: 1px solid #6ee7b7; }
        .type-tension { background: #fff3e0; color: #f57c00; border: 1px solid #ffe0b2; }
        .type-printing { background: #fef2f2; color: #ef4444; border: 1px solid #fca5a5; }

        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
        .modal-content { background: white; padding: 30px; width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); position: relative; border: 1px solid var(--border-fine); }
        .close-btn { position: absolute; top: 20px; right: 20px; font-size: 20px; cursor: pointer; color: #888; border: none; background: none; }
        .close-btn:hover { color: #000; }
        
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--premium-navy); margin-bottom: 8px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid var(--border-fine); box-sizing: border-box; font-size: 0.9rem; outline: none; }
        .btn-submit { width: 100%; background: var(--premium-navy); color: white; padding: 14px; border: none; font-weight: 700; text-transform: uppercase; cursor: pointer; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header-flex">
            <h1 class="page-title-text">Service Configuration</h1>
            <button class="btn-action-edit" style="padding: 10px 20px;" onclick="openModal('addModal')">+ Create New Service Option</button>
        </div>

        <div class="table-box" style="background: #ffffff; border: 1px solid var(--border-fine); border-radius: 0px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-fine); background: #ffffff;">
                        <th style="text-align: left;">Service Category</th>
                        <th style="text-align: left;">Option Specification Name</th>
                        <th style="text-align: left;">Additional Price Surcharge</th>
                        <th style="text-align: right;">Action Controls</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM service_options ORDER BY service_type ASC, additional_price ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $type_class = 'type-string';
                            if ($row['service_type'] == 'tension') $type_class = 'type-tension';
                            if ($row['service_type'] == 'printing') $type_class = 'type-printing';

                            echo "<tr>";
                            echo "<td><span class='badge-type {$type_class}'>" . htmlspecialchars($row['service_type']) . "</span></td>";
                            echo "<td><strong>" . htmlspecialchars($row['option_name']) . "</strong></td>";
                            echo "<td style='color: var(--premium-navy); font-weight: bold;'>+ RM " . number_format($row['additional_price'], 2) . "</td>";
                            echo "<td style='text-align: right;'>
                                    <button class='btn-action-edit' onclick=\"openEditModal('{$row['id']}', '{$row['service_type']}', '{$row['option_name']}', '{$row['additional_price']}')\">Modify Matrix</button>
                                    <a href='admin_service.php?delete={$row['id']}' class='btn-action-delete' onclick='return confirm(\"Permanently remove this service option matrix?\");'>Remove</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding: 40px; color: var(--slate-muted); font-size: 0.85rem;'>No service option modules configured within the framework.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
            <h2 style="font-size: 1.1rem; color: var(--premium-navy); margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid var(--border-fine); padding-bottom: 10px; font-weight:800;">Add New Option</h2>
            <form method="POST" action="admin_service.php">
                <div class="form-group">
                    <label>Service Category</label>
                    <select name="service_type" required>
                        <option value="string">Racket Stringing</option>
                        <option value="tension">String Tension</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Option Name</label>
                    <input type="text" name="option_name" placeholder="e.g. EXBOLT 63" required>
                </div>
                <div class="form-group">
                    <label>Extra Price Surcharge (RM)</label>
                    <input type="number" step="0.01" name="additional_price" placeholder="0.00" required>
                </div>
                <button type="submit" name="add_service" class="btn-submit">Save Configuration</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
            <h2 style="font-size: 1.1rem; color: var(--premium-navy); margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid var(--border-fine); padding-bottom: 10px; font-weight:800;">Modify Matrix Option</h2>
            <form method="POST" action="admin_service.php">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="form-group">
                    <label>Service Category</label>
                    <select name="service_type" id="edit_type" required>
                        <option value="string">Racket Stringing</option>
                        <option value="tension">String Tension</option>
                        <option value="printing">Name Printing (Apparel)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Option Name</label>
                    <input type="text" name="option_name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Extra Price Surcharge (RM)</label>
                    <input type="number" step="0.01" name="additional_price" id="edit_price" required>
                </div>
                <button type="submit" name="edit_service" class="btn-submit">Update Configuration</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) { document.getElementById(modalId).style.display = 'flex'; }
        function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; }

        function openEditModal(id, type, name, price) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            openModal('editModal');
        }
    </script>
</body>
</html>
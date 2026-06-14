<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../db_connect.php'; 

// ==========================================
// 1. Handle CRUD Operations
// ==========================================

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
    <title>Service Configurations | Yonex Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .main-content { padding: 40px; width: 100%; }
        .page-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .page-header h1 { color: #0033a0; font-weight: 800; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        
        .btn-add { background: #0033a0; color: white; border: none; padding: 10px 20px; font-weight: bold; cursor: pointer; border-radius: 4px; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .btn-add:hover { background: #002277; }
        
        .table-box { background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .table-box table { width: 100%; border-collapse: collapse; }
        .table-box th { background: #f8f9fa; padding: 15px 20px; text-align: left; font-size: 13px; font-weight: 700; color: #555; text-transform: uppercase; border-bottom: 2px solid #eee; }
        .table-box td { padding: 15px 20px; border-bottom: 1px solid #eee; color: #333; font-size: 14px; vertical-align: middle; }
        
        .badge-type { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .type-string { background: #e3f2fd; color: #1976d2; }
        .type-tension { background: #fff3e0; color: #f57c00; }
        .type-printing { background: #fce4ec; color: #c2185b; } /* New Badge Style for Printing */

        .btn-edit { background: #f3f4f6; color: #333; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 5px; border: 1px solid #ddd; display: inline-block;}
        .btn-edit:hover { background: #e2e8f0; }
        .btn-delete { background: white; color: #e60012; border: 1px solid #e60012; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block;}
        .btn-delete:hover { background: #e60012; color: white; }

        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; }
        .modal-content { background: white; padding: 30px; border-radius: 8px; width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; }
        .close-btn { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: #888; border: none; background: none; }
        .close-btn:hover { color: #e60012; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 13px; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;}
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1>Service & Add-ons Configuration</h1>
            <button class="btn-add" onclick="openModal('addModal')">+ Create New Service Option</button>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Service Category</th>
                        <th>Option Specification Name</th>
                        <th>Additional Price Surcharge</th>
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
                            if ($row['service_type'] == 'printing') $type_class = 'type-printing'; // Apply new badge class

                            echo "<tr>";
                            echo "<td><span class='badge-type {$type_class}'>" . htmlspecialchars($row['service_type']) . "</span></td>";
                            echo "<td><strong>" . htmlspecialchars($row['option_name']) . "</strong></td>";
                            echo "<td style='color: #e60012; font-weight: bold;'>+ RM " . number_format($row['additional_price'], 2) . "</td>";
                            echo "<td style='text-align: right;'>
                                    <button class='btn-edit' onclick=\"openEditModal('{$row['id']}', '{$row['service_type']}', '{$row['option_name']}', '{$row['additional_price']}')\">Modify</button>
                                    <a href='admin_service.php?delete={$row['id']}' class='btn-delete' onclick='return confirm(\"Permanently remove this service option?\");'>Remove</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding: 20px; color: #888;'>No service options configured yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="addModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
            <h3 style="margin-bottom: 20px; color: #0033a0; border-bottom: 2px solid #eee; padding-bottom: 10px;">Add New Option</h3>
            <form method="POST" action="admin_service.php">
                <div class="form-group">
                    <label>Service Category</label>
                    <select name="service_type" required>
                        <option value="string">Racket Stringing</option>
                        <option value="tension">String Tension</option>
                        <option value="printing">Name Printing (Apparel)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Option Name</label>
                    <input type="text" name="option_name" placeholder="e.g. EXBOLT 63 or Gold Printing" required>
                </div>
                <div class="form-group">
                    <label>Extra Price Surcharge (RM)</label>
                    <input type="number" step="0.01" name="additional_price" placeholder="0.00" required>
                </div>
                <button type="submit" name="add_service" class="btn btn-add" style="width: 100%; justify-content: center; margin-top: 10px;">Save Configuration</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
            <h3 style="margin-bottom: 20px; color: #0033a0; border-bottom: 2px solid #eee; padding-bottom: 10px;">Modify Option</h3>
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
                <button type="submit" name="edit_service" class="btn btn-add" style="width: 100%; justify-content: center; margin-top: 10px;">Update Configuration</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

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
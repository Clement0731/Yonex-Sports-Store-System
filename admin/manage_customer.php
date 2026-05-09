<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if(isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $conn->query("DELETE FROM USERS WHERE USER_ID = $del_id AND ROLE = 'Customer'");
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
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1>Manage Customers</h1>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $customers = $conn->query("SELECT * FROM USERS WHERE ROLE = 'Customer' ORDER BY USER_ID DESC");
                    if ($customers->num_rows > 0) {
                        while($row = $customers->fetch_assoc()) {
                            echo "<tr>
                                    <td>".$row['USER_ID']."</td>
                                    <td>".$row['USERNAME']."</td>
                                    <td>".$row['EMAIL']."</td>
                                    <td>".$row['ROLE']."</td>
                                    <td>
                                        <a href='manage_customer.php?delete=".$row['USER_ID']."' class='btn btn-delete'>Remove</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No customers found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
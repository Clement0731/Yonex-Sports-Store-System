<?php
include '../db_connect.php'; 

$sql = "SELECT * FROM service_options ORDER BY service_type, id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Services - Yonex Admin</title>
    
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        
        <div class="header-flex">
            <h1 class="page-title">Manage Services</h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-back">← Back</a>
                <a href="#" class="btn btn-add">+ Add New Option</a>
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
                            <a href="#" class="btn btn-edit">Edit</a>
                            <a href="#" class="btn btn-delete" style="margin-left: 5px;">Delete</a>
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

</body>
</html>
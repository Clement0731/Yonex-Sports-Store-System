<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1>Inventory Control</h1>
        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Stock Level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>YNX-RK-01</td><td>Astrox 100 ZZ</td><td>45</td><td><span class="badge bg-success">In Stock</span></td></tr>
                    <tr><td>YNX-SH-05</td><td>SHB 65 Z3</td><td>3</td><td><span class="badge bg-warning">Low Stock</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
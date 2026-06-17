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
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: var(--slate-muted, #94a3b8); font-size: 0.9rem;">
                            No inventory records found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
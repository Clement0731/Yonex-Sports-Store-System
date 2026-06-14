<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if(isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $del_id");
    header("Location: manage_product.php");
    exit();
}

$where_clause = "";
$page_title = "ALL RETAIL PRODUCTS";

if (isset($_GET['category'])) {
    $cat = $conn->real_escape_string($_GET['category']);
    $where_clause = "WHERE p.category = '$cat'";
    $page_title = strtoupper($cat) . " SPECIFIC PRODUCTS";
}

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE p.name LIKE '%$search%' OR p.series LIKE '%$search%'";
    $page_title = "SEARCH FILTER: '" . strtoupper(htmlspecialchars($search)) . "'";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Control & Inventory | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root { 
            --premium-navy: #002d56; 
            --slate-dark: #0f172a; 
            --slate-muted: #64748b; 
            --border-color: #e2e8f0; 
        }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--slate-dark); }
        .main-content { padding: 40px; width: 100%; }
        .page-header-flex { border-bottom: 1px solid var(--border-color); padding-bottom: 20px; margin-bottom: 35px; display: flex; justify-content: space-between; align-items: center; }
        .page-title-text { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; color: var(--premium-navy); }
        
        .variant-spec-box { background: #f8fafc; border: 1px solid var(--border-color); padding: 8px 12px; margin-top: 5px; font-size: 0.8rem; line-height: 1.5; color: var(--slate-muted); text-align: left; }
        .variant-title-line { font-weight: 700; color: var(--slate-dark); border-bottom: 1px solid var(--border-color); padding-bottom: 2px; margin-bottom: 4px; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em;}
        
        .stock-indicator-badge { font-size: 0.85rem; font-weight: 700; letter-spacing: -0.01em; }
        .status-instock { color: #0f172a; }
        .status-outofstock { color: #94a3b8; text-decoration: line-through; }

        .btn-action-edit { background: var(--premium-navy); color: white; border: none; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; display: inline-block; transition: background 0.2s; border-radius: 4px; }
        .btn-action-edit:hover { background: #001f3f; color:white; }
        .btn-action-delete { background: transparent; color: var(--slate-muted); border: 1px solid var(--border-color); padding: 5px 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; display: inline-block; margin-left: 5px; border-radius: 4px; }
        .btn-action-delete:hover { border-color: #0f172a; color: #0f172a; }
        
        .table-box th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--slate-muted); padding: 15px 20px; }
        .table-box td { padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid var(--border-color); }
        .product-thumbnail { width: 55px; height: 55px; object-fit: contain; border: 1px solid var(--border-color); border-radius: 6px; background: #fff; padding: 2px; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="page-header-flex">
            <h1 class="page-title-text"><?php echo $page_title; ?></h1>
            <a href="add_product.php" class="btn-action-edit" style="padding: 10px 20px;">+ Register New Asset</a>
        </div>

        <div class="table-box" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 0px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); background: #ffffff;">
                        <th style="text-align: left;">Product Info</th>
                        <th style="text-align: left;">Collection / Series</th>
                        <th style="text-align: left;">Stock Allocations (Specifications)</th>
                        <th style="text-align: left;">MSRP Price</th>
                        <th style="text-align: right;">Control Panel Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.*, IFNULL(SUM(pv.stock_quantity), 0) as total_stock 
                            FROM products p 
                            LEFT JOIN product_variants pv ON p.id = pv.product_id 
                            $where_clause 
                            GROUP BY p.id 
                            ORDER BY p.id DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $p_id = $row['id'];
                            $v_res = $conn->query("SELECT * FROM product_variants WHERE product_id = $p_id");
                            $variant_html = "";
                            
                            if($v_res && $v_res->num_rows > 0) {
                                $variant_html .= "<div class='variant-spec-box'><div class='variant-title-line'>Variant Breakdown</div>";
                                while($v_row = $v_res->fetch_assoc()) {
                                    $variant_html .= "• " . htmlspecialchars($v_row['spec_value']) . ": <b>" . $v_row['stock_quantity'] . " units</b> available<br>";
                                }
                                $variant_html .= "</div>";
                            }

                            $stock_status_class = ($row['total_stock'] > 0) ? 'status-instock' : 'status-outofstock';
                            $stock_text = ($row['total_stock'] > 0) ? $row['total_stock'] : 'OUT OF STOCK';
                            
                            // 💡 修复图片显示逻辑：只提取文件名，并指向正确的 images 文件夹
                            $img_filename = basename($row['image_url']);
                            $final_img_path = !empty($img_filename) ? "../images/" . $img_filename : "../images/default.png";

                            echo "<tr>
                                    <td>
                                        <div style='display: flex; align-items: center; gap: 15px;'>
                                            <img src='".htmlspecialchars($final_img_path)."' class='product-thumbnail' onerror=\"this.style.display='none'\">
                                            <div>
                                                <div style='font-weight: 700; color: var(--slate-dark);'>".htmlspecialchars($row['name'])."</div>
                                                <div style='font-size: 0.75rem; color: var(--slate-muted); text-transform: uppercase;'>Category: ".htmlspecialchars($row['category'])."</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style='font-size: 0.85rem; font-weight: 600; color: var(--slate-muted); text-transform: uppercase;'>
                                        ".htmlspecialchars($row['series'])."
                                    </td>
                                    <td>
                                        <div class='stock-indicator-badge {$stock_status_class}'>
                                            Total: <span>{$stock_text}</span>
                                        </div>
                                        {$variant_html}
                                    </td>
                                    <td style='font-weight: 700; color: var(--premium-navy); font-size: 0.95rem;'>
                                        RM ".number_format((float)$row['price'], 2)."
                                    </td>
                                    <td style='text-align: right;'>
                                        <a href='edit_product.php?id={$row['id']}' class='btn-action-edit'>Modify Matrix</a>
                                        <a href='manage_product.php?delete={$row['id']}' class='btn-action-delete' onclick=\"return confirm('Confirm deletion of this product asset? This action is non-reversible.');\">Remove</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding:40px; color: var(--slate-muted); font-size: 0.85rem;'>No active product records found within the system index.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
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
$page_title = "All Products";

if (isset($_GET['category'])) {
    $cat = $conn->real_escape_string($_GET['category']);
    $where_clause = "WHERE p.category = '$cat'";
    $page_title = $cat . " Products";
}

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE p.name LIKE '%$search%' OR p.series LIKE '%$search%'";
    $page_title = "Search Results: '" . htmlspecialchars($search) . "'";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1><?php echo $page_title; ?></h1>
            <a href="add_product.php" class="btn btn-add">+ Add New Product</a>
        </div>
        
        <div style="background: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <form action="manage_product.php" method="GET" style="display: flex; gap: 10px; align-items: center; margin: 0;">
                <input type="text" name="search" placeholder="Search product or series..." 
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
                       style="padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
                <button type="submit" class="btn btn-add" style="padding: 10px 20px;">Search</button>
                <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <a href="manage_product.php" class="btn" style="background:#cbd5e1; color:#1e293b; text-decoration:none; padding: 10px 20px;">Clear</a>
                <?php endif; ?>
            </form>
            <span style="color: #666; font-size: 14px;">Use keyword to find products quickly</span>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th style="width: 100px;">Image</th>
                        <th style="width: 230px;">Product Name & Info</th>
                        <th style="width: 150px;">Category / Series</th>
                        <th style="width: 250px;">Specs Preview</th>
                        <th>Total Stock</th>
                        <th>Price</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.*, IFNULL(SUM(v.stock_quantity), 0) as total_stock 
                            FROM products p 
                            LEFT JOIN product_variants v ON p.id = v.product_id 
                            $where_clause 
                            GROUP BY p.id 
                            ORDER BY p.id DESC";
                            
                    $products = $conn->query($sql);
                    
                    if ($products->num_rows > 0) {
                        while($row = $products->fetch_assoc()) {
                            $img_name = basename($row['image_url']);
                            $img_src = !empty($img_name) ? '../images/' . $img_name : 'https://via.placeholder.com/50';
                            
                            $subtitle = !empty($row['subtitle']) ? htmlspecialchars($row['subtitle']) : '<span style="color:#aaa; font-style:italic;">No subtitle</span>';
                            
                            $cat = $row['category'];
                            $spec1 = !empty($row['racket_flex']) ? htmlspecialchars($row['racket_flex']) : '';
                            $spec2 = !empty($row['racket_balance']) ? htmlspecialchars($row['racket_balance']) : '';
                            $spec3 = !empty($row['string_tension']) ? htmlspecialchars($row['string_tension']) : '';
                            $spec4 = !empty($row['frame_material']) ? htmlspecialchars($row['frame_material']) : '';
                            $spec5 = !empty($row['shaft_material']) ? htmlspecialchars($row['shaft_material']) : '';
                            $color = !empty($row['color']) ? htmlspecialchars($row['color']) : '';

                            $specs_html = "";
                            
                            if ($cat == 'Rackets') {
                                if($spec1) $specs_html .= "<b>Flex:</b> $spec1<br>";
                                if($spec2) $specs_html .= "<b>Bal:</b> $spec2<br>";
                                if($spec3) $specs_html .= "<b>Tension:</b> $spec3<br>";
                                if($spec4) $specs_html .= "<b>Frame:</b> <span style='font-size:11px;'>$spec4</span><br>";
                                if($spec5) $specs_html .= "<b>Shaft:</b> <span style='font-size:11px;'>$spec5</span><br>";
                            } elseif ($cat == 'Footwear') {
                                $upper = $spec1 ?: $spec4; 
                                $midsole = $spec2 ?: $spec5;
                                $outsole = $spec3;
                                $width = $spec1 ? $spec4 : ''; 
                                
                                if($upper) $specs_html .= "<b>Upper:</b> <span style='font-size:11px;'>$upper</span><br>";
                                if($midsole) $specs_html .= "<b>Midsole:</b> <span style='font-size:11px;'>$midsole</span><br>";
                                if($outsole) $specs_html .= "<b>Outsole:</b> $outsole<br>";
                                if($width) $specs_html .= "<b>Width:</b> $width<br>";
                            } elseif ($cat == 'Shuttlecocks') {
                                $speed = $spec1 ?: '77 Speed';
                                $app = $spec2;
                                $qty = $spec3;
                                $skirt = $spec4;
                                $base = $spec5;
                                
                                if($speed) $specs_html .= "<b>Speed:</b> $speed<br>";
                                if($app) $specs_html .= "<b>App:</b> $app<br>";
                                if($qty) $specs_html .= "<b>Qty:</b> $qty<br>";
                                if($skirt) $specs_html .= "<b>Skirt:</b> <span style='font-size:11px;'>$skirt</span><br>";
                                if($base) $specs_html .= "<b>Base:</b> <span style='font-size:11px;'>$base</span><br>";
                            } elseif ($cat == 'Apparel') {
                                if($spec1) $specs_html .= "<b>Material:</b> <span style='font-size:11px;'>$spec1</span><br>";
                                if($spec2) $specs_html .= "<b>Fit:</b> $spec2<br>";
                                if($spec3) $specs_html .= "<b>Gender:</b> $spec3<br>";
                                if($spec4) $specs_html .= "<b>Tech:</b> <span style='font-size:11px;'>$spec4</span><br>";
                                if($spec5) $specs_html .= "<b>Detail:</b> $spec5<br>";
                            } elseif ($cat == 'Bags') {
                                if($spec1) $specs_html .= "<b>Comparts:</b> $spec1<br>";
                                if($spec2) $specs_html .= "<b>Capacity:</b> $spec2<br>";
                                if($spec3) $specs_html .= "<b>Straps:</b> $spec3<br>";
                                if($spec4) $specs_html .= "<b>Outer:</b> <span style='font-size:11px;'>$spec4</span><br>";
                                if($spec5) $specs_html .= "<b>Inner:</b> <span style='font-size:11px;'>$spec5</span><br>";
                            } elseif ($cat == 'Accessories') {
                                if($spec1) $specs_html .= "<b>Type:</b> $spec1<br>";
                                if($spec2) $specs_html .= "<b>Size:</b> $spec2<br>";
                                if($spec4) $specs_html .= "<b>Material:</b> <span style='font-size:11px;'>$spec4</span><br>";
                                if($spec5) $specs_html .= "<b>App/Qty:</b> <span style='font-size:11px;'>$spec5</span><br>";
                            } else {
                                if($spec1) $specs_html .= "<b>Spec 1:</b> $spec1<br>";
                                if($spec2) $specs_html .= "<b>Spec 2:</b> $spec2<br>";
                                if($spec3) $specs_html .= "<b>Spec 3:</b> $spec3<br>";
                                if($spec4) $specs_html .= "<b>Spec 4:</b> $spec4<br>";
                                if($spec5) $specs_html .= "<b>Spec 5:</b> $spec5<br>";
                            }
                            // ================= 新增：获取库存明细逻辑 =================
                            $pid = $row['id'];
                            $var_query = $conn->query("SELECT spec_value, stock_quantity FROM product_variants WHERE product_id = $pid");
                            $variant_html = "";

                            if ($var_query && $var_query->num_rows > 0) {
                                $variant_html = "<div style='margin-top:8px; font-size:11px; background:#f1f5f9; padding:6px; border-radius:4px; border: 1px solid #e2e8f0;'>";
                                $has_variants = false;
    
                            while($v = $var_query->fetch_assoc()) {
                                $val = $v['spec_value'];
                                $qty = $v['stock_quantity'];
        
                            // 排除掉没有变体的 'Standard'，只显示有具体尺码的（如 XL, 40）
                            if ($val != 'Standard') {
                                $variant_html .= "<span style='display:inline-block; margin-right:8px; color:#334155; margin-bottom:4px;'><b>{$val}:</b> {$qty}件</span>";
                                $has_variants = true;
                                }
                            }
                            $variant_html .= "</div>";
    
                            // 如果只有 Standard，就清空这个气泡框
                            if (!$has_variants) {
                                $variant_html = "";
                                }
                            }
                            // =========================================================
                            if($color) $specs_html .= "<b style='color:#0f172a;'>Color:</b> <span style='font-size:11px;'>$color</span>";

                            echo "<tr>
                                    <td><img src='".$img_src."' style='width: 100px; height: 100px; object-fit: contain; border-radius: 4px; border:1px solid #eee;'></td>
                                    
                                    <td>
                                        <div style='font-weight:bold; font-size: 1.1em; color:#1e293b;'>".$row['name']."</div>
                                        <div style='font-size: 12px; color: #64748b; margin-top: 6px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;'>".$subtitle."</div>
                                    </td>
                                    
                                    <td>
                                        <span style='background:#f1f5f9; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:600;'>".$row['category']."</span><br>
                                        <span style='font-size:12px; color:#666; margin-top:8px; display:inline-block;'>".$row['series']."</span>
                                    </td>
                                    
                                    <td style='font-size:12px; color:#475569; line-height: 1.6;'>
                                        " . $specs_html . "
                                    </td>
                                    
                                    <td>
                                    <span style='font-weight:bold; font-size:1.1em; color:".($row['total_stock']>0 ? '#16a34a' : '#dc2626').";'>".$row['total_stock']."</span>
                                    " . $variant_html . "
                                    </td>
                                    <td style='font-weight:bold; font-size:1.1em; color:#b71c1c;'>RM ".number_format($row['price'], 2)."</td>
                                    
                                    <td>
                                        <a href='edit_product.php?id=".$row['id']."' class='btn' style='background:#c9a84c; color:#fff; text-decoration:none; padding:6px 12px; border-radius:4px; font-size: 13px; display:block; text-align:center; margin-bottom:8px;'>Edit Details</a>
                                        <a href='manage_product.php?delete=".$row['id']."' class='btn btn-delete' style='padding:6px 12px; font-size: 13px; display:block; text-align:center;' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; padding:30px;'>No products found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
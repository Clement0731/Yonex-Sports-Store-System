<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if(isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM `users` WHERE `id` = $del_id");
    $conn->query("DELETE FROM `addresses` WHERE `user_id` = $del_id"); 
    header("Location: manage_customer.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Directory & Access Control | YONEX Pro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root { 
            --premium-navy: #002d56; 
            --slate-dark: #0f172a; 
            --slate-muted: #64748b; 
            --border-fine: #e2e8f0; 
        }
        body { background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, sans-serif; color: var(--slate-dark); }
        .main-content { padding: 40px; width: 100%; }
        .header-flex { border-bottom: 1px solid var(--border-fine); padding-bottom: 20px; margin-bottom: 35px; }
        .header-title { font-size: 1.6rem; font-weight: 800; color: var(--premium-navy); letter-spacing: -0.02em; text-transform: uppercase; }
        
        .address-directory-box { background: #f8fafc; border: 1px solid var(--border-fine); padding: 12px 16px; font-size: 0.82rem; line-height: 1.6; color: #334155; text-align: left; }
        .address-card-line { border-bottom: 1px dashed var(--border-fine); padding-bottom: 6px; margin-bottom: 6px; }
        .address-card-line:last-child { border-bottom: none; padding-bottom: 0; margin-bottom: 0; }
        .address-label-tag { font-size: 0.65rem; font-weight: 800; background: #0f172a; color: white; padding: 2px 6px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-right: 5px; }
        
        .verification-tag { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
        .status-verified { color: #000; border-bottom: 1px solid #000; }
        .status-pending { color: var(--slate-muted); text-decoration: line-through; }

        .btn-remove-client { background: transparent; color: var(--slate-muted); border: 1px solid var(--border-fine); padding: 6px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; display: inline-block; transition: all 0.2s; }
        .btn-remove-client:hover { border-color: #000; color: #000; }
        
        .table-box th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--slate-muted); padding: 15px 20px; }
        .table-box td { padding: 18px 20px; vertical-align: top; border-bottom: 1px solid var(--border-fine); }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1 class="header-title">Client Registry</h1>
        </div>

        <div class="table-box" style="background: #ffffff; border: 1px solid var(--border-fine); border-radius: 0px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-fine);">
                        <th style="text-align: left; width: 12%;">Client Reference</th>
                        <th style="text-align: left; width: 18%;">Personal Profiles</th>
                        <th style="text-align: left; width: 22%;">Contact Credentials</th>
                        <th style="text-align: left; width: 38%;">Registered Shipping Directory</th>
                        <th style="text-align: right; width: 10%;">System Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `users` ORDER BY `id` DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $user_id = $row['id'];
                            
                            $addr_res = $conn->query("SELECT * FROM `addresses` WHERE `user_id` = '$user_id' ORDER BY `id` DESC");
                            $address_html = "";
                            
                            if ($addr_res && $addr_res->num_rows > 0) {
                                $address_html .= "<div class='address-directory-box'>";
                                while($addr = $addr_res->fetch_assoc()) {
                                    $label = !empty($addr['label']) ? htmlspecialchars($addr['label']) : 'Default';
                                    
                                    // 💡 核心修复：提前提取变量，并用 ?? 赋予安全默认值。
                                    // 如果数据库里叫 'city_state'、'state' 或 'city' 都能自动兼容，没有也不会报错
                                    $rec_name = htmlspecialchars($addr['receiver_name'] ?? '');
                                    $rec_phone = htmlspecialchars($addr['receiver_phone'] ?? '');
                                    $full_address = htmlspecialchars($addr['full_address'] ?? '');
                                    $postcode = htmlspecialchars($addr['postcode'] ?? '');
                                    $city_state = htmlspecialchars($addr['city_state'] ?? $addr['state'] ?? $addr['city'] ?? '');

                                    $address_html .= "<div class='address-card-line'>
                                                        <span class='address-label-tag'>{$label}</span> 
                                                        <b>{$rec_name}</b> ({$rec_phone})<br>
                                                        <span style='color:var(--slate-muted);'>{$full_address} , {$postcode} {$city_state}</span>
                                                      </div>";
                                }
                                $address_html .= "</div>";
                            } else {
                                $address_html .= "<div style='color:var(--slate-muted); font-size:0.8rem; font-style: italic;'>No shipping coordinates saved by client.</div>";
                            }

                            $gender = !empty($row['gender']) ? htmlspecialchars($row['gender']) : 'Unspecified';
                            $birthday = (!empty($row['birthday']) && $row['birthday'] != '0000-00-00') ? date('d M Y', strtotime($row['birthday'])) : 'Unspecified';
                            
                            // 💡 顺手防御：防止这里的 is_verified 字段也有未定义风险
                            $is_verified = (($row['is_verified'] ?? 0) == 1);
                            $status_text = $is_verified ? 'Verified Account' : 'Pending Verification';
                            $status_class = $is_verified ? 'status-verified' : 'status-pending';

                            echo "<tr>
                                    <td>
                                        <div style='font-weight: 700; color: var(--premium-navy);'># YNX-USR-{$user_id}</div>
                                        <div style='margin-top: 8px;'>
                                            <span class='verification-tag {$status_class}'>{$status_text}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style='font-weight: 700; color: #000;'>".htmlspecialchars($row['username'] ?? '')."</div>
                                        <div style='font-size: 0.75rem; color: var(--slate-muted); margin-top: 4px;'>Gender: {$gender}</div>
                                        <div style='font-size: 0.75rem; color: var(--slate-muted);'>DOB: {$birthday}</div>
                                    </td>
                                    <td>
                                        <div style='font-weight: 600; font-size: 0.85rem;'>".htmlspecialchars($row['email'] ?? '')."</div>
                                        <div style='font-size: 0.8rem; color: var(--premium-navy); font-weight: 700; margin-top: 2px;'>".htmlspecialchars($row['phone'] ?? '-')."</div>
                                    </td>
                                    <td>
                                        {$address_html}
                                    </td>
                                    <td style='text-align: right;'>
                                        <a href='manage_customer.php?delete={$user_id}' class='btn-remove-client' onclick=\"return confirm('Are you sure you want to completely expunge this client profile and their saved address books? This cannot be undone.');\">Revoke</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding:40px; color: var(--slate-muted); font-size: 0.85rem;'>No clients indexed in database directory.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
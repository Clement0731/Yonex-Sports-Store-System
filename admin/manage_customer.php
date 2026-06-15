<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if(isset($_GET['action']) && isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];
    $action = $_GET['action'];
    $current_filter = $_GET['filter'] ?? 'Active';
    $search = $_GET['search'] ?? '';
    
    if ($action === 'deactivate') {
        $conn->query("UPDATE `users` SET `status` = 'Deactivated' WHERE `id` = $target_id");
    } elseif ($action === 'reactivate') {
        $conn->query("UPDATE `users` SET `status` = 'Active' WHERE `id` = $target_id");
    }
    
    header("Location: manage_customer.php?filter=" . urlencode($current_filter) . "&search=" . urlencode($search));
    exit();
}

$filter = (isset($_GET['filter']) && $_GET['filter'] === 'Deactivated') ? 'Deactivated' : 'Active';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
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
        .header-flex { border-bottom: 1px solid var(--border-fine); padding-bottom: 20px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        .header-title { font-size: 1.6rem; font-weight: 800; color: var(--premium-navy); letter-spacing: -0.02em; text-transform: uppercase; margin: 0; }
        
        .filter-tabs { display: flex; gap: 10px; align-items: center; }
        .filter-tab { padding: 8px 16px; text-decoration: none; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; border-radius: 4px; transition: all 0.2s; }
        .tab-active { background: var(--premium-navy); color: white; border: 1px solid var(--premium-navy); }
        .tab-inactive { background: transparent; color: var(--slate-muted); border: 1px solid var(--border-fine); }
        .tab-inactive:hover { border-color: var(--slate-muted); color: var(--slate-dark); }

        .search-container { display: flex; gap: 10px; margin-bottom: 25px; background: #ffffff; padding: 15px; border: 1px solid var(--border-fine); }
        .search-input { padding: 10px 15px; border: 1px solid var(--border-fine); font-size: 0.9rem; width: 320px; outline: none; }
        .btn-search { background: var(--premium-navy); color: white; border: none; padding: 10px 24px; font-size: 0.85rem; font-weight: 700; cursor: pointer; text-transform: uppercase; }
        .btn-clear { background: #f1f5f9; color: var(--slate-dark); border: 1px solid var(--border-fine); padding: 10px 20px; font-size: 0.85rem; font-weight: 700; text-decoration: none; text-transform: uppercase; display: flex; align-items: center; }

        .address-directory-box { background: #f8fafc; border: 1px solid var(--border-fine); padding: 12px 16px; font-size: 0.82rem; line-height: 1.6; color: #334155; text-align: left; }
        .address-card-line { border-bottom: 1px dashed var(--border-fine); padding-bottom: 6px; margin-bottom: 6px; }
        .address-card-line:last-child { border-bottom: none; padding-bottom: 0; margin-bottom: 0; }
        .address-label-tag { font-size: 0.65rem; font-weight: 800; background: #0f172a; color: white; padding: 2px 6px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-right: 5px; }
        
        .verification-tag { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
        .status-verified { color: #000; border-bottom: 1px solid #000; }
        .status-pending { color: var(--slate-muted); text-decoration: line-through; }

        .btn-action { background: transparent; padding: 6px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; display: inline-block; transition: all 0.2s; border-radius: 3px; }
        .btn-deactivate { color: #ef4444; border: 1px solid #fca5a5; }
        .btn-deactivate:hover { background: #fef2f2; border-color: #ef4444; }
        .btn-reactivate { color: #10b981; border: 1px solid #6ee7b7; }
        .btn-reactivate:hover { background: #ecfdf5; border-color: #10b981; }
        
        .table-box th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--slate-muted); padding: 15px 20px; }
        .table-box td { padding: 18px 20px; vertical-align: top; border-bottom: 1px solid var(--border-fine); }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-flex">
            <h1 class="header-title">Client Registry</h1>
            <div class="filter-tabs">
                <a href="?filter=Active&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo $filter === 'Active' ? 'tab-active' : 'tab-inactive'; ?>">Active Clients</a>
                <a href="?filter=Deactivated&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo $filter === 'Deactivated' ? 'tab-active' : 'tab-inactive'; ?>">Deactivated</a>
            </div>
        </div>

        <form method="GET" action="" class="search-container">
            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, email, phone or ID...">
            <button type="submit" class="btn-search">Search</button>
            <?php if (!empty($search)): ?>
                <a href="manage_customer.php?filter=<?php echo urlencode($filter); ?>" class="btn-clear">Clear</a>
            <?php endif; ?>
        </form>

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
                    $search_query = "";
                    if (!empty($search)) {
                        $clean_search = $conn->real_escape_string($search);
                        $numeric_id = (int)str_ireplace('YNX-USR-', '', $clean_search);
                        $search_query = " AND (id = '$numeric_id' OR username LIKE '%$clean_search%' OR email LIKE '%$clean_search%' OR phone LIKE '%$clean_search%')";
                    }

                    $sql = "SELECT * FROM `users` WHERE `status` = '$filter' $search_query ORDER BY `id` DESC";
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
                                $address_html .= "<div style='color:var(--slate-muted); font-size:0.8rem; font-style: italic;'>No shipping coordinates saved.</div>";
                            }

                            $gender = !empty($row['gender']) ? htmlspecialchars($row['gender']) : 'Unspecified';
                            $birthday = (!empty($row['birthday']) && $row['birthday'] != '0000-00-00') ? date('d M Y', strtotime($row['birthday'])) : 'Unspecified';
                            
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
                                    <td style='text-align: right;'>";
                                    
                            if ($filter === 'Active') {
                                echo "<a href='manage_customer.php?action=deactivate&id={$user_id}&filter={$filter}&search=" . urlencode($search) . "' class='btn-action btn-deactivate' onclick=\"return confirm('Suspend this user account?');\">Deactivate</a>";
                            } else {
                                echo "<a href='manage_customer.php?action=reactivate&id={$user_id}&filter={$filter}&search=" . urlencode($search) . "' class='btn-action btn-reactivate' onclick=\"return confirm('Restore access for this user account?');\">Reactivate</a>";
                            }
                            
                            echo "  </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding:40px; color: var(--slate-muted); font-size: 0.85rem;'>No records matched your criteria.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
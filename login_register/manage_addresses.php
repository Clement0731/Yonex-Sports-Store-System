<?php
session_start();
// 保持你的连接方式
$conn = new mysqli("localhost", "root", "", "yonex_db");
if (!isset($_SESSION['user_id'])) { header("Location: login_page.php"); exit(); }
$user_id = $_SESSION['user_id'];

// --- 1. 处理删除逻辑 ---
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    header("Location: manage_addresses.php");
    exit();
}

// --- 2. 处理新增地址 (与 Checkout 弹窗逻辑完全一致) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_type']) && $_POST['action_type'] == 'add_new_addr') {
    $unit = trim($_POST['unit_no']);
    $building = trim($_POST['building_name']);
    $street = trim($_POST['street_name']);
    $full_address = "$unit, $building, $street";
    $city_state = trim($_POST['city']) . ", " . trim($_POST['state']);

    $stmt = $conn->prepare("INSERT INTO addresses (user_id, receiver_name, receiver_phone, full_address, postcode, city_state, label) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $_POST['name'], $_POST['phone'], $full_address, $_POST['postcode'], $city_state, $_POST['label']);
    $stmt->execute();
    header("Location: manage_addresses.php");
    exit();
}

// --- 3. 处理编辑地址 (与 Checkout 弹窗逻辑完全一致) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_type']) && $_POST['action_type'] == 'edit_addr') {
    $addr_id = intval($_POST['edit_addr_id']);
    $unit = trim($_POST['unit_no']);
    $building = trim($_POST['building_name']);
    $street = trim($_POST['street_name']);
    $full_address = "$unit, $building, $street";
    $city_state = trim($_POST['city']) . ", " . trim($_POST['state']);

    $stmt = $conn->prepare("UPDATE addresses SET receiver_name=?, receiver_phone=?, full_address=?, postcode=?, city_state=?, label=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssssssii", $_POST['name'], $_POST['phone'], $full_address, $_POST['postcode'], $city_state, $_POST['label'], $addr_id, $user_id);
    $stmt->execute();
    header("Location: manage_addresses.php");
    exit();
}

// 获取属于该用户的地址
$addresses = $conn->query("SELECT * FROM addresses WHERE user_id = '$user_id' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Addresses | YONEX</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --primary-blue: #003366; 
            --primary-gold: #FFD700; 
            --bg-light: #f4f7f9; 
        }
        body { 
            background-color: var(--bg-light); 
            font-family: 'Inter', sans-serif; 
            padding: 40px 20px; 
            color: #333; 
        }
        .page-container { max-width: 650px; margin: auto; }
        
        .page-title { font-family: 'Oswald', sans-serif; color: var(--primary-blue); letter-spacing: 1px; }

        .btn-light-custom { 
            background: #fff; border: 2px solid #eef2f6; color: var(--primary-blue); 
            font-weight: 700; transition: 0.3s; display: inline-flex; align-items: center;
            text-decoration: none; padding: 8px 20px; border-radius: 50rem; margin-bottom: 30px;
        }
        .btn-light-custom:hover {
            background-color: var(--primary-blue); color: white; border-color: var(--primary-blue);
            transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,51,102,0.15);
        }

        .addr-card {
            border: 2px solid #eef2f6; border-radius: 12px; padding: 20px 20px 15px 20px;
            position: relative; transition: all 0.2s ease; background: #fff;
            margin-bottom: 20px; display: flex; flex-direction: column; justify-content: space-between;
        }
        .addr-card:hover { border-color: #b3cce6; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,51,102,0.05); }
        
        .addr-name { font-weight: 700; font-size: 1.05rem; color: #1a1a1a; }
        .addr-phone { color: #6c757d; font-weight: 500; font-size: 0.9rem; }
        .addr-detail { font-size: 0.9rem; color: #555; line-height: 1.6; margin-top: 10px; }

        .addr-actions { border-top: 1px dashed #e2e8f0; padding-top: 12px; margin-top: 15px; display: flex; justify-content: flex-end; gap: 15px; }
        .action-link { font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.2s; cursor: pointer; }
        .action-link.edit { color: var(--primary-blue); }
        .action-link.edit:hover { color: #00509e; text-decoration: underline; }
        .action-link.delete { color: #dc3545; }
        .action-link.delete:hover { color: #a71d2a; text-decoration: underline; }

        .btn-add { 
            display: block; text-align: center; padding: 16px; background: var(--primary-blue); 
            color: white; border-radius: 12px; text-decoration: none; font-weight: 700; 
            font-size: 1.05rem; transition: 0.3s; margin-top: 30px; letter-spacing: 0.5px; border: none; width: 100%;
        }
        .btn-add:hover { background: #001f3f; color: var(--primary-gold); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,51,102,0.2); }

        /* Modal 样式调整 */
        .modal-body .form-control, .modal-body .form-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem; background-color: #f8fafc; transition: all 0.2s ease-in-out; }
        .modal-body .form-control:focus, .modal-body .form-select:focus { background-color: #ffffff; border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1); }
    </style>
</head>
<body>

<div class="page-container">
    <a href="user_profile.php" class="btn-light-custom">
        <i class="fas fa-arrow-left me-2"></i>Back to Profile
    </a>
    
    <h2 class="mb-4 fw-bold page-title"><i class="fas fa-map-marker-alt me-2 text-danger"></i>MY ADDRESSES</h2>
    
    <?php if ($addresses && $addresses->num_rows > 0): ?>
        <?php while($row = $addresses->fetch_assoc()): ?>
            <div class="addr-card">
                <div>
                    <div class="d-flex align-items-center mb-1">
                        <span class="addr-name"><?php echo htmlspecialchars($row['receiver_name']); ?></span>
                        <span class="addr-phone ms-3"><?php echo htmlspecialchars($row['receiver_phone']); ?></span>
                    </div>
                    <span class="badge rounded-pill px-2 py-1 mt-1" style="font-size: 0.7rem; background-color: #64748b;"><?php echo htmlspecialchars($row['label']); ?></span>
                </div>
                
                <div class="addr-detail">
                    <?php echo htmlspecialchars($row['full_address']); ?><br>
                    <?php echo htmlspecialchars($row['postcode'] . ', ' . $row['city_state']); ?>
                </div>

                <div class="addr-actions">
                    <a class="action-link edit" onclick="prepareEditModal(
                        <?php echo $row['id']; ?>, 
                        '<?php echo addslashes($row['receiver_name']); ?>', 
                        '<?php echo addslashes($row['receiver_phone']); ?>', 
                        '<?php echo addslashes($row['full_address']); ?>', 
                        '<?php echo addslashes($row['city_state'] ?? ''); ?>', 
                        '<?php echo addslashes($row['postcode']); ?>', 
                        '<?php echo addslashes($row['label'] ?? 'Home'); ?>'
                    )">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="manage_addresses.php?delete_id=<?php echo $row['id']; ?>" class="action-link delete" onclick="return confirm('Are you sure you want to remove this address?');">
                        <i class="fas fa-trash-alt me-1"></i> Remove
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="addr-card" style="text-align: center; border-style: dashed; padding: 50px 20px;">
            <i class="fas fa-map-marked-alt fa-3x mb-3" style="color: #cbd5e1;"></i>
            <h5 class="fw-bold text-dark">No addresses saved yet</h5>
            <p class="text-muted small mb-0">Add a new address to speed up your checkout process.</p>
        </div>
    <?php endif; ?>

    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="prepareAddModal()">
        <i class="fas fa-plus me-2"></i> ADD NEW ADDRESS
    </button>
</div>

<div class="modal fade" id="addressModal" tabindex="-1">
  <div class="modal-content modal-dialog modal-dialog-centered border-0 rounded-4 shadow-lg">
    <form method="POST" action="manage_addresses.php" id="addressForm">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold fs-4" id="modalTitle">New Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action_type" id="action_type" value="add_new_addr">
        <input type="hidden" name="edit_addr_id" id="edit_addr_id" value="">
        
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">Receiver Name</label>
                <input type="text" name="name" id="receiver_name" class="form-control bg-light" placeholder="e.g. Ali bin Abu" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">Phone Number</label>
                <input type="tel" name="phone" id="phone_input" class="form-control bg-light" placeholder="01X XXXXXXX" required pattern="01\d \d{7,8}" title="Format: 01X XXXXXXX">
            </div>

            <div class="col-12">
                <label class="form-label small fw-semibold text-muted">Detailed Address</label>
                <input type="text" name="unit_no" id="unit_no" class="form-control bg-light mb-2" placeholder="Unit / House No." required>
                <input type="text" name="building_name" id="building_name" class="form-control bg-light mb-2" placeholder="Building Name / Taman" required pattern=".*[a-zA-Z].*" title="Must contain at least one letter">
                <input type="text" name="street_name" id="street_name" class="form-control bg-light" placeholder="Street Name" required pattern=".*[a-zA-Z].*" title="Must contain at least one letter">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">State</label>
                <select name="state" id="state_select" class="form-select bg-light" required>
                    <option value="" disabled selected>Select State</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-muted">City</label>
                <select name="city" id="city_select" class="form-select bg-light" required disabled>
                    <option value="" disabled selected>Select City First</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label small fw-semibold text-muted">Postcode <span id="postcode_hint" class="text-danger" style="font-size:0.75rem;"></span></label>
                <input type="text" name="postcode" id="postcode_input" class="form-control bg-light" placeholder="e.g. 80000" required maxlength="5">
            </div>

            <div class="col-12 mt-2">
                <label class="form-label small fw-semibold text-muted d-block">Label as</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="label" id="btnradio1" value="Home" checked>
                    <label class="btn btn-outline-secondary" for="btnradio1"><i class="fas fa-home me-2"></i>Home</label>
                    <input type="radio" class="btn-check" name="label" id="btnradio2" value="Office">
                    <label class="btn btn-outline-secondary" for="btnradio2"><i class="fas fa-building me-2"></i>Office</label>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
          <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3" style="background: var(--primary-blue); border:none;">SAVE ADDRESS</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ----------------------------------------------------
    //  🎯 地址增改弹窗 JS 逻辑 + 马来西亚邮编引擎
    // ----------------------------------------------------
    const malaysiaData = {
        "Johor": { cities: { "Johor Bahru": "80000", "Batu Pahat": "83000", "Kluang": "86000", "Muar": "84000", "Kulai": "81000", "Kota Tinggi": "81900", "Segamat": "85000", "Pontian": "82000" }, prefix: /^(79|80|81|82|83|84|85|86)/, hint: "79000-86999" },
        "Kedah": { cities: { "Alor Setar": "05000", "Sungai Petani": "08000", "Kulim": "09000", "Langkawi": "07000", "Baling": "09100", "Jitra": "06000" }, prefix: /^(05|06|07|08|09)/, hint: "05000-09999" },
        "Kelantan": { cities: { "Kota Bharu": "15000", "Pasir Mas": "17000", "Tanah Merah": "17500", "Gua Musang": "18300", "Machang": "18500" }, prefix: /^(15|16|17|18)/, hint: "15000-18999" },
        "Kuala Lumpur": { cities: { "Kuala Lumpur": "50000", "Cheras": "56000", "Kepong": "52000", "Setapak": "53000", "Wangsa Maju": "53300" }, prefix: /^(50|51|52|53|54|55|56|57|58|59|60)/, hint: "50000-60000" },
        "Melaka": { cities: { "Melaka City": "75000", "Alor Gajah": "78000", "Jasin": "77000", "Ayer Keroh": "75450", "Batu Berendam": "75350" }, prefix: /^(75|76|77|78)/, hint: "75000-78999" },
        "Negeri Sembilan": { cities: { "Seremban": "70000", "Port Dickson": "71000", "Nilai": "71800", "Kuala Pilah": "72000", "Bahau": "72100" }, prefix: /^(70|71|72|73)/, hint: "70000-73999" },
        "Pahang": { cities: { "Kuantan": "25000", "Temerloh": "28000", "Bentong": "28700", "Cameron Highlands": "39000", "Raub": "27600" }, prefix: /^(25|26|27|28|39|49|69)/, hint: "25000-49999" },
        "Penang": { cities: { "George Town": "10000", "Butterworth": "12000", "Bayan Lepas": "11900", "Bukit Mertajam": "14000", "Nibong Tebal": "14300" }, prefix: /^(10|11|12|13|14)/, hint: "10000-14999" },
        "Perak": { cities: { "Ipoh": "30000", "Taiping": "34000", "Sitiawan": "32000", "Teluk Intan": "36000", "Kampar": "31900" }, prefix: /^(30|31|32|33|34|35|36)/, hint: "30000-36999" },
        "Perlis": { cities: { "Kangar": "01000", "Arau": "02600", "Padang Besar": "02100" }, prefix: /^(01|02)/, hint: "01000-02999" },
        "Selangor": { cities: { "Shah Alam": "40000", "Petaling Jaya": "46000", "Subang Jaya": "47500", "Klang": "41000", "Cyberjaya": "63000", "Puchong": "47100", "Sepang": "43900" }, prefix: /^(40|41|42|43|44|45|46|47|48|63|64|65|66|67|68)/, hint: "40000-68999" },
        "Terengganu": { cities: { "Kuala Terengganu": "20000", "Kemaman": "24000", "Dungun": "23000", "Besut": "22200" }, prefix: /^(20|21|22|23|24)/, hint: "20000-24999" },
        "Putrajaya": { cities: { "Putrajaya": "62000" }, prefix: /^(62)/, hint: "62000-62999" }
    };

    const stateSelect = document.getElementById('state_select');
    const citySelect = document.getElementById('city_select');
    const postcodeInput = document.getElementById('postcode_input');
    const postcodeHint = document.getElementById('postcode_hint');

    Object.keys(malaysiaData).forEach(state => { stateSelect.add(new Option(state, state)); });

    stateSelect.addEventListener('change', function() {
        citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';
        citySelect.disabled = false;
        const selectedState = this.value;
        const cityData = malaysiaData[selectedState].cities;
        postcodeHint.innerText = `(Valid range: ${malaysiaData[selectedState].hint})`;
        Object.keys(cityData).forEach(city => { citySelect.add(new Option(city, city)); });
        if(event && event.isTrusted) postcodeInput.value = ''; 
    });

    citySelect.addEventListener('change', function() {
        const state = stateSelect.value;
        const city = this.value;
        if(state && city) {
            postcodeInput.value = malaysiaData[state].cities[city]; 
            validatePostcode();
        }
    });

    postcodeInput.addEventListener('input', validatePostcode);

    function validatePostcode() {
        const state = stateSelect.value;
        let pc = postcodeInput.value.replace(/\D/g, ''); 
        postcodeInput.value = pc; 
        postcodeInput.setCustomValidity("");
        if (state && pc.length === 5) {
            const regex = malaysiaData[state].prefix;
            if (!regex.test(pc)) { postcodeInput.setCustomValidity(`Invalid postcode for ${state}. Please use range ${malaysiaData[state].hint}`); }
        } else if (pc.length < 5 && pc.length > 0) {
            postcodeInput.setCustomValidity(`Postcode must be 5 digits.`);
        }
    }

    const phoneInput = document.getElementById('phone_input');
    phoneInput.addEventListener('focus', function() { if (this.value === '') this.value = '01'; });
    phoneInput.addEventListener('input', function (e) {
        let val = this.value;
        if (!val.startsWith('01')) val = '01' + val.replace(/^0?1?/, '');
        val = val.replace(/\D/g, ''); 
        if (val.length > 11) val = val.substring(0, 11);
        if (val.length > 3) this.value = val.substring(0, 3) + ' ' + val.substring(3);
        else this.value = val;
    });

    function prepareAddModal() {
        document.getElementById('modalTitle').innerText = 'New Address';
        document.getElementById('action_type').value = 'add_new_addr';
        document.getElementById('edit_addr_id').value = '';
        document.getElementById('addressForm').reset();
        document.getElementById('city_select').innerHTML = '<option value="" disabled selected>Select City First</option>';
        document.getElementById('city_select').disabled = true;
        postcodeHint.innerText = '';
    }

    function prepareEditModal(id, name, phone, fullAddress, cityState, postcode, label) {
        event.stopPropagation(); 
        document.getElementById('modalTitle').innerText = 'Edit Address';
        document.getElementById('action_type').value = 'edit_addr';
        document.getElementById('edit_addr_id').value = id;
        document.getElementById('receiver_name').value = name;
        document.getElementById('phone_input').value = phone;
        document.getElementById('postcode_input').value = postcode;

        let addrParts = fullAddress.split(',').map(s => s.trim());
        document.getElementById('unit_no').value = addrParts[0] || '';
        document.getElementById('building_name').value = addrParts[1] || '';
        document.getElementById('street_name').value = addrParts.slice(2).join(', ') || '';

        if(cityState) {
            let csParts = cityState.split(',').map(s => s.trim());
            let city = csParts[0];
            let state = csParts[1];
            document.getElementById('state_select').value = state;
            document.getElementById('state_select').dispatchEvent(new Event('change'));
            document.getElementById('city_select').value = city;
        }

        if (label === 'Office') document.getElementById('btnradio2').checked = true;
        else document.getElementById('btnradio1').checked = true;

        var myModal = new bootstrap.Modal(document.getElementById('addressModal'));
        myModal.show();
    }
</script>

</body>
</html>
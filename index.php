<?php
session_start();

include 'db_connect.php';

$activeCategory = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'home';
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$categories = [
    'home'      => 'Home',
    'badminton' => 'Badminton',
    'service'   => 'Service',
    'about'     => 'About',
    'contact'   => 'Contact',
];

// Dynamic Subcategories
$badmintonSubcategories = [
    'badminton' => ['label' => 'All Product', 'image' => 'images/badminton-hero.jpg']
];

$cat_sql = "SELECT * FROM categories ORDER BY id ASC";
$cat_result = $conn->query($cat_sql);

if ($cat_result && $cat_result->num_rows > 0) {
    while($cat = $cat_result->fetch_assoc()) {
        $db_cat_name = $cat['category_name'];
        $db_cat_key = strtolower(str_replace(' ', '_', $db_cat_name)); 
        
        $badmintonSubcategories[$db_cat_key] = [
            'label' => $db_cat_name,
            'image' => $cat['image_url']
        ];
    }
}

$account_url = "login_register/user_profile.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YONEX — Official Badminton Products</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Cormorant+Garamond:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --white:     #ffffff;
            --offwhite:  #fafbfc;
            --lightgray: #eef2f5;
            --midgray:   #cbd5e1;
            --charcoal:  #1e293b;
            --dark:      #0f172a;
            --red:       #d0021b;
            --red-hover: #a80016;
            --gold:      #c9a84c;
            --text-main: #1e293b;
            --text-muted:#64748b;
            --border:    #e2e8f0;
            --shadow:    0 8px 30px rgba(0,0,0,0.05);
            --shadow-hover: 0 20px 35px -8px rgba(0,0,0,0.1);
            --nav-h:     70px;
        }

        html { scroll-behavior: smooth; }
        body { font-family: 'Montserrat', sans-serif; background: var(--offwhite); color: var(--text-main); min-height: 100vh; display: flex; flex-direction: column; }
        main { flex: 1; }

        header { position: sticky; top: 0; z-index: 1000; background: var(--white); height: var(--nav-h); display: flex; align-items: center; padding: 0 40px; box-shadow: 0 1px 0 var(--border); }
        .logo-area { display: flex; align-items: center; text-decoration: none; min-width: 200px; }
        .logo-image { height: 38px; width: auto; display: block; }
        
        nav { flex: 1; display: flex; justify-content: center; gap: 4px; height: 100%; align-items: center; }
        
        nav a, .dropdown-trigger {
            position: relative; padding: 8px 18px; color: var(--text-muted); text-decoration: none; 
            font-size: 0.85rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; 
            transition: color 0.25s; white-space: nowrap; cursor: pointer;
            display: flex; align-items: center; height: 100%;
            background: none; border: none; font-family: inherit;
        }
        
        nav a::after, .dropdown-trigger::after {
            content: ''; position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%);
            width: 0; height: 2px; background: var(--charcoal); transition: width 0.3s ease;
        }
        nav a:hover, .dropdown-trigger:hover { color: var(--charcoal); }
        nav a:hover::after, .dropdown-trigger:hover::after { width: 70%; }
        nav a.active, .dropdown-trigger.active { color: var(--charcoal); font-weight: 700; }
        nav a.active::after, .dropdown-trigger.active::after { width: 70%; background: var(--charcoal); }

        .dropdown { position: relative; height: 100%; display: flex; align-items: center; }
        
        .dropdown-content {
            display: none; 
            position: absolute; 
            top: var(--nav-h); 
            left: 50%; 
            transform: translateX(-50%); 
            background-color: var(--white); 
            min-width: 220px; 
            box-shadow: var(--shadow-hover); 
            z-index: 1100; 
            border: 1px solid var(--border); 
            border-radius: 0 0 8px 8px;
            padding: 8px 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
            opacity: 1;
        }
        
        .dropdown-content a {
            color: var(--text-muted); 
            padding: 12px 24px; 
            text-decoration: none; 
            display: block; 
            font-size: 0.8rem; 
            font-weight: 500; 
            text-transform: none;
            letter-spacing: 0.05em;
            transition: 0.2s; 
            text-align: left;
            white-space: nowrap;
        }
        
        .dropdown-content a:hover { 
            background-color: var(--lightgray); 
            color: var(--red); 
            padding-left: 30px; 
        }

        .header-actions { min-width: 200px; display: flex; justify-content: flex-end; align-items: center; gap: 16px; }
        .icon-btn { background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 6px; border-radius: 6px; transition: color 0.2s, background 0.2s; display: flex; align-items: center; text-decoration: none; }
        .icon-btn:hover { color: var(--charcoal); background: var(--lightgray); }

        /* =========================================
           🚀 SEARCH OVERLAY (下拉搜索栏高级样式)
           ========================================= */
        .search-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 120px; background: var(--white);
            z-index: 2000; transform: translateY(-100%); transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .search-overlay.active { transform: translateY(0); }
        .search-container { width: 100%; max-width: 900px; padding: 0 40px; display: flex; align-items: center; }
        .search-form { display: flex; align-items: center; gap: 20px; width: 100%; }
        .search-form input { 
            flex: 1; border: none; font-size: 1.8rem; font-family: 'Cormorant Garamond', serif; 
            color: var(--charcoal); outline: none; background: transparent; font-weight: 600;
        }
        .search-form input::placeholder { color: var(--midgray); font-weight: 500;}
        .close-search { background: none; border: none; font-size: 3rem; color: var(--midgray); cursor: pointer; transition: color 0.2s; line-height: 1; padding: 0 10px;}
        .close-search:hover { color: var(--red); }
        .search-icon-large { color: var(--charcoal); }

        /* =========================================
           HOME PAGE STYLES
           ========================================= */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.25)), url('images/badminton-hero.jpg');
            background-size: cover; background-position: center 40%; padding: 180px 60px 170px;
            display: flex; align-items: center; position: relative; overflow: hidden;
        }
        .hero::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 100%); pointer-events: none; }
        .hero-content { flex: 1; position: relative; z-index: 2; }
        .hero-eyebrow { font-size: 0.72rem; letter-spacing: 0.3em; text-transform: uppercase; color: var(--gold); margin-bottom: 16px; font-weight: 700; }
        .hero-title { font-family: 'Cormorant Garamond', serif; font-size: 4.2rem; font-weight: 600; line-height: 1.1; margin-bottom: 20px; color: var(--white); }
        .hero-title span { color: var(--red); }
        .hero-desc { font-size: 1rem; color: rgba(255,255,255,0.9); line-height: 1.7; max-width: 480px; margin-bottom: 36px; }
        
        .btn-primary { display: inline-flex; align-items: center; gap: 8px; background: var(--red); color: var(--white); text-decoration: none; padding: 13px 32px; font-size: 0.78rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; border-radius: 40px; transition: background 0.2s, transform 0.2s; border: 2px solid var(--red); }
        .btn-primary:hover { background: var(--red-hover); border-color: var(--red-hover); transform: translateY(-2px); }
        
        .btn-outline { display: inline-flex; align-items: center; gap: 8px; background: transparent; color: var(--charcoal); text-decoration: none; padding: 13px 32px; font-size: 0.78rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; border-radius: 40px; transition: 0.3s; border: 2px solid var(--charcoal); }
        .btn-outline:hover { background: var(--charcoal); color: var(--white); transform: translateY(-2px); }

        .promo-section {
            display: flex; align-items: center; justify-content: space-between;
            min-height: 70vh; padding: 80px 10%; background: var(--white);
            border-bottom: 1px solid var(--border);
        }
        .promo-section:nth-child(even) { flex-direction: row-reverse; background: var(--offwhite); }
        
        .promo-text { flex: 1; padding: 40px; max-width: 600px; }
        .promo-series { font-size: 0.8rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--red); font-weight: 700; margin-bottom: 15px; }
        .promo-name { font-family: 'Cormorant Garamond', serif; font-size: 3.5rem; font-weight: 600; color: var(--charcoal); margin-bottom: 20px; line-height: 1.1; }
        .promo-desc { font-size: 1.1rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 40px; }
        
        .promo-image { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .promo-image img { width: 100%; max-width: 500px; height: auto; object-fit: contain; filter: drop-shadow(0 20px 30px rgba(0,0,0,0.1)); transition: transform 0.5s ease; }
        .promo-section:hover .promo-image img { transform: scale(1.05) rotate(-2deg); }

        .page-header { padding: 200px 60px; text-align: center; background: var(--charcoal); color: var(--white); }
        .page-header h1 { font-family: 'Cormorant Garamond', serif; font-size: 4.2rem; font-weight: 600; }

        @media (max-width: 900px) {
            header { padding: 0 20px; }
            .hero { padding: 120px 30px 100px; }
            .promo-section, .promo-section:nth-child(even) { flex-direction: column !important; text-align: center; padding: 60px 20px; }
            .promo-text { padding: 20px 0; }
            .promo-image { margin-top: 30px; }
            .promo-name { font-size: 2.8rem; }
        }

        /* =========================================
           PRODUCT ANIMATION (Fade & Slide Up)
           ========================================= */
        @keyframes fadeSlideUp {
            0% { opacity: 0; transform: translateY(50px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        .product-card-anim {
            opacity: 0;
            animation: fadeSlideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            
            text-decoration: none; 
            color: inherit; 
            display: block; 
            background: var(--white); 
            border: 1px solid var(--border); 
            border-radius: 8px; 
            padding: 20px; 
            text-align: center; 
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card-anim:hover {
            transform: translateY(-8px) !important;
            box-shadow: var(--shadow-hover);
        }

        footer { background: var(--white); color: var(--text-muted); text-align: center; padding: 32px 40px; font-size: 0.75rem; letter-spacing: 0.06em; border-top: 1px solid var(--border); }
        footer strong { color: var(--charcoal); }

    </style>
</head>
<body>

<div id="searchOverlay" class="search-overlay">
    <div class="search-container">
        <form action="index.php" method="GET" class="search-form">
            <input type="hidden" name="category" value="search">
            <svg class="search-icon-large" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="q" id="searchInput" placeholder="Search for rackets, footwear, series..." required autocomplete="off">
            <button type="button" class="close-search" onclick="closeSearch()">&times;</button>
        </form>
    </div>
</div>

<header>
    <a href="?category=home" class="logo-area">
        <img src="yonex-logo.png" alt="YONEX" class="logo-image">
    </a>
    <nav>
        <a href="?category=home" class="<?= $activeCategory === 'home' && empty($productId) ? 'active' : '' ?>">
            Home
        </a>
        
        <div class="dropdown">
            <span class="dropdown-trigger <?= isset($badmintonSubcategories[$activeCategory]) ? 'active' : '' ?>">
                Badminton
            </span>
            <div class="dropdown-content">
                <?php foreach ($badmintonSubcategories as $subKey => $subData): ?>
                    <a href="?category=<?= $subKey ?>">
                        <?= htmlspecialchars($subData['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <a href="?category=service" class="<?= $activeCategory === 'service' ? 'active' : '' ?>">
            Service
        </a>
        
        <a href="?category=about" class="<?= $activeCategory === 'about' ? 'active' : '' ?>">
            About
        </a>
    </nav>
    <div class="header-actions">
        <button class="icon-btn" title="Search" onclick="openSearch()">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
        </button>
        <a href="payment/shopping_cart.php" class="icon-btn" title="Cart"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></a>
        
        <a href="<?= $account_url ?>" class="icon-btn" title="Account">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </a>
    </div>
</header>

<main>
    <?php 
        // 1. 如果有产品ID，显示商品详情页
        if ($productId > 0) {
            if (file_exists('product_detail.php')) {
                include 'product_detail.php';
            } elseif (file_exists('data/product_detail.php')) {
                include 'data/product_detail.php';
            } else {
                echo '<div class="page-header"><h1>Product Not Found</h1></div>';
            }
        } 
        // 🌟 2. 新增：如果是搜索页面
        elseif ($activeCategory === 'search') {
            $query_str = isset($_GET['q']) ? trim($_GET['q']) : '';
            
            echo '<div style="padding: 60px 40px; max-width: 1200px; margin: 0 auto; min-height: 60vh;">';
            echo '<h1 style="font-family: \'Cormorant Garamond\', serif; font-size: 3rem; margin-bottom: 10px; color: var(--charcoal); text-transform: uppercase;">Search Results</h1>';
            
            if (empty($query_str)) {
                echo '<p style="color: var(--text-muted); font-size: 1.2rem;">Please enter a search keyword to begin.</p>';
            } else {
                echo '<p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 40px; border-bottom: 2px solid var(--border); padding-bottom: 20px;">Showing results for: <strong style="color: var(--red); font-size: 1.3rem;">"' . htmlspecialchars($query_str) . '"</strong></p>';
                
                // 智能数据库检索：同时匹配 名字 / 系列 / 分类 / 描述
                $escaped_q = $conn->real_escape_string($query_str);
                $search_sql = "SELECT * FROM products WHERE name LIKE '%$escaped_q%' OR series LIKE '%$escaped_q%' OR category LIKE '%$escaped_q%' OR subtitle LIKE '%$escaped_q%' ORDER BY id DESC";
                $search_res = $conn->query($search_sql);
                
                if ($search_res && $search_res->num_rows > 0) {
                    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">';
                    $delay = 0;
                    while($p = $search_res->fetch_assoc()) {
                        $img_name = basename($p['image_url']);
                        if (empty($img_name)) $img_name = 'placeholder.png'; 
                        
                        echo '<a href="?id=' . $p['id'] . '" class="product-card-anim" style="animation-delay: ' . $delay . 's;">';
                        echo '<img src="images/' . htmlspecialchars($img_name) . '" style="width: 100%; height: 200px; object-fit: contain; margin-bottom: 15px;" alt="Product">';
                        echo '<div style="font-size: 0.75rem; color: var(--red); font-weight: 700; letter-spacing: 0.1em; margin-bottom: 5px; text-transform: uppercase;">' . htmlspecialchars($p['series']) . '</div>';
                        echo '<h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--charcoal);">' . htmlspecialchars($p['name']) . '</h3>';
                        echo '<div style="font-weight: 600; color: var(--charcoal);">RM ' . number_format($p['price'], 2) . '</div>';
                        echo '</a>';
                        $delay += 0.08;
                    }
                    echo '</div>';
                } else {
                    // 如果找不到商品，显示友好的空状态
                    echo '<div style="text-align:center; padding: 60px 20px; background: var(--white); border: 1px dashed var(--border); border-radius: 8px;">';
                    echo '<svg width="64" height="64" fill="none" stroke="var(--midgray)" stroke-width="1" viewBox="0 0 24 24" style="margin-bottom:20px;"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>';
                    echo '<h3 style="font-size: 1.5rem; margin-bottom: 15px; color: var(--charcoal);">No products found</h3>';
                    echo '<p style="color: var(--text-muted); margin-bottom: 30px;">We couldn\'t find anything matching "'.htmlspecialchars($query_str).'". Try adjusting your keywords.</p>';
                    echo '<a href="?category=badminton" class="btn-primary">Browse All Products</a>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        // 3. 如果是指定的羽毛球分类页面 (Bags, Apparel 等)
        elseif (isset($badmintonSubcategories[$activeCategory])) {
            
            $catLabel = $badmintonSubcategories[$activeCategory]['label'];
            $catImage = isset($badmintonSubcategories[$activeCategory]['image']) ? $badmintonSubcategories[$activeCategory]['image'] : '';
            
            $cleanImage = str_replace('../', '', $catImage);
            if (empty($cleanImage) || !file_exists($cleanImage)) {
                $cleanImage = 'images/badminton-hero.jpg'; 
            }

            echo '<div style="position:relative; height: 35vh; min-height: 250px; display:flex; align-items:center; justify-content:center; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.6)), url(\'' . htmlspecialchars($cleanImage) . '\') center/cover;">';
            echo '<h1 style="color:white; font-family: \'Cormorant Garamond\', serif; font-size: 3.5rem; text-transform: uppercase; letter-spacing: 0.1em; margin:0; text-shadow: 2px 2px 10px rgba(0,0,0,0.5);">' . htmlspecialchars($catLabel) . '</h1>';
            echo '</div>';
            
            echo '<div style="padding: 60px 40px; max-width: 1200px; margin: 0 auto; min-height: 50vh;">';

            if ($activeCategory === 'badminton') {
                $prod_sql = "SELECT * FROM products ORDER BY id DESC"; 
            } else {
                $escapedLabel = $conn->real_escape_string($catLabel);
                $prod_sql = "SELECT * FROM products WHERE category = '$escapedLabel' ORDER BY id DESC";
            }
            
            $prod_res = $conn->query($prod_sql);

            if ($prod_res && $prod_res->num_rows > 0) {
                echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">';
                
                $delay = 0; 
                
                while($p = $prod_res->fetch_assoc()) {
                    $img_name = basename($p['image_url']);
                    if (empty($img_name)) $img_name = 'placeholder.png'; 
                    
                    echo '<a href="?id=' . $p['id'] . '" class="product-card-anim" style="animation-delay: ' . $delay . 's;">';
                    echo '<img src="images/' . htmlspecialchars($img_name) . '" style="width: 100%; height: 200px; object-fit: contain; margin-bottom: 15px;" alt="Product">';
                    echo '<div style="font-size: 0.75rem; color: var(--red); font-weight: 700; letter-spacing: 0.1em; margin-bottom: 5px; text-transform: uppercase;">' . htmlspecialchars($p['series']) . '</div>';
                    echo '<h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--charcoal);">' . htmlspecialchars($p['name']) . '</h3>';
                    echo '<div style="font-weight: 600; color: var(--charcoal);">RM ' . number_format($p['price'], 2) . '</div>';
                    echo '</a>';
                    
                    $delay += 0.08; 
                }
                echo '</div>';
            } else {
                echo '<p style="color: var(--text-muted); font-size: 1.1rem; text-align:center; padding: 40px;">No products currently available in this category.</p>';
            }
            echo '</div>';
        } 
        // 4. 首页
        elseif ($activeCategory === 'home') {
            if (file_exists('home.php')) {
                include 'home.php';
            } else {
                echo "<p style='padding:50px; text-align:center;'>Error: home.php not found.</p>";
            }
        } 
        // 5. 其他页面
        else {
            $otherFile = $activeCategory . '.php'; 
            if (file_exists($otherFile)) {
                include $otherFile;
            } else {
                echo '<div class="page-header">';
                echo '<h1>' . htmlspecialchars($categories[$activeCategory] ?? 'Page') . '</h1>';
                echo '<p style="margin-top:20px; font-size:1.2rem; color:var(--midgray);">Page content coming soon...</p>';
                echo '</div>';
            }
        }
    ?>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> <strong>YONEX</strong>. All rights reserved. &nbsp;|&nbsp; Badminton FYP Project &nbsp;|&nbsp;Multimedia University</p>
</footer>

<script>
    function openSearch() {
        document.getElementById('searchOverlay').classList.add('active');
        setTimeout(() => document.getElementById('searchInput').focus(), 100);
    }

    function closeSearch() {
        document.getElementById('searchOverlay').classList.remove('active');
    }

    // 按键盘的 Esc 键也能快速关闭搜索框
    document.addEventListener('keydown', function(event){
        if(event.key === "Escape"){
            closeSearch();
        }
    });
</script>

</body>
</html>
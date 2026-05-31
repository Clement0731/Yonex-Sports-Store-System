<?php
include 'db_connect.php';

// 当前活跃类别，默认为 'home'
$activeCategory = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'home';

// ★ 新增：检测网址里有没有产品 ID (比如 ?id=1)
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 主导航类别
$categories = [
    'home'      => 'Home',
    'badminton' => 'Badminton',
    'service'   => 'Service',
    'about'     => 'About',
];

// Badminton 子类别及其对应的 PHP 文件
$badmintonSubcategories = [
    'badminton'    => ['label' => 'All Product', 'file' => 'data/all_product.php'],
    'rackets'      => ['label' => 'Racket', 'file' => 'data/rackets.php'],
    'footwear'     => ['label' => 'Footwear', 'file' => 'data/footwear.php'],
    'shuttlecocks' => ['label' => 'Shuttlecocks', 'file' => 'data/shuttlecocks.php'],
    'bags'         => ['label' => 'Bags', 'file' => 'data/bags.php'],
    'apparel'      => ['label' => 'Apparel', 'file' => 'data/apparel.php'],
    'accessories'  => ['label' => 'Accessories', 'file' => 'data/accessories.php'],
    'package'      => ['label' => 'Package', 'file' => 'data/package.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YONEX — Official Badminton Products</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Cormorant+Garamond:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* 全局变量和基础设置保持不变 */
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

        /* --- Header & 导航栏 --- */
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
        
        .dropdown-content a::after { display: none; }

        .header-actions { min-width: 200px; display: flex; justify-content: flex-end; align-items: center; gap: 16px; }
        .icon-btn { background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 6px; border-radius: 6px; transition: color 0.2s, background 0.2s; display: flex; align-items: center; }
        .icon-btn:hover { color: var(--charcoal); background: var(--lightgray); }

        /* --- 首页特定样式：大型产品宣传面 --- */
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

        footer { background: var(--white); color: var(--text-muted); text-align: center; padding: 32px 40px; font-size: 0.75rem; letter-spacing: 0.06em; border-top: 1px solid var(--border); }
        footer strong { color: var(--charcoal); }

        @media (max-width: 900px) {
            header { padding: 0 20px; }
            .hero { padding: 120px 30px 100px; }
            .promo-section, .promo-section:nth-child(even) { flex-direction: column !important; text-align: center; padding: 60px 20px; }
            .promo-text { padding: 20px 0; }
            .promo-image { margin-top: 30px; }
            .promo-name { font-size: 2.8rem; }
        }
    </style>
</head>
<body>

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
                        <?= $subData['label'] ?>
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
        <button class="icon-btn" title="Search"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg></button>
        <button class="icon-btn" title="Cart"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></button>
        <button class="icon-btn" title="Account"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></button>
    </div>
</header>

<main>
    <?php 
        // ★ 1. 如果网址里有 ?id=X，就直接在 main 里面加载万能详情页！
        if ($productId > 0) {
            if (file_exists('product_detail.php')) {
                include 'product_detail.php';
            } elseif (file_exists('data/product_detail.php')) {
                include 'data/product_detail.php';
            } else {
                echo '<div class="page-header"><h1>Product Not Found</h1></div>';
            }
        } 
        // 2. Badminton 子类别页面 (Rackets, Shoes 等)
        elseif (isset($badmintonSubcategories[$activeCategory])) {
            $fileToLoad = $badmintonSubcategories[$activeCategory]['file'];
            // 兼容文件可能不在 data 文件夹里的情况
            $fallbackFile = str_replace('data/', '', $fileToLoad);
            
            if (file_exists($fileToLoad)) {
                include $fileToLoad;
            } elseif (file_exists($fallbackFile)) {
                include $fallbackFile;
            } else {
                echo '<div class="page-header">';
                echo '<h1>' . htmlspecialchars($badmintonSubcategories[$activeCategory]['label']) . '</h1>';
                echo '<p style="margin-top:20px; font-size:1.2rem; color:var(--midgray);">Coming soon...</p>';
                echo '</div>';
            }
        } 
        // 3. 首页
        elseif ($activeCategory === 'home') {
            if (file_exists('home.php')) {
                include 'home.php';
            } else {
                echo "<p style='padding:50px; text-align:center;'>Error: home.php not found.</p>";
            }
        } 
        // 4. 其他页面
        else {
            $otherFile = $activeCategory . '.php'; // 比如 service 就会变成 service.php
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

</body>
</html>
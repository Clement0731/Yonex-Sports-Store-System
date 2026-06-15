<?php
session_start();

include 'db_connect.php';

// --- 账号状态实时检测开始 ---
if (isset($_SESSION['user_id'])) {
    $current_user_id = (int)$_SESSION['user_id'];
    
    $status_check_sql = "SELECT status FROM users WHERE id = $current_user_id";
    $status_result = $conn->query($status_check_sql);
    
    if ($status_result && $status_result->num_rows > 0) {
        $user_row = $status_result->fetch_assoc();
        
        if ($user_row['status'] === 'Deactivated') {
            session_unset();
            session_destroy();
            echo "<script>
                alert('Your account has been deactivated by the administrator. Please contact support for assistance.');
                window.location.href = 'login_register/login_page.php';
            </script>";
            exit();
        }
    } else {
        session_unset();
        session_destroy();
        header("Location: login_register/login_page.php");
        exit();
    }
}
// --- 账号状态实时检测结束 ---

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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Montserrat:wght@300;400;500;600;700;800&family=Cormorant+Garamond:wght@400;500;600&display=swap" rel="stylesheet">
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

        /* ========== 全局定制滚动条 ========== */
        ::-webkit-scrollbar {
            width: 8px;
            background: var(--lightgray);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--charcoal);
            border-radius: 8px;
            border: 2px solid var(--lightgray);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--dark);
        }

        html { scroll-behavior: smooth; }
        body { font-family: 'Montserrat', sans-serif; background: var(--offwhite); color: var(--text-main); min-height: 100vh; display: flex; flex-direction: column; }
        main { flex: 1; }

        header { position: sticky; top: 0; z-index: 1000; background: var(--white); height: var(--nav-h); display: flex; align-items: center; padding: 0 40px; box-shadow: 0 1px 0 var(--border); }
        .logo-area { display: flex; align-items: center; text-decoration: none; min-width: 200px; }
        .logo-image { height: 38px; width: auto; display: block; }
        
        nav { flex: 1; display: flex; justify-content: center; gap: 4px; height: 100%; align-items: center; }
        
        nav a, .dropdown-trigger {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            position: relative; padding: 8px 18px; color: var(--text-muted); text-decoration: none; 
            font-size: 0.85rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; 
            transition: color 0.25s; white-space: nowrap; cursor: pointer;
            display: flex; align-items: center; height: 100%;
            background: none; border: none;
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
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
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
            position: relative;
        }
        
        .dropdown-content a::after {
            display: none;
        }
        
        .dropdown-content a:hover { 
            background-color: var(--lightgray); 
            color: var(--red); 
            padding-left: 30px; 
        }

        .header-actions { min-width: 200px; display: flex; justify-content: flex-end; align-items: center; gap: 16px; }
        .icon-btn { background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 6px; border-radius: 6px; transition: color 0.2s, background 0.2s; display: flex; align-items: center; text-decoration: none; }
        .icon-btn:hover { color: var(--charcoal); background: var(--lightgray); }

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
        
        .brand-features {
            background: var(--white);
            padding: 80px 10%;
            border-bottom: 1px solid var(--border);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            text-align: center;
            padding: 30px 20px;
            transition: all 0.3s ease;
            border-radius: 16px;
            background: var(--offwhite);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .feature-card h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 12px;
        }
        
        .feature-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
        }
        
        .news-section {
            background: var(--offwhite);
            padding: 80px 10%;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-header h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 15px;
        }
        
        .section-header .divider {
            width: 60px;
            height: 3px;
            background: var(--red);
            margin: 0 auto;
        }
        
        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .news-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .news-image {
            height: 200px;
            overflow: hidden;
        }
        
        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .news-card:hover .news-image img {
            transform: scale(1.05);
        }
        
        .news-content {
            padding: 25px;
        }
        
        .news-date {
            font-size: 0.7rem;
            color: var(--red);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
        }
        
        .news-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 12px;
            line-height: 1.4;
        }
        
        .news-excerpt {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
        }
        
        .partners-section {
            background: var(--white);
            padding: 60px 10%;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        
        .partners-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 50px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .partner-item {
            text-align: center;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        
        .partner-item:hover {
            opacity: 1;
        }
        
        .partner-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.05em;
        }
        
        .social-section {
            background: var(--charcoal);
            padding: 60px 10%;
            text-align: center;
        }
        
        .social-section h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            color: var(--white);
            margin-bottom: 15px;
        }
        
        .social-section p {
            color: rgba(255,255,255,0.7);
            margin-bottom: 30px;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 25px;
        }
        
        .social-link {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            color: var(--white);
            text-decoration: none;
        }
        
        .social-link:hover {
            background: var(--red);
            transform: translateY(-3px);
        }
        
        .footer-enhanced {
            background: var(--charcoal);
            color: var(--white);
            padding: 60px 10% 30px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 50px;
            max-width: 1200px;
            margin: 0 auto;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 40px;
        }
        
        .footer-col h4 {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            margin-bottom: 20px;
            color: var(--white);
            position: relative;
            display: inline-block;
        }
        
        .footer-col h4::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--red);
        }
        
        .footer-col p {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        
        .footer-contact-info {
            list-style: none;
            margin-top: 15px;
        }
        
        .footer-contact-info li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
        }
        
        .footer-contact-info svg {
            flex-shrink: 0;
        }
        
        .footer-hours {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .footer-hours p {
            margin-bottom: 8px;
            font-size: 0.8rem;
        }
        
        .store-list {
            list-style: none;
        }
        
        .store-list li {
            margin-bottom: 15px;
        }
        
        .store-list li strong {
            color: var(--white);
            display: block;
            margin-bottom: 4px;
        }
        
        .store-list li span {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.5);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
        }
        
        @media (max-width: 900px) {
            header { padding: 0 20px; }
            .hero { padding: 120px 30px 100px; }
            .promo-section, .promo-section:nth-child(even) { flex-direction: column !important; text-align: center; padding: 60px 20px; }
            .promo-text { padding: 20px 0; }
            .promo-image { margin-top: 30px; }
            .promo-name { font-size: 2.8rem; }
            
            .features-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
            .news-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr; text-align: center; gap: 40px; }
            .footer-col h4::after { left: 50%; transform: translateX(-50%); }
            .footer-contact-info li { justify-content: center; }
            .partners-grid { gap: 30px; }
        }
        
        @media (max-width: 600px) {
            .features-grid { grid-template-columns: 1fr; }
            .news-grid { grid-template-columns: 1fr; }
            .social-links { gap: 15px; }
        }

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
        if ($productId > 0) {
            if (file_exists('product_detail.php')) {
                include 'product_detail.php';
            } elseif (file_exists('data/product_detail.php')) {
                include 'data/product_detail.php';
            } else {
                echo '<div class="page-header"><h1>Product Not Found</h1></div>';
            }
        } 
        elseif ($activeCategory === 'search') {
            $query_str = isset($_GET['q']) ? trim($_GET['q']) : '';
            
            echo '<div style="padding: 60px 40px; max-width: 1200px; margin: 0 auto; min-height: 60vh;">';
            echo '<h1 style="font-family: \'Cormorant Garamond\', serif; font-size: 3rem; margin-bottom: 10px; color: var(--charcoal); text-transform: uppercase;">Search Results</h1>';
            
            if (empty($query_str)) {
                echo '<p style="color: var(--text-muted); font-size: 1.2rem;">Please enter a search keyword to begin.</p>';
            } else {
                echo '<p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 40px; border-bottom: 2px solid var(--border); padding-bottom: 20px;">Showing results for: <strong style="color: var(--red); font-size: 1.3rem;">"' . htmlspecialchars($query_str) . '"</strong></p>';
                
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
        elseif ($activeCategory === 'home') {
            if (file_exists('home.php')) {
                include 'home.php';
            } else {
                echo "<p style='padding:50px; text-align:center;'>Error: home.php not found.</p>";
            }
        } 
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

<div class="partners-section">
    <div class="partners-grid">
        <div class="partner-item">
            <div class="partner-name">🏸 BWF World Tour</div>
        </div>
        <div class="partner-item">
            <div class="partner-name">🏆 All England Open</div>
        </div>
        <div class="partner-item">
            <div class="partner-name">🌏 Thomas & Uber Cup</div>
        </div>
        <div class="partner-item">
            <div class="partner-name">⭐ World Championships</div>
        </div>
        <div class="partner-item">
            <div class="partner-name">🏅 Sudirman Cup</div>
        </div>
    </div>
</div>

<div class="social-section">
    <h3>Connect With YONEX</h3>
    <p>Follow us for the latest updates, product launches and tournament news</p>
    <div class="social-links">
        <a href="https://www.bing.com/ck/a?!&&p=1b78ae21ae39206a25e261822e2f2991ec0316d1eede06aa20ac4c740e9f7f1eJmltdHM9MTc4MTM5NTIwMA&ptn=3&ver=2&hsh=4&fclid=086117f6-b82c-6386-2e95-032fb9bb6265&psq=yonex+facebook&u=a1aHR0cHM6Ly93d3cuZmFjZWJvb2suY29tL3lvbmV4YmFkbWludG9uLw" class="social-link">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879v-6.99h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.99C18.343 21.128 22 16.991 22 12z"/></svg>
        </a>
        <a href="https://www.bing.com/ck/a?!&&p=30ac00a68fcc78525f1a01a73686ba665cd835c6a93d8b3a61e89d2c9627c97aJmltdHM9MTc4MTM5NTIwMA&ptn=3&ver=2&hsh=4&fclid=086117f6-b82c-6386-2e95-032fb9bb6265&psq=yonex+x&u=a1aHR0cHM6Ly94LmNvbS95b25leF9jb20" class="social-link">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
        </a>
        <a href="https://www.bing.com/ck/a?!&&p=9adb6bb7c971b7adc6b5bdb3233ea95df846018f438ddaee1811b94547041cbcJmltdHM9MTc4MTM5NTIwMA&ptn=3&ver=2&hsh=4&fclid=086117f6-b82c-6386-2e95-032fb9bb6265&psq=yonex+instagram&u=a1aHR0cHM6Ly93d3cuaW5zdGFncmFtLmNvbS95b25leF9jb20v" class="social-link">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
        </a>
        <a href="https://www.bing.com/ck/a?!&&p=3dcd154fb5d0766b18fde4f74c7fe56c24d5f629d5c72bec205de0571d14c23fJmltdHM9MTc4MTM5NTIwMA&ptn=3&ver=2&hsh=4&fclid=086117f6-b82c-6386-2e95-032fb9bb6265&psq=yonex+youtube&u=a1aHR0cHM6Ly93d3cueW91dHViZS5jb20veW9uZXhjb20" class="social-link">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.376.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.376-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
        </a>
    </div>
</div>

<footer class="footer-enhanced">
    <div class="footer-grid">
        <div class="footer-col">
            <h4>About YONEX</h4>
            <p>Founded in 1946 in Niigata, Japan, YONEX has grown into the world's leading badminton brand, trusted by champions across generations.</p>
            <p>Our philosophy: "Innovation through craftsmanship" — delivering excellence in every product we create.</p>
        </div>
        
        <div class="footer-col">
            <h4>Contact Us</h4>
            <ul class="footer-contact-info">
                <li>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>+60 3-1234 5678</span>
                </li>
                <li>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>support@yonex.com.my</span>
                </li>
                <li>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Level 20, Menara Exchange, 59200 KL</span>
                </li>
            </ul>
            <div class="footer-hours">
                <p>🕒 Mon - Fri: 9:00 - 18:00</p>
                <p>🕒 Sat: 10:00 - 15:00</p>
            </div>
        </div>
        
        <div class="footer-col">
            <h4>Find a Store</h4>
            <ul class="store-list">
                <li>
                    <strong>YONEX KL Flagship</strong>
                    <span>Bukit Bintang, Kuala Lumpur</span>
                </li>
                <li>
                    <strong>YONEX Penang</strong>
                    <span>Gurney Plaza, George Town</span>
                </li>
                <li>
                    <strong>YONEX Johor Bahru</strong>
                    <span>Mid Valley Southkey</span>
                </li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <strong>YONEX</strong>. All rights reserved. | Badminton FYP Project | Multimedia University</p>
    </div>
</footer>

<script>
    function openSearch() {
        document.getElementById('searchOverlay').classList.add('active');
        setTimeout(() => document.getElementById('searchInput').focus(), 100);
    }

    function closeSearch() {
        document.getElementById('searchOverlay').classList.remove('active');
    }

    document.addEventListener('keydown', function(event){
        if(event.key === "Escape"){
            closeSearch();
        }
    });
</script>

</body>
</html>
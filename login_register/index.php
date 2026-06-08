<?php
// 当前活跃类别
$activeCategory = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'home';

$categories = [
    'home'         => 'Home',
    'rackets'      => 'Rackets',
    'footwear'     => 'Footwear',
    'shuttlecocks' => 'Shuttlecocks',
    'bags'         => 'Bags',
    'apparel'      => 'Apparel',
    'accessories'  => 'Accessories',
    'strings'      => 'Strings',
];

// 核心逻辑：根据当前类别动态加载对应的 PHP 文件
$dataPath = __DIR__ . "/data/{$activeCategory}.php";
if (file_exists($dataPath)) {
    $currentProducts = require $dataPath;
} else {
    // 默认加载 home
    $currentProducts = require __DIR__ . "../../home.php";
}
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
        /* 这里的 CSS 保持你之前代码的原样，不要修改 */
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

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--offwhite);
            color: var(--text-main);
            min-height: 100vh;
        }

        header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--white);
            height: var(--nav-h);
            display: flex;
            align-items: center;
            padding: 0 40px;
            box-shadow: 0 1px 0 var(--border);
        }

        .logo-area {
            display: flex;
            align-items: center;
            text-decoration: none;
            min-width: 200px;
        }

        .logo-image { height: 38px; width: auto; display: block; }

        nav { flex: 1; display: flex; justify-content: center; gap: 4px; }

        nav a {
            position: relative;
            padding: 8px 18px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            transition: color 0.25s;
            white-space: nowrap;
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--red);
            transition: width 0.3s ease;
        }

        nav a:hover { color: var(--charcoal); }
        nav a:hover::after { width: 70%; }
        nav a.active { color: var(--charcoal); font-weight: 700; }
        nav a.active::after { width: 70%; background: var(--red); }

        .header-actions {
            min-width: 200px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 16px;
        }

        .icon-btn { background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 6px; border-radius: 6px; transition: color 0.2s, background 0.2s; display: flex; align-items: center; }
        .icon-btn:hover { color: var(--charcoal); background: var(--lightgray); }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.25)), url('images/badminton-hero.jpg');
            background-size: cover;
            background-position: center 40%;
            padding: 180px 60px 170px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 100%);
            pointer-events: none;
        }

        .hero-content { flex: 1; position: relative; z-index: 2; }
        .hero-eyebrow { font-size: 0.72rem; letter-spacing: 0.3em; text-transform: uppercase; color: var(--gold); margin-bottom: 16px; font-weight: 700; }
        .hero-title { font-family: 'Cormorant Garamond', serif; font-size: 4.2rem; font-weight: 600; line-height: 1.1; margin-bottom: 20px; color: var(--white); }
        .hero-title span { color: var(--red); }
        .hero-desc { font-size: 1rem; color: rgba(255,255,255,0.9); line-height: 1.7; max-width: 480px; margin-bottom: 36px; }
        .btn-primary { display: inline-flex; align-items: center; gap: 8px; background: var(--red); color: var(--white); text-decoration: none; padding: 13px 32px; font-size: 0.78rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; border-radius: 40px; transition: background 0.2s, transform 0.2s; }
        .btn-primary:hover { background: var(--red-hover); transform: translateY(-2px); }

        .page-header {
            padding: 290px 60px 290px; 
            border-bottom: 2px solid var(--border);
            position: relative;
            overflow: hidden;
            color: var(--white);
            background-size: cover !important;
            background-position: center !important;
        }

        .page-header::after {
            content: attr(data-label);
            position: absolute; right: 40px; top: 50%; transform: translateY(-50%);
            font-family: 'Cormorant Garamond', serif; font-size: 6rem; font-weight: 600; color: rgba(255,255,255,0.08); text-transform: uppercase; letter-spacing: 0.05em; pointer-events: none;
        }

        .page-header-eyebrow { font-size: 0.7rem; letter-spacing: 0.3em; text-transform: uppercase; color: var(--gold); margin-bottom: 10px; font-weight: 700; position: relative; z-index: 2; }
        .page-header h1 { font-family: 'Cormorant Garamond', serif; font-size: 4.2rem; font-weight: 600; position: relative; z-index: 2; color: var(--white); }

        /* 特定背景图 */
        .page-header[data-category="rackets"] { background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2)), url('images/rackets-bg.jpg'); }
        .page-header[data-category="footwear"] { background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2)), url('images/footwear-bg.jpg'); }
        .page-header[data-category="shuttlecocks"] { background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2)), url('images/shuttlecocks-bg.jpg'); }
        .page-header[data-category="bags"] { background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2)), url('images/bags-bg.jpg'); }
        .page-header[data-category="apparel"] { background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2)), url('images/apparel-bg.jpg'); }
        .page-header[data-category="accessories"] { background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2)), url('images/accessories-bg.jpg'); }
        .page-header[data-category="strings"] { background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.2)), url('images/strings-bg.jpg'); }

        .products-section { padding: 52px 60px 70px; }
        .section-meta { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 36px; border-bottom: 1px solid var(--border); padding-bottom: 20px; }
        .section-label { font-size: 0.68rem; letter-spacing: 0.28em; text-transform: uppercase; color: var(--text-muted); font-weight: 600; }
        .product-count { font-size: 0.8rem; color: var(--text-muted); }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 32px; }

        .product-card { background: var(--white); border-radius: 16px; overflow: hidden; border: 1px solid var(--border); transition: transform 0.3s, box-shadow 0.3s; cursor: pointer; text-decoration: none; color: inherit; display: block; }
        .product-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-hover); border-color: transparent; }
        .card-img-wrap { position: relative; background: #f5f7fa; height: 240px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
        .product-card:hover .card-img-wrap img { transform: scale(1.04); }

        .tag { position: absolute; top: 14px; left: 14px; padding: 4px 10px; font-size: 0.63rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; border-radius: 20px; z-index: 2; }
        .tag-new  { background: var(--charcoal); color: var(--white); }
        .tag-hot  { background: var(--red);  color: var(--white); }
        .tag-sale { background: var(--gold); color: var(--charcoal); }

        .card-body { padding: 20px 20px 24px; border-top: 1px solid var(--border); background: var(--white); }
        .card-series { font-size: 0.65rem; letter-spacing: 0.2em; text-transform: uppercase; color: var(--red); font-weight: 600; margin-bottom: 8px; }
        .card-name { font-size: 1rem; font-weight: 700; color: var(--charcoal); margin-bottom: 14px; line-height: 1.3; }
        .card-footer { display: flex; align-items: center; justify-content: space-between; }
        .card-price { font-size: 1.05rem; font-weight: 800; color: var(--dark); }
        .card-action { width: 34px; height: 34px; background: var(--lightgray); border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: background 0.2s, transform 0.2s; }
        .product-card:hover .card-action { background: var(--charcoal); }
        .product-card:hover .card-action svg { stroke: var(--white); }

        footer { background: var(--white); color: var(--text-muted); text-align: center; padding: 32px 40px; font-size: 0.75rem; letter-spacing: 0.06em; border-top: 1px solid var(--border); }
        footer strong { color: var(--charcoal); }

        @media (max-width: 900px) {
            header { padding: 0 20px; }
            nav a  { padding: 8px 12px; font-size: 0.7rem; }
            .hero { padding: 100px 30px 90px; }
            .page-header { padding: 100px 30px 90px; }
            .hero-title, .page-header h1 { font-size: 2.6rem; }
            .page-header, .products-section { padding-left: 24px; padding-right: 24px; }
        }

        @media (max-width: 640px) {
            nav a { padding: 8px 6px; font-size: 0.62rem; }
            .hero-title, .page-header h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

<header>
    <a href="?category=home" class="logo-area">
        <img src="yonex-logo.png" alt="YONEX" class="logo-image">
    </a>
    <nav>
        <?php foreach ($categories as $key => $label): ?>
            <a href="?category=<?= $key ?>" class="<?= $activeCategory === $key ? 'active' : '' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="header-actions">
        <button class="icon-btn" title="Search"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg></button>
        <button class="icon-btn" title="Cart"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></button>
        <button class="icon-btn" title="Account"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></button>
    </div>
</header>

<main>
<?php if ($activeCategory === 'home'): ?>
    <section class="hero">
        <div class="hero-content">
            <p class="hero-eyebrow">Official Badminton Equipment</p>
            <h1 class="hero-title">Play Like <span>Champions</span><br>Perform at the Top</h1>
            <p class="hero-desc">YONEX has equipped world champions for decades. Explore our latest rackets, footwear, and accessories.</p>
            <a href="?category=rackets" class="btn-primary">Shop Rackets <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
        </div>
    </section>
<?php else: ?>
    <div class="page-header" data-label="<?= htmlspecialchars($categories[$activeCategory]) ?>" data-category="<?= $activeCategory ?>">
        <p class="page-header-eyebrow">YONEX Collection</p>
        <h1><?= htmlspecialchars($categories[$activeCategory]) ?></h1>
    </div>
<?php endif; ?>

    <section class="products-section">
        <div class="section-meta">
            <span class="section-label"><?= $activeCategory === 'home' ? 'Featured Products' : htmlspecialchars($categories[$activeCategory]) . ' Collection' ?></span>
            <span class="product-count"><?= count($currentProducts) ?> products</span>
        </div>

        <div class="product-grid">
            <?php foreach ($currentProducts as $product): ?>
            <a href="#" class="product-card">
                <div class="card-img-wrap">
                    <img src="<?= $product['img'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php if (!empty($product['tag'])): ?>
                        <span class="tag tag-<?= strtolower($product['tag']) ?>"><?= $product['tag'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <p class="card-series"><?= htmlspecialchars($product['series']) ?></p>
                    <p class="card-name"><?= htmlspecialchars($product['name']) ?></p>
                    <div class="card-footer">
                        <span class="card-price"><?= $product['price'] ?></span>
                        <div class="card-action"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> <strong>YONEX</strong>. All rights reserved. &nbsp;|&nbsp; Badminton FYP Project &nbsp;|&nbsp;Multimedia University</p>
</footer>

</body>
</html>
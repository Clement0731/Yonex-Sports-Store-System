<?php 
// 引入你的数据库连接，假设是 db_connect.php
include 'db_connect.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Yonex Professional Stringing Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --yonex-blue: #003366; --yonex-red: #d0021b; }
        .hero { background: var(--yonex-blue); color: white; padding: 60px 20px; text-align: center; }
        .service-card { background: #fff; border: 1px solid #eef2f6; border-radius: 16px; padding: 30px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .tension-guide { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px; }
        .tension-item { background: #f8fafc; padding: 15px; border-radius: 10px; border-left: 4px solid var(--yonex-blue); }
        .string-type { border-bottom: 1px solid #eee; padding: 10px 0; display: flex; justify-content: space-between; }
        .section-title { font-weight: 800; color: var(--yonex-blue); margin-bottom: 20px; text-transform: uppercase; border-left: 5px solid var(--yonex-red); padding-left: 15px; }
    </style>
</head>
<body>

<div class="hero">
    <h1>Professional Stringing Service</h1>
    <p>Precision tensioning for maximum court performance.</p>
</div>

<div class="container my-5">
    <div class="service-card">
        <h3 class="section-title">String Guide</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="string-type"><span>BG65 (Durability)</span> <span>High</span></div>
                <div class="string-type"><span>Exbolt 63 (Repulsion)</span> <span>Extreme</span></div>
                <div class="string-type"><span>Aerobite (Control)</span> <span>Pro Level</span></div>
            </div>
            <div class="col-md-6">
                <p class="text-muted small">Choosing the right string depends on your playstyle. Repulsion strings offer "pop" but lower durability, while thicker strings are built for long-term play.</p>
            </div>
        </div>
    </div>

    <div class="service-card">
        <h3 class="section-title">Tension Recommendation</h3>
        <div class="tension-guide">
            <div class="tension-item"><strong>20-22 lbs</strong><br>Beginner / Maximum Power</div>
            <div class="tension-item"><strong>23-25 lbs</strong><br>Intermediate / Best Balance</div>
            <div class="tension-item"><strong>26-30 lbs</strong><br>Pro / Maximum Control</div>
        </div>
        <p class="text-muted small mt-3">* Higher tension requires better technique to avoid wrist strain and racket damage.</p>
    </div>

    <div class="service-card">
        <h3 class="section-title">Current Price List</h3>
        <?php
        $services = $conn->query("SELECT * FROM service_options");
        while($row = $services->fetch_assoc()) {
            echo "<div class='string-type'>
                    <span>" . htmlspecialchars($row['option_name']) . " (" . ucfirst($row['service_type']) . ")</span>
                    <span class='fw-bold'>RM " . number_format($row['additional_price'], 2) . "</span>
                  </div>";
        }
        ?>
    </div>
</div>

</body>
</html>
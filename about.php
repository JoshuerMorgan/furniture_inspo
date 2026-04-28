<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="homestyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manufacturing+Consent&display=swap" rel="stylesheet">
    <title>About Us — Oak & Wool</title>
</head>
<body>
    <nav id="nav">
        <div id="img-container">
            <a href="homepage.php"><h2 id="logo">Oak & Wool</h2></a>
        </div>
        <ul>
            <li><a href="homepage.php">HOME</a></li>
            <li><a href="blog.php">BLOG</a></li>
            <li><a href="about.php">ABOUT US</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="dashboard.php">DASHBOARD</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <div class="page-header">
            <h1 class="page-header-title">About Oak &amp; Wool</h1>
            <p class="page-header-sub">A better way to find, save, and organize furniture inspiration.</p>
        </div>

        <div class="about-body">

            <section class="about-section">
                <h2 class="about-section-title">The Problem We Solve</h2>
                <p>Furnishing a home or office space is harder than it should be. Most people have limited design experience, and the inspiration they need is scattered across dozens of disconnected websites and social media platforms. Product details — materials, dimensions, styles, designer information — are rarely organized in one place, making it nearly impossible to compare options, save ideas, and return to them later in any structured way.</p>
                <p>Oak &amp; Wool was built to change that.</p>
            </section>

            <section class="about-section">
                <h2 class="about-section-title">What We Offer</h2>
                <ul class="about-list">
                    <li><strong>A centralized furniture catalog</strong> — every item organized with consistent details including material, dimensions, color, style, category, and designer.</li>
                    <li><strong>Open browsing</strong> — visitors can explore the full catalog without creating an account.</li>
                    <li><strong>Personal favorites</strong> — registered users can save items they love and build a shortlist at their own pace.</li>
                    <li><strong>Inspiration boards</strong> — organize saved furniture into named boards, whether you are planning a living room refresh, a home office, or a full redesign.</li>
                    <li><strong>Reliable data</strong> — a relational database keeps everything consistent and connected, so the relationships between furniture, categories, designers, and users are always accurate.</li>
                </ul>
            </section>

            <section class="about-section">
                <h2 class="about-section-title">Built with Structure in Mind</h2>
                <p>At its core, Oak &amp; Wool is powered by a relational database designed to support everything the platform does. Furniture items are linked to their categories, designers, and user interactions in a way that keeps data organized and meaningful. Administrative actions are logged for accountability, and the system is built to grow — whether that means more furniture, more users, or more ways to explore.</p>
                <p>We believe good design deserves good infrastructure behind it.</p>
            </section>

        </div>
    </main>
</body>
</html>

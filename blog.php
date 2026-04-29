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
    <title>Blog — Oak & Wool</title>
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
            <li><a href="favorites.php">FAVORITES</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="dashboard.php">DASHBOARD</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <div class="page-header">
            <h1 class="page-header-title">Blog</h1>
            <p class="page-header-sub">Ideas, inspiration, and the art of making things yourself.</p>
        </div>

        <div class="blog-feed">

            <article class="blog-card">
                <div class="blog-meta">April 10, 2026 &bull; By the Oak &amp; Wool Team</div>
                <h2 class="blog-card-title">The Rise of DIY Home Decor: Why More People Are Making It Themselves</h2>
                <p>Over the past decade, do-it-yourself home decor has shifted from a fringe hobby to a mainstream movement. What was once driven by necessity — building furniture because you could not afford to buy it — has evolved into a conscious lifestyle choice rooted in creativity, sustainability, and personal expression.</p>
                <p>Social media platforms like Pinterest and Instagram played a major role in this shift. For the first time, everyday people could share their builds with a global audience, turning a weekend workshop project into viral inspiration for thousands. Step-by-step tutorials became a genre of their own, and with them came a growing community of makers eager to trade ideas, materials, and techniques.</p>
                <p>The pandemic years accelerated the trend further. With more time at home and supply chain disruptions making furniture harder to source, people turned to their garages and workshops. Hardware stores reported record sales. YouTube channels dedicated to woodworking and upcycling saw subscriber counts explode. DIY was no longer just a pastime — it was a response to the world.</p>
                <p>Today the movement continues to grow, fueled by a generation that values authenticity over mass production. A handmade bookshelf carries a story that a flatpack never will.</p>
            </article>

            <article class="blog-card">
                <div class="blog-meta">March 28, 2026 &bull; By the Oak &amp; Wool Team</div>
                <h2 class="blog-card-title">From Sawdust to Showpiece: Building Furniture at Home</h2>
                <p>There is something deeply satisfying about sitting on a chair you built yourself. The slight imperfection in the joint, the grain you chose at the lumber yard, the finish you mixed by hand — these details make a piece of furniture feel alive in a way a store-bought item rarely does.</p>
                <p>Building furniture at home has never been more accessible. Entry-level tools are affordable, plans are freely available online, and communities of experienced makers are generous with advice. Whether you are cutting your first dovetail or experimenting with steam bending, there is a resource for every skill level.</p>
                <p>Beyond the craft, there are practical advantages. You control the dimensions, the material, the color, and the finish. You can build to fit an awkward alcove, match an existing piece, or create something entirely original. Custom furniture from a maker or studio can cost thousands; building it yourself can bring that price down dramatically while teaching you skills that compound over time.</p>
                <p>The learning curve is real, but so is the reward. Start small — a side table, a simple bench — and let the confidence build from there. Your living room might just become your favorite gallery.</p>
            </article>

            <article class="blog-card">
                <div class="blog-meta">March 14, 2026 &bull; By the Oak &amp; Wool Team</div>
                <h2 class="blog-card-title">DIY Design Inspiration: How Social Media Changed How We Furnish Our Homes</h2>
                <p>Before social media, home design inspiration came from a narrow set of sources — glossy magazines, showrooms, and the occasional TV makeover program. The aesthetic was polished, aspirational, and largely unattainable for most households. Social media tore that model apart.</p>
                <p>Platforms built around images democratized interior design overnight. Suddenly a renter in a studio apartment could share their space and attract the same attention as a professional designer. Authenticity became the new currency. Rooms with visible wear, mismatched chairs, and thrift-store finds accumulated millions of saves and likes. The imperfect became desirable.</p>
                <p>This shift pushed people to experiment. When you can document and share the process — the before, the messy middle, the reveal — DIY becomes a form of storytelling. Followers become an audience, and that audience offers both accountability and encouragement. Projects that might have sat unfinished in a corner now get completed because someone online is waiting for the update.</p>
                <p>The result is a generation of home decorators who think in terms of mood boards and color palettes, who know the difference between mid-century modern and Scandinavian minimalism, and who are not afraid to pick up a paintbrush or a drill to get the look they want. Inspiration is everywhere — and so are the tools to act on it.</p>
            </article>

        </div>
    </main>
</body>
</html>

<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "root";
$dbname = "furniture_inspiration_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cat_result = $conn->query("SELECT category_id, category_name FROM Category ORDER BY category_name");
$categories = [];
while ($cat = $cat_result->fetch_assoc()) {
    $categories[] = $cat;
}

$mat_result = $conn->query("SELECT DISTINCT material FROM Furniture WHERE material IS NOT NULL");
$materials = [];
while ($mat = $mat_result->fetch_assoc()) {
    foreach (explode(',', $mat['material']) as $m) {
        $m = trim($m);
        if ($m !== '') $materials[$m] = true;
    }
}
$materials = array_keys($materials);
sort($materials);

$search      = isset($_GET['search'])   ? trim($_GET['search'])  : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$material    = isset($_GET['material']) ? trim($_GET['material']) : '';

$sql = "SELECT f.furniture_id, f.furniture_name, f.image_url, d.designer_name, c.category_name
        FROM Furniture f
        JOIN Designer d ON f.designer_id = d.designer_id
        JOIN Category c ON f.category_id = c.category_id
        WHERE 1=1";

$params = [];
$types  = '';

if ($search !== '') {
    $sql .= " AND (f.furniture_name LIKE ? OR d.designer_name LIKE ?)";
    $like      = '%' . $search . '%';
    $params[]  = $like;
    $params[]  = $like;
    $types    .= 'ss';
}

if ($category_id > 0) {
    $sql .= " AND f.category_id = ?";
    $params[] = $category_id;
    $types   .= 'i';
}

if ($material !== '') {
    $sql .= " AND f.material LIKE ?";
    $params[] = '%' . $material . '%';
    $types   .= 's';
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$furniture_items = [];
while ($row = $result->fetch_assoc()) {
    $furniture_items[] = $row;
}

$saved_ids = [];
if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
    $fav_stmt = $conn->prepare(
        "SELECT ibi.furniture_id
         FROM Inspiration_Board_Item ibi
         JOIN Inspiration_Board ib ON ibi.board_id = ib.board_id
         WHERE ib.user_id = ?"
    );
    $fav_stmt->bind_param("i", $user_id);
    $fav_stmt->execute();
    $fav_result = $fav_stmt->get_result();
    while ($fav_row = $fav_result->fetch_assoc()) {
        $saved_ids[] = $fav_row['furniture_id'];
    }
    $fav_stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="homestyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manufacturing+Consent&display=swap" rel="stylesheet">
    <title>Full Catalog — Oak & Wool</title>
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
    <div id="catalog-header">
        <h2 id="catalog-title">Full Catalog</h2>
        <form id="search-form" method="GET" action="fullCatalog.php">
            <input
                class="search-input"
                type="text"
                name="search"
                placeholder="Search by name or designer..."
                value="<?= htmlspecialchars($search) ?>"
            >
            <select class="category-select" name="category">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>"
                        <?= $category_id === (int)$cat['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select class="category-select" name="material">
                <option value="">All Materials</option>
                <?php foreach ($materials as $mat): ?>
                    <option value="<?= htmlspecialchars($mat) ?>"
                        <?= $material === $mat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="orange-btn" type="submit">Search</button>
        </form>
    </div>

    <section id="catalog">
        <div id="cards-container">
            <?php if (!empty($furniture_items)): ?>
                <?php foreach ($furniture_items as $row): ?>
                    <?php $is_saved = in_array($row['furniture_id'], $saved_ids); ?>
                    <div class="catalog-item">
                        <img class="furniture-img" src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['furniture_name']) ?>">
                        <h4 class="furniture-name"><?= htmlspecialchars($row['furniture_name']) ?></h4>
                        <h4 class="designer-name"><?= htmlspecialchars($row['designer_name']) ?></h4>
                        <button class="fav-btn <?= $is_saved ? 'saved' : '' ?>" data-id="<?= $row['furniture_id'] ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M305 151.1L320 171.8L335 151.1C360 116.5 400.2 96 442.9 96C516.4 96 576 155.6 576 229.1L576 231.7C576 343.9 436.1 474.2 363.1 529.9C350.7 539.3 335.5 544 320 544C304.5 544 289.2 539.4 276.9 529.9C203.9 474.2 64 343.9 64 231.7L64 229.1C64 155.6 123.6 96 197.1 96C239.8 96 280 116.5 305 151.1z"/></svg>
                            <span><?= $is_saved ? 'Saved' : 'Save' ?></span>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p id="no-results">No furniture found matching your search.</p>
            <?php endif; ?>
        </div>
    </section>
    </main>

    <script>
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

        document.querySelectorAll('.fav-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                if (!isLoggedIn) {
                    window.location.href = 'index.html';
                    return;
                }

                const furnitureId = this.dataset.id;
                const span = this.querySelector('span');

                fetch('toggle_favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'furniture_id=' + furnitureId
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const saved = data.action === 'added';
                        this.classList.toggle('saved', saved);
                        span.textContent = saved ? 'Saved' : 'Save';
                    }
                });
            });
        });
    </script>
</body>
</html>

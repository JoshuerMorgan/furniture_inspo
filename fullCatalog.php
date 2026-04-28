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
                    <div class="catalog-item">
                        <img class="furniture-img" src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['furniture_name']) ?>">
                        <h4 class="furniture-name"><?= htmlspecialchars($row['furniture_name']) ?></h4>
                        <h4 class="designer-name"><?= htmlspecialchars($row['designer_name']) ?></h4>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p id="no-results">No furniture found matching your search.</p>
            <?php endif; ?>
        </div>
    </section>
    </main>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id  = (int) $_SESSION['user_id'];
$firstname = htmlspecialchars($_SESSION['firstname'] ?? 'User');

$conn = new mysqli("localhost", "root", "root", "furniture_inspiration_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT ib.board_id, ib.board_name, ib.created_date,
               f.furniture_id, f.furniture_name, f.image_url, d.designer_name
        FROM Inspiration_Board ib
        LEFT JOIN Inspiration_Board_Item ibi ON ib.board_id = ibi.board_id
        LEFT JOIN Furniture f ON ibi.furniture_id = f.furniture_id
        LEFT JOIN Designer d ON f.designer_id = d.designer_id
        WHERE ib.user_id = ?
        ORDER BY ib.board_id, f.furniture_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$boards = [];
while ($row = $result->fetch_assoc()) {
    $bid = $row['board_id'];
    if (!isset($boards[$bid])) {
        $boards[$bid] = [
            'board_name'   => $row['board_name'],
            'created_date' => $row['created_date'],
            'items'        => []
        ];
    }
    if ($row['furniture_id']) {
        $boards[$bid]['items'][] = [
            'furniture_id'   => $row['furniture_id'],
            'furniture_name' => $row['furniture_name'],
            'image_url'      => $row['image_url'],
            'designer_name'  => $row['designer_name'],
        ];
    }
}
$stmt->close();
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
    <title>My Inspiration Board</title>
    <link rel="stylesheet" href="favoritesStyle.css">
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
        </ul>
    </nav>
    <main>
        <div id="favorites-header">
            <h2><?= $firstname ?>'s Inspiration Board</h2>
            <p>Items you've saved from the catalog.</p>
        </div>

        <?php if (empty($boards)): ?>
            <div class="board-section">
                <p class="empty-board">You haven't saved any items yet. <a href="homepage.php">Browse the catalog</a> to get started.</p>
            </div>
        <?php else: ?>
            <?php foreach ($boards as $board_id => $board): ?>
                <div class="board-section">
                    <h3 class="board-title"><?= htmlspecialchars($board['board_name']) ?></h3>
                    <?php if (empty($board['items'])): ?>
                        <p class="empty-board">This board is empty.</p>
                    <?php else: ?>
                        <div class="board-grid">
                            <?php foreach ($board['items'] as $item): ?>
                                <div class="catalog-item" id="item-<?= $item['furniture_id'] ?>">
                                    <img class="furniture-img"
                                         src="<?= htmlspecialchars($item['image_url']) ?>"
                                         alt="<?= htmlspecialchars($item['furniture_name']) ?>">
                                    <h4 class="furniture-name"><?= htmlspecialchars($item['furniture_name']) ?></h4>
                                    <h4 class="designer-name"><?= htmlspecialchars($item['designer_name']) ?></h4>
                                    <button class="fav-btn" data-id="<?= $item['furniture_id'] ?>" data-board-id="<?= $board_id ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="white"><path d="M305 151.1L320 171.8L335 151.1C360 116.5 400.2 96 442.9 96C516.4 96 576 155.6 576 229.1L576 231.7C576 343.9 436.1 474.2 363.1 529.9C350.7 539.3 335.5 544 320 544C304.5 544 289.2 539.4 276.9 529.9C203.9 474.2 64 343.9 64 231.7L64 229.1C64 155.6 123.6 96 197.1 96C239.8 96 280 116.5 305 151.1z"/></svg>
                                        Remove
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <script>
        document.querySelectorAll('.fav-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const furnitureId = this.dataset.id;
                const boardId     = this.dataset.boardId;
                const card = document.getElementById('item-' + furnitureId);

                fetch('toggle_favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'furniture_id=' + furnitureId + '&board_id=' + boardId
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.action === 'removed') {
                        card.style.transition = 'opacity 0.3s';
                        card.style.opacity = '0';
                        setTimeout(() => card.remove(), 300);
                    }
                });
            });
        });
    </script>
</body>
</html>

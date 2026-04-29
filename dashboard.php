<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: homepage.php");
    exit();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = "localhost";
$db_username = "root";
$db_password = "root";
$dbname = "furniture_inspiration_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Handle POST actions (PRG pattern)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action']  ?? '';
    $section = $_POST['section'] ?? 'users';
    $flash_msg  = '';
    $flash_type = 'success';

    try {
        if ($action === 'delete_user') {
            $uid = (int)($_POST['user_id'] ?? 0);
            if ($uid === (int)$_SESSION['user_id']) {
                throw new Exception("You cannot delete your own account.");
            }
            $conn->begin_transaction();
            $conn->query("DELETE FROM Admin_Log WHERE admin_user_id = $uid");
            $conn->query("DELETE FROM User_Favorite WHERE user_id = $uid");
            $conn->query("DELETE FROM Inspiration_Board_Item WHERE board_id IN (SELECT board_id FROM Inspiration_Board WHERE user_id = $uid)");
            $conn->query("DELETE FROM Inspiration_Board WHERE user_id = $uid");
            $conn->query("DELETE FROM Users WHERE user_id = $uid");
            $conn->commit();
            $flash_msg = "User deleted successfully.";
            $section = 'users';

        } elseif ($action === 'delete_furniture') {
            $fid = (int)($_POST['furniture_id'] ?? 0);
            $conn->begin_transaction();
            $conn->query("DELETE FROM User_Favorite WHERE furniture_id = $fid");
            $conn->query("DELETE FROM Inspiration_Board_Item WHERE furniture_id = $fid");
            $conn->query("DELETE FROM Furniture WHERE furniture_id = $fid");
            $conn->commit();
            $flash_msg = "Furniture deleted successfully.";
            $section = 'furniture';

        } elseif ($action === 'delete_category') {
            $cid   = (int)($_POST['category_id'] ?? 0);
            $count = $conn->query("SELECT COUNT(*) as cnt FROM Furniture WHERE category_id = $cid")->fetch_assoc()['cnt'];
            if ($count > 0) {
                throw new Exception("Cannot delete: $count furniture item(s) use this category.");
            }
            $conn->query("DELETE FROM Category WHERE category_id = $cid");
            $flash_msg = "Category deleted successfully.";
            $section = 'categories';

        } elseif ($action === 'delete_designer') {
            $did   = (int)($_POST['designer_id'] ?? 0);
            $count = $conn->query("SELECT COUNT(*) as cnt FROM Furniture WHERE designer_id = $did")->fetch_assoc()['cnt'];
            if ($count > 0) {
                throw new Exception("Cannot delete: $count furniture item(s) are assigned to this designer.");
            }
            $conn->query("DELETE FROM Designer WHERE designer_id = $did");
            $flash_msg = "Designer deleted successfully.";
            $section = 'designers';

        } elseif ($action === 'edit_furniture') {
            $fid      = (int)($_POST['furniture_id']  ?? 0);
            $name     = trim($_POST['furniture_name'] ?? '');
            $color    = trim($_POST['color']          ?? '');
            $material = trim($_POST['material']       ?? '');
            $style    = trim($_POST['style']          ?? '');
            $img      = trim($_POST['image_url']      ?? '');
            $cat_id   = (int)($_POST['category_id']   ?? 0);
            $des_id   = (int)($_POST['designer_id']   ?? 0);

            $stmt = $conn->prepare(
                "UPDATE Furniture SET furniture_name=?, color=?, material=?, style=?, image_url=?, category_id=?, designer_id=? WHERE furniture_id=?"
            );
            $stmt->bind_param("sssssiii", $name, $color, $material, $style, $img, $cat_id, $des_id, $fid);
            $stmt->execute();
            $flash_msg = "Furniture updated successfully.";
            $section = 'furniture';
        }

    } catch (Exception $e) {
        try { $conn->rollback(); } catch (Exception $ex) {}
        $flash_msg  = $e->getMessage();
        $flash_type = 'error';
    }

    $_SESSION['flash'] = ['message' => $flash_msg, 'type' => $flash_type];
    header("Location: dashboard.php?section=$section");
    exit();
}

// GET — retrieve flash from session
$flash      = '';
$flash_type = '';
if (isset($_SESSION['flash'])) {
    $flash      = $_SESSION['flash']['message'];
    $flash_type = $_SESSION['flash']['type'];
    unset($_SESSION['flash']);
}

$allowed_sections = ['users', 'furniture', 'categories', 'designers'];
$active_section   = in_array($_GET['section'] ?? '', $allowed_sections) ? $_GET['section'] : 'users';

// Fetch all data
$users = $conn->query(
    "SELECT user_id, first_name, last_name, email, role FROM Users ORDER BY user_id"
)->fetch_all(MYSQLI_ASSOC);

$furniture_list = $conn->query(
    "SELECT f.furniture_id, f.furniture_name, f.color, f.material, f.style, f.image_url,
            f.category_id, f.designer_id, c.category_name, d.designer_name
     FROM Furniture f
     JOIN Category c ON f.category_id = c.category_id
     JOIN Designer d ON f.designer_id = d.designer_id
     ORDER BY f.furniture_id"
)->fetch_all(MYSQLI_ASSOC);

$categories = $conn->query(
    "SELECT c.category_id, c.category_name, COUNT(f.furniture_id) as item_count
     FROM Category c
     LEFT JOIN Furniture f ON f.category_id = c.category_id
     GROUP BY c.category_id ORDER BY c.category_id"
)->fetch_all(MYSQLI_ASSOC);

$designers = $conn->query(
    "SELECT d.designer_id, d.designer_name, d.contact_email, d.phone_number, COUNT(f.furniture_id) as item_count
     FROM Designer d
     LEFT JOIN Furniture f ON f.designer_id = d.designer_id
     GROUP BY d.designer_id ORDER BY d.designer_id"
)->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="homestyle.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manufacturing+Consent&display=swap" rel="stylesheet">
    <title>Dashboard — Oak & Wool</title>
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
            <li><a href="dashboard.php">DASHBOARD</a></li>
        </ul>
    </nav>

    <div id="dashboard-wrapper">

        <!-- Sidebar -->
        <aside id="sidebar">
            <h3>Manage</h3>
            <button class="sidebar-tab <?= $active_section === 'users'      ? 'active' : '' ?>" data-target="users">Users</button>
            <button class="sidebar-tab <?= $active_section === 'furniture'  ? 'active' : '' ?>" data-target="furniture">Furniture</button>
            <button class="sidebar-tab <?= $active_section === 'categories' ? 'active' : '' ?>" data-target="categories">Categories</button>
            <button class="sidebar-tab <?= $active_section === 'designers'  ? 'active' : '' ?>" data-target="designers">Designers</button>
        </aside>

        <!-- Content -->
        <main id="dashboard-content">

            <?php if ($flash): ?>
                <div class="flash <?= htmlspecialchars($flash_type) ?>"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>

            <!-- USERS -->
            <section id="users" class="dash-section <?= $active_section === 'users' ? 'active' : '' ?>">
                <h2 class="section-heading">Users</h2>
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $u['user_id'] ?></td>
                                <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="role-badge <?= $u['role'] ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                                <td>
                                    <?php if ($u['user_id'] !== (int)$_SESSION['user_id']): ?>
                                        <form method="POST" onsubmit="return confirm('Delete this user?')">
                                            <input type="hidden" name="action"   value="delete_user">
                                            <input type="hidden" name="user_id"  value="<?= $u['user_id'] ?>">
                                            <input type="hidden" name="section"  value="users">
                                            <button class="btn-delete" type="submit">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <em style="color:#aaa;font-size:0.82em">You</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- FURNITURE -->
            <section id="furniture" class="dash-section <?= $active_section === 'furniture' ? 'active' : '' ?>">
                <h2 class="section-heading">Furniture</h2>
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Designer</th>
                            <th>Material</th>
                            <th>Color</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($furniture_list as $f): ?>
                            <tr>
                                <td><?= $f['furniture_id'] ?></td>
                                <td><?= htmlspecialchars($f['furniture_name']) ?></td>
                                <td><?= htmlspecialchars($f['category_name']) ?></td>
                                <td><?= htmlspecialchars($f['designer_name']) ?></td>
                                <td><?= htmlspecialchars($f['material']) ?></td>
                                <td><?= htmlspecialchars($f['color']) ?></td>
                                <td style="white-space:nowrap">
                                    <button class="btn-edit"
                                        data-id="<?= $f['furniture_id'] ?>"
                                        data-name="<?= htmlspecialchars($f['furniture_name'],    ENT_QUOTES) ?>"
                                        data-color="<?= htmlspecialchars($f['color'],            ENT_QUOTES) ?>"
                                        data-material="<?= htmlspecialchars($f['material'],      ENT_QUOTES) ?>"
                                        data-style="<?= htmlspecialchars($f['style'],            ENT_QUOTES) ?>"
                                        data-image-url="<?= htmlspecialchars($f['image_url'],    ENT_QUOTES) ?>"
                                        data-category-id="<?= $f['category_id'] ?>"
                                        data-designer-id="<?= $f['designer_id'] ?>">
                                        Edit
                                    </button>
                                    <form method="POST" style="display:inline" onsubmit="return confirm('Delete this furniture?')">
                                        <input type="hidden" name="action"       value="delete_furniture">
                                        <input type="hidden" name="furniture_id" value="<?= $f['furniture_id'] ?>">
                                        <input type="hidden" name="section"      value="furniture">
                                        <button class="btn-delete" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- CATEGORIES -->
            <section id="categories" class="dash-section <?= $active_section === 'categories' ? 'active' : '' ?>">
                <h2 class="section-heading">Categories</h2>
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Furniture Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $c): ?>
                            <tr>
                                <td><?= $c['category_id'] ?></td>
                                <td><?= htmlspecialchars($c['category_name']) ?></td>
                                <td><?= $c['item_count'] ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Delete this category?')">
                                        <input type="hidden" name="action"      value="delete_category">
                                        <input type="hidden" name="category_id" value="<?= $c['category_id'] ?>">
                                        <input type="hidden" name="section"     value="categories">
                                        <button class="btn-delete" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- DESIGNERS -->
            <section id="designers" class="dash-section <?= $active_section === 'designers' ? 'active' : '' ?>">
                <h2 class="section-heading">Designers</h2>
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Furniture Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($designers as $d): ?>
                            <tr>
                                <td><?= $d['designer_id'] ?></td>
                                <td><?= htmlspecialchars($d['designer_name']) ?></td>
                                <td><?= htmlspecialchars($d['contact_email']) ?></td>
                                <td><?= htmlspecialchars($d['phone_number']) ?></td>
                                <td><?= $d['item_count'] ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Delete this designer?')">
                                        <input type="hidden" name="action"      value="delete_designer">
                                        <input type="hidden" name="designer_id" value="<?= $d['designer_id'] ?>">
                                        <input type="hidden" name="section"     value="designers">
                                        <button class="btn-delete" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

        </main>
    </div>

    <!-- Edit Furniture Modal -->
    <div id="edit-modal">
        <div class="modal-box">
            <h3>Edit Furniture</h3>
            <form method="POST">
                <input type="hidden" name="action"       value="edit_furniture">
                <input type="hidden" name="section"      value="furniture">
                <input type="hidden" name="furniture_id" id="edit-id">
                <div class="modal-field">
                    <label>Name</label>
                    <input type="text" name="furniture_name" id="edit-name">
                </div>
                <div class="modal-field">
                    <label>Color</label>
                    <input type="text" name="color" id="edit-color">
                </div>
                <div class="modal-field">
                    <label>Material</label>
                    <input type="text" name="material" id="edit-material">
                </div>
                <div class="modal-field">
                    <label>Style</label>
                    <input type="text" name="style" id="edit-style">
                </div>
                <div class="modal-field">
                    <label>Image URL</label>
                    <input type="text" name="image_url" id="edit-image-url">
                </div>
                <div class="modal-field">
                    <label>Category</label>
                    <select name="category_id" id="edit-category">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-field">
                    <label>Designer</label>
                    <select name="designer_id" id="edit-designer">
                        <?php foreach ($designers as $d): ?>
                            <option value="<?= $d['designer_id'] ?>"><?= htmlspecialchars($d['designer_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="close-modal">Cancel</button>
                    <button type="submit" class="orange-btn btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sidebar tab switching
        document.querySelectorAll('.sidebar-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.dash-section').forEach(s => s.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.target).classList.add('active');
            });
        });

        // Edit modal
        const modal = document.getElementById('edit-modal');

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('edit-id').value          = btn.dataset.id;
                document.getElementById('edit-name').value        = btn.dataset.name;
                document.getElementById('edit-color').value       = btn.dataset.color;
                document.getElementById('edit-material').value    = btn.dataset.material;
                document.getElementById('edit-style').value       = btn.dataset.style;
                document.getElementById('edit-image-url').value   = btn.dataset.imageUrl;
                document.getElementById('edit-category').value    = btn.dataset.categoryId;
                document.getElementById('edit-designer').value    = btn.dataset.designerId;
                modal.classList.add('open');
            });
        });

        document.getElementById('close-modal').addEventListener('click', () => modal.classList.remove('open'));
        modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });
    </script>
</body>
</html>

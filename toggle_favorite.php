<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['furniture_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$user_id      = (int) $_SESSION['user_id'];
$furniture_id = (int) $_POST['furniture_id'];
$req_board_id = isset($_POST['board_id']) ? (int) $_POST['board_id'] : 0;

$conn = new mysqli("localhost", "root", "root", "furniture_inspiration_db");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit();
}

if ($req_board_id > 0) {
    // Verify the requested board actually belongs to this user
    $verify = $conn->prepare("SELECT board_id FROM Inspiration_Board WHERE board_id = ? AND user_id = ?");
    $verify->bind_param("ii", $req_board_id, $user_id);
    $verify->execute();
    $verify->store_result();
    if ($verify->num_rows > 0) {
        $board_id = $req_board_id;
    }
    $verify->close();
}

if (empty($board_id)) {
    // Fall back to get or create the user's default board
    $stmt = $conn->prepare("SELECT board_id FROM Inspiration_Board WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        $ins = $conn->prepare("INSERT INTO Inspiration_Board (user_id, board_name) VALUES (?, 'My Board')");
        $ins->bind_param("i", $user_id);
        $ins->execute();
        $board_id = $conn->insert_id;
        $ins->close();
    } else {
        $stmt->bind_result($board_id);
        $stmt->fetch();
        $stmt->close();
    }
}

// Toggle: remove if exists, add if not
$check = $conn->prepare("SELECT board_item_id FROM Inspiration_Board_Item WHERE board_id = ? AND furniture_id = ?");
$check->bind_param("ii", $board_id, $furniture_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    $del = $conn->prepare("DELETE FROM Inspiration_Board_Item WHERE board_id = ? AND furniture_id = ?");
    $del->bind_param("ii", $board_id, $furniture_id);
    $del->execute();
    $del->close();
    echo json_encode(['success' => true, 'action' => 'removed']);
} else {
    $check->close();
    $add = $conn->prepare("INSERT INTO Inspiration_Board_Item (board_id, furniture_id) VALUES (?, ?)");
    $add->bind_param("ii", $board_id, $furniture_id);
    $add->execute();
    $add->close();
    echo json_encode(['success' => true, 'action' => 'added']);
}

$conn->close();
?>

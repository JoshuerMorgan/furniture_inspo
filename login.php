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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        die("Email and password are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    $sql = "SELECT user_id, first_name, password, role FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $first_name, $hashed_password, $role);
        $stmt->fetch();

        if ($hashed_password && password_verify($password, $hashed_password)) {
            $_SESSION['user_id']   = $user_id;
            $_SESSION['firstname'] = $first_name;
            $_SESSION['email']     = $email;
            $_SESSION['role']      = $role;
            header("Location: homepage.php");
            exit();
        } else {
            die("Invalid credentials.");
        }
    } else {
        die("No account found with that email.");
    }

    $stmt->close();
}

$conn->close();
?>

<?php
session_start();
$host = 'localhost';
$db_name = 'event mangament system';
$username = 'root';
$password = '';
$user_role = $_SESSION['user_role'] ?? null; // Get user role from session
$user_name = $_SESSION['user_name'] ?? null; // Get user name from session
$user_id = $_SESSION['user_id'] ?? null ;
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    // Check admin privileges
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: index.php");
        exit;
    }

    $user_id = (int)$_POST['user_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        header("Location: ../adminDashboard.php");
    } catch (PDOException $e) {
        die("Error deleting user: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>

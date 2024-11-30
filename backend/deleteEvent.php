<?php
session_start();

// Database connection
$host = 'localhost';
$db_name = 'event mangament system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if user is logged in and is an organizer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "admin"& $_SESSION['user_role'] != "organizer") {
 
  die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];

    // Check if the event belongs to the logged-in organizer
    $stmt = $pdo->prepare("DELETE FROM Events WHERE id = :event_id AND organizer_id = :organizer_id");
    $result = $stmt->execute([
        'event_id' => $event_id,
        'organizer_id' => $_SESSION['user_id']
    ]);

    if ($result) {
        header("Location: ../index.php"); 
        exit;
    } else {
        echo "Error deleting event.";
    }
}
?>

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
        die("You must be logged in to register for events.");
    }

    $user_id = $_SESSION['user_id'];
    $email = $_SESSION['user_email']; // User's email from session
    $event_id = $_POST['event_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO Registrations (user_id, event_id, contact_details) VALUES (:user_id, :event_id, :contact_details)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':event_id' => $event_id,
            ':contact_details' => $email,
        ]);

        echo "Successfully registered for the event!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

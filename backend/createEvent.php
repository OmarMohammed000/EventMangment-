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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../register&login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $organizer_id = $_SESSION['user_id']; // Organizer ID from session
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['descreption']), ENT_QUOTES, 'UTF-8');
    $event_date = $_POST['time']; // datetime-local input format

    // Validate input
    $errors = [];
    if (empty($title)) $errors[] = "Event title is required.";
    if (empty($event_date)) $errors[] = "Event time is required.";

    if (empty($errors)) {
        try {
            // Insert event into the database
            $stmt = $pdo->prepare("INSERT INTO Events (organizer_id, title, description, event_date) VALUES (:organizer_id, :title, :description, :event_date)");
            $stmt->bindParam(':organizer_id', $organizer_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':event_date', $event_date);
            $stmt->execute();

            // Redirect to events list or confirmation page
            header("Location: ../index.php#events");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Display errors if any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p class='text-danger'>$error</p>";
        }
    }
}
?>

<?php
session_start();

// Ensure the user is an organizer or admin
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'organizer'])) {
    header("Location: index.php");
    exit;
}

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

// Handle notification form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;
    $message = trim($_POST['message'] ?? '');

    if (empty($event_id) || empty($message)) {
        die("Event ID and message are required.");
    }

    try {
        // Get all attendees of the event
        $stmt = $pdo->prepare("
            SELECT r.contact_details, u.id AS recipient_id
            FROM registrations r
            JOIN users u ON r.contact_details = u.email
            WHERE r.event_id = :event_id
        ");
        $stmt->execute(['event_id' => $event_id]);
        $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Insert notifications for each attendee
        $notificationStmt = $pdo->prepare("
            INSERT INTO Notifications (event_id, recipient_id, message) 
            VALUES (:event_id, :recipient_id, :message)
        ");

        foreach ($attendees as $attendee) {
            $notificationStmt->execute([
                'event_id' => $event_id,
                'recipient_id' => $attendee['recipient_id'],
                'message' => $message,
            ]);
        }

        echo "Notifications sent successfully.";
    } catch (PDOException $e) {
        die("Error sending notifications: " . $e->getMessage());
    }
}
?>

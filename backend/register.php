<?php

session_start();


$host = 'localhost'; 
$db_name = 'event mangament system'; 
$username = 'root';
$password = ''; 

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize user input
    $full_name = htmlspecialchars(trim($_POST['registerName'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['registerEmail'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars(trim($_POST['registerPassword'] ?? ''), ENT_QUOTES, 'UTF-8');
    $role = htmlspecialchars(trim($_POST['userType'] ?? ''), ENT_QUOTES, 'UTF-8');
    

    // Validate inputs
    $errors = [];
    if (empty($full_name)) $errors[] = "Full Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (!in_array($role, ['regular', 'organizer', 'admin'])) $errors[] = "Invalid role selected.";

    // Check for errors before proceeding
    if (empty($errors)) {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Prepare SQL query
            $stmt = $pdo->prepare("INSERT INTO Users (email, password_hash, full_name, role) VALUES (:email, :password_hash, :full_name, :role)");

            // Bind parameters
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':role', $role);

            // Execute query
            $stmt->execute();

            // Success message or redirection
            header("Location: ../index.php");
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate email error
                $errors[] = "Email is already registered.";
            } else {
                $errors[] = "Error: " . $e->getMessage();
            }
        }
    }
}

// If there are errors, display them
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p class='invailded-feedback'>$error</p>";
    }
}
?>

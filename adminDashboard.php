<?php
session_start();
$host = 'localhost';
$db_name = 'event mangament system';
$username = 'root';
$password = '';
$user_role = $_SESSION['user_role'] ?? null; // Get user role from session
$user_name = $_SESSION['user_name'] ?? null; // Get user name from session
$user_id = $_SESSION['user_id'] ?? null;
try {
  $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}
// Fetch all users
try {
  $stmt = $pdo->prepare("SELECT id, full_name, email FROM Users");
  $stmt->execute();
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error fetching users: " . $e->getMessage());
}
?>
<?php
// Fetch total number of events
try {
  $stmt = $pdo->prepare("SELECT COUNT(*) AS event_count FROM Events");
  $stmt->execute();
  $event_count = $stmt->fetch(PDO::FETCH_ASSOC)['event_count'];
} catch (PDOException $e) {
  die("Error fetching event count: " . $e->getMessage());
}

// Fetch event(s) with the highest attendees
try {
  $stmt = $pdo->prepare("
        SELECT e.id, e.title, COUNT(r.id) AS attendee_count
        FROM Events e
        LEFT JOIN registrations r ON e.id = r.event_id
        GROUP BY e.id
        HAVING attendee_count = (
            SELECT MAX(attendee_count) FROM (
                SELECT COUNT(r.id) AS attendee_count
                FROM Events e
                LEFT JOIN registrations r ON e.id = r.event_id
                GROUP BY e.id
            ) AS subquery
        )
    ");
  $stmt->execute();
  $top_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error fetching top events: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Managment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script
    src="https://kit.fontawesome.com/9291c1d06a.js"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="./styles/index.css">
</head>

<body>
  <header>
    <nav class="navbar navbar-expand-lg bg-white">
      <div class="container-fluid">
        <a class="navbar-brand text-danger fw-bolder me-5 ms-3" href="#">Event Management System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="index.php">Home</a>
            <a class="nav-link" href="index.php#events">Events</a>
            <a class="nav-link" href="index.php#contact">Contact Us</a>
            <?php if ($user_role): ?>
              <span class="navbar-text fw-bold me-3">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
              <?php if ($user_role == 'admin'):  ?>
                <a class="nav-link" href="adminDashboard.php">Admin Dashboard</a>
                <a class="nav-link" href="eventOrg.php">Create Event</a>
              <?php elseif ($user_role == 'organizer'): ?>
                <a class="nav-link" href="eventOrg.php">Create Event</a>
              <?php endif; ?>
              <a class="btn btn-outline-danger" href="backend/logout.php">Logout</a>
            <?php else: ?>
              <a class="btn btn-outline-danger" href="register&login.php">Login</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </nav>
    <nav>
      <div class="nav nav-tabs  mt-5 d-flex justify-content-center align-items-center ps-2 pe-2" id="nav-tab" role="tablist">
        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Users</button>
        <button button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Events</button>

      </div>
    </nav>
    <div class="tab-content " id="nav-tabContent">
      <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $index => $user): ?>
              <tr>
                <th scope="row"><?= $index + 1; ?></th>
                <td><?= htmlspecialchars($user['full_name']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td>
                  <form method="POST" action="backend/deleteUser.php" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
        <div class="container ">
      <p>Total Events: <?= $event_count; ?></p>
        <h3>Top Event(s) with Highest Attendees</h3>
        <ul>
          <?php foreach ($top_events as $event): ?>
            <li><?= htmlspecialchars($event['title']); ?> (Attendees: <?= $event['attendee_count']; ?>)</li>
          <?php endforeach; ?>
        </ul>
      </div>
      </div>

  
    </div>
  </header>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>
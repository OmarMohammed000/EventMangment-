<?php
session_start();

$user_role = $_SESSION['user_role'] ?? null; // Get user role from session
$user_name = $_SESSION['user_name'] ?? null; // Get user name from session
$user_id = $_SESSION['user_id'] ?? null ;

// Check if the user has the correct role
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'organizer'])) {
  header("Location: index.php"); // Redirect if not an admin or event organizer
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

// Fetch events
$user_role = $_SESSION['user_role'];
$organizer_id = $_SESSION['user_id'];

try {
  if ($user_role === 'admin') {
    // Admin sees all events
    $stmt = $pdo->prepare("SELECT * FROM events ORDER BY created_at DESC");
  } else {
    // Organizer sees only their events
    $stmt = $pdo->prepare("SELECT * FROM events WHERE organizer_id = :organizer_id ORDER BY created_at DESC");
    $stmt->bindParam(':organizer_id', $organizer_id, PDO::PARAM_INT);
  }
  $stmt->execute();
  $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error fetching events: " . $e->getMessage());
}

// Fetch attendees
try {
  if ($user_role === 'admin') {
    // Admin sees all attendees
    $stmt = $pdo->prepare("
      SELECT 
          r.id AS registration_id, 
          r.contact_details, 
          e.id AS event_id, 
          e.title AS event_title, 
          r.user_id 
      FROM 
          registrations r
      JOIN 
          events e ON r.event_id = e.id
      ORDER BY 
          r.registered_at DESC
    ");
  } else {
    // Organizer sees only attendees of their events
    $stmt = $pdo->prepare("
      SELECT 
          r.id AS registration_id, 
          r.contact_details, 
          e.id AS event_id, 
          e.title AS event_title, 
          r.user_id 
      FROM 
          registrations r
      JOIN 
          events e ON r.event_id = e.id
      WHERE 
          e.organizer_id = :organizer_id
      ORDER BY 
          r.registered_at DESC
    ");
    $stmt->bindParam(':organizer_id', $organizer_id, PDO::PARAM_INT);
  }
  $stmt->execute();
  $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error fetching attendees: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Event Managment</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous" />
  <script
    src="https://kit.fontawesome.com/9291c1d06a.js"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="./styles/index.css" />
</head>

<body>
  <header>
  <nav class="navbar navbar-expand-lg bg-white">
      <div class="container-fluid">
        <a class="navbar-brand text-danger fw-bolder me-5 ms-3" href="index.php">Event Management System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="index.php">Home</a>
            <a class="nav-link" href="index.php#events">Events</a>
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
    <ul
      class="nav nav-tabs mt-5 d-flex justify-content-center align-items-center ps-2 pe-2"
      id="myTab"
      role="tablist">
      <li class="nav-item" role="presentation">
        <button
          class="nav-link active"
          id="home-tab"
          data-bs-toggle="tab"
          data-bs-target="#home-tab-pane"
          type="button"
          role="tab"
          aria-controls="home-tab-pane"
          aria-selected="true">
          Create Event
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button
          class="nav-link"
          id="profile-tab"
          data-bs-toggle="tab"
          data-bs-target="#profile-tab-pane"
          type="button"
          role="tab"
          aria-controls="profile-tab-pane"
          aria-selected="false">
          View Created Events
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button
          class="nav-link"
          id="contact-tab"
          data-bs-toggle="tab"
          data-bs-target="#contact-tab-pane"
          type="button"
          role="tab"
          aria-controls="contact-tab-pane"
          aria-selected="false">
          View Attendees
        </button>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <div
        class="tab-pane fade show active"
        id="home-tab-pane"
        role="tabpanel"
        aria-labelledby="home-tab"
        tabindex="0">
        <div
          class="tab-pane fade show active"
          id="home-tab-pane"
          role="tabpanel"
          aria-labelledby="home-tab"
          tabindex="0">
          <h2 class="text-center text-bg-danger rounded-5 mb-3 p-1"></h2>
          <form
            action="backend/createEvent.php"
            method="post"
            style="height: 76vh">
            <div class="mb-3">
              <label for="title" class="form-label">Event Title</label>
              <input
                type="text"
                class="form-control"
                id="title"
                name="title"
                required />
            </div>
            <div class="mb-3">
              <label for="title" class="form-label">Event Time</label>
              <input
                type="datetime-local"
                class="form-control"
                id="time"
                name="time"
                required />
            </div>
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Event descreption</label>
              <textarea
                class="form-control"
                id="descreption"
                name="descreption"
                rows="3"></textarea>
            </div>
            <input type="submit" value="Create" class="btn btn-danger" />
          </form>
        </div>
      </div>
      <div
        class="tab-pane fade"
        id="profile-tab-pane"
        role="tabpanel"
        aria-labelledby="profile-tab"
        tabindex="0">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Event Title</th>
              <th scope="col">Event Description</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($events): ?>
              <?php foreach ($events as $index => $event): ?>
                <tr>
                  <th scope="row"><?php echo $index + 1; ?></th>
                  <td><?php echo htmlspecialchars($event['title']); ?></td>
                  <td><?php echo htmlspecialchars($event['description']); ?></td>
                  <td>
                    <!-- Delete Button -->
                    <form action="backend/deleteEvent.php" method="post" class="d-inline">
                      <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                      <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center">No events found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div
        class="tab-pane fade"
        id="contact-tab-pane"
        role="tabpanel"
        aria-labelledby="contact-tab"
        tabindex="0">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Event Title</th>
              <th scope="col">Attendee Email</th>
              <th scope="col">Send Notification</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($attendees)): ?>
              <?php foreach ($attendees as $index => $attendee): ?>
                <tr>
                  <td><?= htmlspecialchars($index + 1) ?></td>
                  <td><?= htmlspecialchars($attendee['event_title']) ?></td>
                  <td><?= htmlspecialchars($attendee['contact_details']) ?></td>
                  <td>
                    <form action="backend/sendNotification.php" method="POST">
                     
                      <input type="hidden" name="event_id" value="<?= htmlspecialchars($attendee['event_id']) ?>">
                      <input type="hidden" name="recipient_id" value="<?= htmlspecialchars($attendee['user_id']) ?>">
                      <div class="d-flex">
                        <input type="text" name="message" class="form-control me-2" placeholder="Enter notification" required>
                        <button type="submit" class="btn btn-primary">Send</button>
                      </div>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center">No attendees found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

      </div>

    </div>
  </header>
  <section class="tab-content mt-5"></section>
  <footer class="container-fluid text-center footer bg-body-secondary">
    <a href="index.html" class="btn">
      <h3 class="pt-2 fw-bold ">Event Management System</h3>
    </a>

    <div>
      <a
        href="https://www.linkedin.com/in/omar-mohammed-8a965a254/"
        target="_blank"><i class="fa-brands fa-linkedin fs-3 m-1"></i></a>
      <a href="mailto:omarmohammedelsayed00" target="_blank"><i class="fa-solid fa-envelope fs-3 m-1" style="color: #f70000"></i></a>
      <a href="https://github.com/OmarMohammed000" target="_blank"><i
          class="fa-brands fa-square-github fs-3 m-1"
          style="color: #000000"></i></a>
    </div>
    <div>
      <a href="index.php" class="btn ">Home</a>
      <a href="index.php#events" class="btn ">Events</a>
    

    </div>
    <div class="text-center ">
      &#169; Copy right 2024
    </div>
  </footer>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>
<?php
session_start();
$user_role = $_SESSION['user_role'] ?? null; // Get user role from session
$user_name = $_SESSION['user_name'] ?? null; // Get user name from session
$user_id = $_SESSION['user_id'] ?? null ;
?>
<?php
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
$notifications = [];
if ($user_id) {
  try {
    $stmt = $pdo->prepare("SELECT message FROM notifications WHERE recipient_id = :user_id ");
    $stmt->execute(['user_id' => $user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    die("Error fetching notifications: " . $e->getMessage());
  }
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
<?php if (!empty($notifications)): ?>
      <?php foreach ($notifications as $notification): ?>
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
          <strong>Notification:</strong> <?= htmlspecialchars($notification['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      
    <?php endif; ?>
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
  </header>
  <!-- Hero section -->
  <section>
    <div class="container-fluid hero d-flex align-items-center justify-content-center">
      <div class="row ms-2 me-2">
        <div class="col-4 ">
          <img src="./assets/card photo 1.jpg" alt="" class="img-fluid">
        </div>
        <div class="col-8">
          <h3> Hackathon for Sustainability</h3>
          <p class="text-danger"> 1 December 12 AM- 5 AM on AOU</p>
          <p>A Hackathon for Sustainability is an event where programmers, designers, and tech enthusiasts collaborate intensively over 24-48 hours to create innovative solutions addressing environmental and sustainability challenges. Participants form teams and work on projects like energy-efficient applications, waste management systems, or climate change awareness tools.</p>
        </div>
      </div>
    </div>
  </section>
  <?php
  // Fetch all events
  try {
    $stmt = $pdo->query("SELECT events.*, users.full_name AS organizer_name FROM events JOIN users ON events.organizer_id = users.id ORDER BY event_date DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "<p class='text-danger'>Error fetching events: " . $e->getMessage() . "</p>";
  }
  ?>

  <section>
    <div class="container">
      <div class="text-center mt-3 fw-bolder">
        <h2 class="text-danger" id="events">All Upcoming Events</h2>
        <hr>
      </div>

      <?php foreach ($events as $event): ?>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
            <h6 class="card-subtitle mb-2 text-body-secondary">
              <?php echo date('F j, Y, g:i A', strtotime($event['event_date'])); ?>
            </h6>
            <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
            <p class="card-text text-muted">Organized by: <?php echo htmlspecialchars($event['organizer_name']); ?></p>

            <form action="backend/userRegister.php" method="post" class="d-inline">
              <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
              <?php echo $_SESSION['user_email']; ?>
              <input type="hidden" name="user_email" value="<?php echo $user_email; ?>">
              <input type="submit" value="Register NOW" class="btn btn-danger fw-bold"></input>
            </form>
            <?php if ($_SESSION['user_role'] == '3' || $_SESSION['user_role'] == '2'): ?>
              <a href="backend/deleteEvent.php?id=<?php echo $event['id']; ?>" class="card-link btn btn-danger">Delete</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</body>

</html>
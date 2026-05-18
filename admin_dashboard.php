<?php
session_start();
require_once 'databaseConnection.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit;
}

// Handle Approve/Reject Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'], $_POST['action'])) {
  $event_id = $_POST['event_id'];
  $action = $_POST['action'];

  if (in_array($action, ['approved', 'rejected'])) {
    $stmt = $conn->prepare("UPDATE events SET event_status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $event_id);
    $stmt->execute();
    $stmt->close();
  }
}

// --- Stats Queries ---
$statsData = [];
$statsQueries = [
    'total_events'    => "SELECT COUNT(*) FROM events",
    'pending_events'  => "SELECT COUNT(*) FROM events WHERE event_status='pending'",
    'approved_events' => "SELECT COUNT(*) FROM events WHERE event_status='approved'",
    'total_users'     => "SELECT COUNT(*) FROM signup",
    'total_clients'   => "SELECT COUNT(*) FROM clients",
    'total_bookings'  => "SELECT COUNT(*) FROM event_bookings",
    'total_revenue'   => "SELECT COALESCE(SUM(total_amount),0) FROM event_bookings",
];
foreach ($statsQueries as $key => $sql) {
    $r = $conn->query($sql);
    $statsData[$key] = $r ? $r->fetch_row()[0] : 0;
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$today = date('Y-m-d');

$query = "SELECT * FROM events WHERE event_status = 'pending'";
if ($filter === 'upcoming') {
  $query .= " AND event_date >= '$today'";
} elseif ($filter === 'past') {
  $query .= " AND event_date < '$today'";
}
$query .= " ORDER BY event_date ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Pending Events</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #f6f9fc, #e0f7fa);
      background-attachment: fixed;
      color: #2c3e50;
    }

    header {
      background: linear-gradient(to right, #6a11cb, #2575fc);
      color: #fff;
      padding: 20px;
      text-align: center;
      font-size: 26px;
      font-weight: 700;
      letter-spacing: 1px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
      position: relative;
    }

    .logout {
      position: absolute;
      top: 20px;
      right: 30px;
      background: #ff4b2b;
      color: white;
      padding: 10px 18px;
      border: none;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .logout:hover {
      background: #ff3b1f;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 220px;
      height: 100%;
      background: linear-gradient(180deg, #2f3542, #1e272e);
      padding-top: 80px;
      color: #fff;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar a {
      display: block;
      padding: 15px 25px;
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      border-left: 4px solid transparent;
      transition: 0.3s;
    }

    .sidebar a:hover {
      background-color: #3742fa;
      border-left: 4px solid #70a1ff;
      color: #fff;
    }

    .main {
      margin-left: 240px;
      padding: 30px;
      animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .filter {
      margin-bottom: 25px;
    }

    .filter a {
      margin-right: 15px;
      text-decoration: none;
      color: #0984e3;
      font-weight: bold;
      padding: 6px 14px;
      border-radius: 20px;
      background-color: #dfe6e9;
      transition: 0.3s ease-in-out;
    }

    .filter a:hover {
      background-color: #74b9ff;
      color: white;
    }

    .event-card {
      background: white;
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07);
      margin-bottom: 30px;
      transition: 0.3s ease;
      border: 2px solid transparent;
    }

    .event-card:hover {
      transform: scale(1.015);
      border-color: #81ecec;
    }

    .event-card h3 {
      color: #0984e3;
      margin-top: 0;
    }

    .event-card p {
      margin: 8px 0;
      line-height: 1.6;
    }

    .event-card img {
      border-radius: 10px;
      max-width: 200px;
      border: 1px solid #dcdde1;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .actions {
      margin-top: 20px;
    }

    .actions button {
      padding: 12px 20px;
      border: none;
      border-radius: 25px;
      font-weight: 600;
      margin-right: 10px;
      cursor: pointer;
      transition: 0.3s;
      box-shadow: 0 5px 14px rgba(0, 0, 0, 0.12);
    }

    .approve-btn {
      background: linear-gradient(135deg, #00b894, #55efc4);
      color: #fff;
    }

    .approve-btn:hover {
      background: linear-gradient(135deg, #00cec9, #81ecec);
    }

    .reject-btn {
      background: linear-gradient(135deg, #d63031, #ff7675);
      color: #fff;
    }

    .reject-btn:hover {
      background: linear-gradient(135deg, #e17055, #fab1a0);
    }

    .no-events {
      text-align: center;
      color: #636e72;
      font-size: 20px;
      margin-top: 50px;
      font-weight: 600;
    }

    /* Stats Cards */
    .stats-row {
      display: flex;
      flex-wrap: wrap;
      gap: 18px;
      margin-bottom: 32px;
    }
    .stat-card {
      background: white;
      border-radius: 14px;
      padding: 20px 28px;
      text-align: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.07);
      flex: 1 1 130px;
      min-width: 120px;
      border-top: 4px solid transparent;
      transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card.blue  { border-color: #3742fa; }
    .stat-card.green { border-color: #00b894; }
    .stat-card.red   { border-color: #d63031; }
    .stat-card.orange{ border-color: #fdcb6e; }
    .stat-card.purple{ border-color: #a29bfe; }
    .stat-card.teal  { border-color: #00cec9; }
    .stat-card.pink  { border-color: #fd79a8; }
    .stat-card .s-num { font-size: 2rem; font-weight: 900; color: #2d3436; }
    .stat-card .s-lbl { color: #636e72; font-size: 0.82rem; margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
  </style>
  <script>
    function confirmAction(eventId, actionType) {
      if (confirm(`Are you sure you want to ${actionType} this event?`)) {
        const form = document.getElementById(`${actionType}-form-${eventId}`);
        form.submit();
      }
    }
  </script>
</head>

<body>
  <header>
    Admin Dashboard - Pending Events
    <form action="admin_logout.php" method="POST" style="display:inline;">
      <button type="submit" class="logout">Logout</button>
    </form>
  </header>

  <div class="sidebar">
    <a href="?filter=all">All Pending</a>
    <a href="?filter=upcoming">Upcoming Events</a>
    <a href="?filter=past">Past Events</a>
  </div>

  <div class="main">
    <!-- Stats Dashboard -->
    <div class="stats-row">
      <div class="stat-card blue">
        <div class="s-num"><?= $statsData['total_events'] ?></div>
        <div class="s-lbl">Total Events</div>
      </div>
      <div class="stat-card orange">
        <div class="s-num"><?= $statsData['pending_events'] ?></div>
        <div class="s-lbl">Pending</div>
      </div>
      <div class="stat-card green">
        <div class="s-num"><?= $statsData['approved_events'] ?></div>
        <div class="s-lbl">Approved</div>
      </div>
      <div class="stat-card purple">
        <div class="s-num"><?= $statsData['total_users'] ?></div>
        <div class="s-lbl">Users</div>
      </div>
      <div class="stat-card teal">
        <div class="s-num"><?= $statsData['total_clients'] ?></div>
        <div class="s-lbl">Organizers</div>
      </div>
      <div class="stat-card pink">
        <div class="s-num"><?= $statsData['total_bookings'] ?></div>
        <div class="s-lbl">Bookings</div>
      </div>
      <div class="stat-card red">
        <div class="s-num">&#8377;<?= number_format($statsData['total_revenue'], 0) ?></div>
        <div class="s-lbl">Revenue</div>
      </div>
    </div>

    <h2><?= ucfirst($filter) ?> Pending Events</h2>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="event-card">
          <h3><?= htmlspecialchars($row['event_name']) ?></h3>
          <p><strong>Date:</strong> <?= $row['event_date'] ?> | <strong>Time:</strong> <?= $row['start_time'] ?> -
            <?= $row['end_time'] ?>
          </p>
          <p><strong>Venue:</strong> <?= htmlspecialchars($row['venue']) ?></p>
          <p><strong>Speaker:</strong> <?= htmlspecialchars($row['speaker']) ?></p>
          <p><strong>Description:</strong> <?= htmlspecialchars($row['event_description']) ?></p>
          <p><strong>Category:</strong> <?= htmlspecialchars($row['event_category']) ?></p>
          <p><strong>Poster:</strong><br>
            <img src="../<?= $row['event_poster'] ?>" alt="Event Poster">
          </p>

          <div class="actions">
            <form id="approved-form-<?= $row['id'] ?>" method="POST" style="display:inline;">
              <input type="hidden" name="event_id" value="<?= $row['id'] ?>">
              <input type="hidden" name="action" value="approved">
              <button type="button" class="approve-btn"
                onclick="confirmAction(<?= $row['id'] ?>, 'approved')">Approve</button>
            </form>

            <form id="rejected-form-<?= $row['id'] ?>" method="POST" style="display:inline;">
              <input type="hidden" name="event_id" value="<?= $row['id'] ?>">
              <input type="hidden" name="action" value="rejected">
              <button type="button" class="reject-btn"
                onclick="confirmAction(<?= $row['id'] ?>, 'rejected')">Reject</button>
            </form>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-events">🎉 No pending events to review at the moment.</p>
    <?php endif; ?>
  </div>
</body>

</html>
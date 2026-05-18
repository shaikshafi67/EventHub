<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}
require_once 'databaseConnection.php';

// Look up the logged-in user's email from the signup table
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT email, full_name FROM signup WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$userResult = $stmt->get_result();
$userRow = $userResult->fetch_assoc();
$stmt->close();

$userEmail = $userRow['email'] ?? '';
$userFullName = $userRow['full_name'] ?? $username;

// Fetch all bookings for this user by email
$bookings = [];
if ($userEmail) {
    $stmt = $conn->prepare("
        SELECT eb.id AS booking_id, eb.tickets, eb.total_amount, eb.transaction_id,
               eb.booking_date, eb.payment_proof,
               e.event_name, e.event_date, e.venue, e.event_category, e.event_poster, e.event_price
        FROM event_bookings eb
        JOIN events e ON eb.event_id = e.id
        WHERE eb.email = ?
        ORDER BY eb.booking_date DESC
    ");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Bookings - EventHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #3a86ff;
      --secondary: #ff006e;
      --gradient: linear-gradient(45deg, #3a86ff, #ff006e);
    }
    body { background: #f0f4ff; font-family: 'Segoe UI', sans-serif; }
    header {
      background: var(--gradient);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(58,134,255,0.3);
    }
    header .logo { font-size: 1.6rem; font-weight: 900; letter-spacing: 2px; text-decoration: none; color: white; }
    header .nav-links a { color: white; text-decoration: none; margin-left: 1.5rem; font-weight: 600; }
    .page-hero {
      background: var(--gradient);
      color: white;
      text-align: center;
      padding: 3rem 1rem;
      margin-bottom: 2rem;
    }
    .page-hero h1 { font-size: 2.2rem; font-weight: 900; margin-bottom: 0.5rem; }
    .page-hero p { opacity: 0.85; font-size: 1.05rem; }
    .stats-bar {
      display: flex; gap: 1.5rem; flex-wrap: wrap; justify-content: center;
      margin-bottom: 2rem;
    }
    .stat-card {
      background: white; border-radius: 12px; padding: 1rem 2rem;
      text-align: center; box-shadow: 0 4px 15px rgba(58,134,255,0.1);
      min-width: 140px;
    }
    .stat-card .number { font-size: 1.8rem; font-weight: 900; color: var(--primary); }
    .stat-card .label { color: #666; font-size: 0.9rem; margin-top: 2px; }
    .booking-card {
      background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      margin-bottom: 1.5rem; overflow: hidden; display: flex; flex-wrap: wrap;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .booking-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(58,134,255,0.15); }
    .booking-poster {
      width: 160px; min-height: 140px; background: #ddd; flex-shrink: 0;
      overflow: hidden; display: flex; align-items: center; justify-content: center;
    }
    .booking-poster img { width: 100%; height: 100%; object-fit: cover; }
    .booking-poster .no-img { font-size: 3rem; color: #bbb; }
    .booking-body { flex: 1; padding: 1.2rem 1.5rem; }
    .booking-body h5 { font-weight: 800; font-size: 1.1rem; color: #001219; margin-bottom: 0.3rem; }
    .meta-row { display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 0.5rem; }
    .meta-item { font-size: 0.88rem; color: #555; }
    .meta-item strong { color: #333; }
    .badge-cat {
      display: inline-block; background: var(--primary); color: white;
      font-size: 11px; padding: 2px 10px; border-radius: 999px; margin-bottom: 0.4rem;
    }
    .booking-footer {
      display: flex; align-items: center; justify-content: space-between;
      padding: 0.8rem 1.5rem; border-top: 1px solid #f0f0f0;
      flex-wrap: wrap; gap: 0.5rem; background: #fafbff;
    }
    .amount-pill {
      background: linear-gradient(90deg, #ff006e22, #3a86ff22);
      border: 1px solid #3a86ff44;
      border-radius: 30px; padding: 5px 18px; font-weight: 700; color: var(--primary);
    }
    .btn-view-ticket {
      background: var(--gradient); color: white; border: none;
      border-radius: 30px; padding: 7px 22px; font-weight: 700;
      text-decoration: none; font-size: 0.9rem; transition: opacity 0.2s;
    }
    .btn-view-ticket:hover { opacity: 0.85; color: white; }
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state .icon { font-size: 5rem; margin-bottom: 1rem; }
    .empty-state h3 { color: #555; font-weight: 700; }
    .empty-state p { color: #999; }
    @media (max-width: 600px) {
      .booking-poster { width: 100%; height: 160px; }
    }
  </style>
</head>
<body>

<header>
  <a href="main.php" class="logo">EventHub</a>
  <div class="nav-links">
    <a href="main.php">&#8592; Back to Events</a>
    <a href="logout.php">Logout</a>
  </div>
</header>

<div class="page-hero">
  <h1>My Bookings</h1>
  <p>Welcome back, <strong><?= htmlspecialchars($userFullName) ?></strong> &mdash; here are all your event bookings.</p>
</div>

<div class="container pb-5">
  <?php
    $totalTickets = array_sum(array_column($bookings, 'tickets'));
    $totalSpent   = array_sum(array_column($bookings, 'total_amount'));
  ?>
  <!-- Stats Bar -->
  <div class="stats-bar">
    <div class="stat-card">
      <div class="number"><?= count($bookings) ?></div>
      <div class="label">Total Bookings</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $totalTickets ?></div>
      <div class="label">Tickets Purchased</div>
    </div>
    <div class="stat-card">
      <div class="number">&#8377;<?= number_format($totalSpent, 2) ?></div>
      <div class="label">Amount Spent</div>
    </div>
  </div>

  <?php if (empty($bookings)): ?>
    <div class="empty-state">
      <div class="icon">🎟️</div>
      <h3>No bookings yet!</h3>
      <p>You haven't booked any events. Start exploring events to book your first ticket.</p>
      <a href="main.php" class="btn-view-ticket mt-3" style="display:inline-block;">Explore Events</a>
    </div>
  <?php else: ?>
    <?php foreach ($bookings as $b): ?>
      <div class="booking-card">
        <div class="booking-poster">
          <?php if (!empty($b['event_poster']) && file_exists($b['event_poster'])): ?>
            <img src="<?= htmlspecialchars($b['event_poster']) ?>" alt="Event Poster">
          <?php else: ?>
            <span class="no-img">🎉</span>
          <?php endif; ?>
        </div>
        <div style="flex:1; display:flex; flex-direction:column; justify-content:space-between;">
          <div class="booking-body">
            <span class="badge-cat"><?= htmlspecialchars($b['event_category']) ?></span>
            <h5><?= htmlspecialchars($b['event_name']) ?></h5>
            <div class="meta-row">
              <span class="meta-item">📅 <strong><?= date("M j, Y", strtotime($b['event_date'])) ?></strong></span>
              <span class="meta-item">📍 <strong><?= htmlspecialchars($b['venue']) ?></strong></span>
              <span class="meta-item">🎟️ <strong><?= $b['tickets'] ?></strong> ticket<?= $b['tickets'] > 1 ? 's' : '' ?></span>
              <span class="meta-item">🆔 Booking #<strong><?= $b['booking_id'] ?></strong></span>
              <span class="meta-item">💳 TXN: <strong><?= htmlspecialchars($b['transaction_id']) ?></strong></span>
              <span class="meta-item">🕐 Booked: <strong><?= date("M j, Y g:i A", strtotime($b['booking_date'])) ?></strong></span>
            </div>
          </div>
          <div class="booking-footer">
            <span class="amount-pill">
              <?= $b['total_amount'] > 0 ? '&#8377;' . number_format($b['total_amount'], 2) : 'Free' ?>
            </span>
            <a href="ticket.php?booking_id=<?= $b['booking_id'] ?>" class="btn-view-ticket">View Ticket &rarr;</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>

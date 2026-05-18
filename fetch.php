<?php
// databaseConnection.php already includes $conn
require_once 'databaseConnection.php';

// Fetch approved events
$sql = "SELECT id, event_name, venue, event_date, start_time, end_time, event_description, event_poster, event_price, event_category 
        FROM events 
        WHERE event_status = 'approved'
        ORDER BY event_date ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
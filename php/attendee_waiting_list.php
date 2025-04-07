<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in']) {
    echo "<script>alert('Please log in.'); window.location.href='organiser_login.php';</script>";
    exit();
}

$attendee_id = $_SESSION['attendee_id'];

$query = "SELECT w.*, e.event_name, e.event_date 
          FROM waiting_list w
          JOIN event e ON w.event_id = e.event_id
          WHERE w.attendee_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $attendee_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Waiting List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #001f3f; color: white; padding: 30px; }
        .card { background: #112; padding: 20px; margin-bottom: 15px; border-radius: 10px; }
        h2 { color: gold; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">📋 Your Waiting List</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <h4><?= htmlspecialchars($row['event_name']) ?></h4>
                    <p>Date: <?= $row['event_date'] ?></p>
                    <p>Joined Waiting List On: <?= $row['request_time'] ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't joined any waiting lists yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>

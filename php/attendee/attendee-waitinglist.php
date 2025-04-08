<?php
session_start();
include '../../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    echo "<script>alert('Please log in.'); window.location.href='../organiser_login.php';</script>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/attendee/attendee-sidebar.css">
    <link rel="stylesheet" href="../../css/attendee/attendee-waitinglist.css">

</head>

<body>

    <div class="sidebar">
        <h1>Attendee Panel</h1>
        <ul>
            <li><a href="attendee-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="choose-event.php"><i class="fas fa-calendar-alt"></i> Choose Event</a></li>
            <li><a href="waiting-list.php"><i class="fas fa-clock"></i> Join Waiting List</a></li>
            <li><a href="attendee-waitinglist.php" class="active"><i class="fas fa-bell"></i> View Waiting List</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>📋 Your Waiting List</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="event-title">🎤 <?= htmlspecialchars($row['event_name']) ?></div>
                    <span class="badge badge-date">📅 <?= date("F j, Y", strtotime($row['event_date'])) ?></span>
                    <span class="badge badge-time">⏱ Joined: <?= date("g:i A, M d", strtotime($row['request_time'])) ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty">
                <h4>😥 You're not on any waiting lists</h4>
                <p>Browse events and join a waiting list when your favourite concert is full.</p>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <a href="attendee-dashboard.php" class="btn btn-back mt-4">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>

</html>
<?php
session_start();
include '../../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    header("Location: ../organiser_login.php");
    exit();
}

// Fetch all active events created by organisers
$query = "SELECT event_id, event_name, event_date, event_time, event_description, file_name FROM event ORDER BY event_date ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/attendee/attendee-sidebar.css">
    <link rel="stylesheet" href="../../css/attendee/choose-event.css">
</head>

<body>
    <div class="sidebar">
        <h1>Attendee Panel</h1>
        <ul>
            <li><a href="attendee-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="choose-event.php" class="active"><i class="fas fa-calendar-alt"></i> Choose Event</a></li>
            <li><a href="waiting-list.php"><i class="fas fa-clock"></i> Join Waiting List</a></li>
            <li><a href="attendee-waitinglist.php"><i class="fas fa-bell"></i> View Waiting List</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2 class="text-center mb-4" style="color: gold;">Available Events</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($event = $result->fetch_assoc()): ?>
                <div class="event-card">
                    <img src="../../img/<?= htmlspecialchars($event['file_name']) ?>" alt="Event Cover">
                    <div class="event-details">
                        <h4><?= htmlspecialchars($event['event_name']) ?></h4>
                        <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?> @
                            <?= htmlspecialchars($event['event_time']) ?>
                        </p>
                        <p><?= htmlspecialchars($event['event_description']) ?></p>
                        <form action="select-seat.php" method="GET">
                            <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                            <button type="submit" class="btn btn-choose">Select Seats</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-warning text-center">No events available at the moment. Please check back later.</div>
        <?php endif; ?>
    </div>
</body>

</html>
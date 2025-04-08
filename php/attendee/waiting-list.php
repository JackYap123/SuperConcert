<?php
session_start();
include '../../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    echo "<script>alert('Please log in first.'); window.location.href = '../organiser_login.php';</script>";
    exit();
}

$attendee_id = $_SESSION['attendee_id'];

// Fetch all events
$eventQuery = "SELECT e.event_id, e.event_name, e.event_date, COUNT(s.seat_number) AS total_seats,
               (SELECT COUNT(*) FROM bookings b WHERE b.event_id = e.event_id) AS booked_seats
               FROM event e
               LEFT JOIN event_seats s ON e.event_id = s.event_id
               GROUP BY e.event_id";

$result = $conn->query($eventQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Join Waiting List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/attendee/attendee-sidebar.css">
    <link rel="stylesheet" href="../../css/attendee/waiting-list.css">
</head>

<body>

    <div class="sidebar">
        <h1>Attendee Panel</h1>
        <ul>
            <li><a href="attendee-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="choose-event.php"><i class="fas fa-calendar-alt"></i> Choose Event</a></li>
            <li><a href="waiting-list.php" class="active"><i class="fas fa-clock"></i> Join Waiting List</a></li>
            <li><a href="attendee-waitinglist.php"><i class="fas fa-bell"></i> View Waiting List</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container mt-4">
        <h2 class="text-center text-warning">🎫 Waiting List for Full Events</h2>

        <?php while ($event = $result->fetch_assoc()):
            $isFull = $event['booked_seats'] >= $event['total_seats'];
            ?>
            <div class="card">
                <h4><?= htmlspecialchars($event['event_name']) ?> (<?= $event['event_date'] ?>)</h4>
                <p>Total Seats: <?= $event['total_seats'] ?> | Booked: <?= $event['booked_seats'] ?></p>
                <p>Status:
                    <span class="status <?= $isFull ? 'full' : 'available' ?>">
                        <?= $isFull ? 'Full' : 'Seats Available' ?>
                    </span>
                </p>
                <?php if ($isFull): ?>
                    <a href="join-waitinglist.php?event_id=<?= $event['event_id'] ?>" class="btn btn-warning">Join Waiting
                        List</a>
                <?php else: ?>
                    <a href="select-seat.php?event_id=<?= $event['event_id'] ?>" class="btn btn-suc">Book Now</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>

</html>
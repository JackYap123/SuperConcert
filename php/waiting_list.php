<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in']) {
    echo "<script>alert('Please log in first.'); window.location.href = 'organiser_login.php';</script>";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #001f3f;
            color: white;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .card {
            background-color: #112;
            margin-bottom: 20px;
            border-radius: 10px;
            padding: 20px;
            color: white;
        }

        .btn {
            border-radius: 6px;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .full {
            background-color: crimson;
        }

        .available {
            background-color: green;
        }
    </style>
</head>
<body>
    <h2 class="text-center text-warning">🎫 Waiting List for Full Events</h2>
    <div class="container mt-4">
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
                    <a href="join_waiting_list.php?event_id=<?= $event['event_id'] ?>" class="btn btn-warning">Join Waiting List</a>
                <?php else: ?>
                    <a href="select_seat.php?event_id=<?= $event['event_id'] ?>" class="btn btn-success">Book Now</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

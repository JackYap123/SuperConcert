<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            background-color: #001f3f;
            color: #fff;
        }

        .sidebar {
            width: 250px;
            background-color: #222;
            height: 100vh;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }



        .sidebar h1 {
            font-size: 20px;
            color: gold;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: #ccc;
            text-decoration: none;
            font-size: 16px;
        }

        .sidebar ul li a:hover {
            color: #fff;
        }

        .sidebar .logout a {
            margin-top: 50px;
            display: block;
            color: red;
            text-decoration: none;
            font-weight: bold;
        }


        .container {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
            /* 👈 Prevent overlap */
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

    <div class="sidebar">
        <h1>Attendee Panel</h1>
        <ul>
            <li><a href="attendee_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="choose_event.php"><i class="fas fa-calendar-alt"></i> Choose Event</a></li>
            <li><a href="waiting_list.php"><i class="fas fa-clock"></i> Join Waiting List</a></li>
            <li><a href="attendee_waiting_list.php"><i class="fas fa-bell"></i> View Waiting List</a></li>
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
                    <a href="join_waiting_list.php?event_id=<?= $event['event_id'] ?>" class="btn btn-warning">Join Waiting
                        List</a>
                <?php else: ?>
                    <a href="select_seat.php?event_id=<?= $event['event_id'] ?>" class="btn btn-success">Book Now</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>

</html>
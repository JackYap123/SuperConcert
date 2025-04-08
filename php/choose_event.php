<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    header("Location: organiser_login.php");
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

        .event-card {
            background-color: #333;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .event-card img {
            width: 180px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }

        .event-details h4 {
            color: gold;
        }

        .btn-choose {
            background-color: #4CAF50;
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-choose:hover {
            background-color: #45a049;
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
    <div class="container">
        <h2 class="text-center mb-4" style="color: gold;">Available Events</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($event = $result->fetch_assoc()): ?>
                <div class="event-card">
                    <img src="../img/<?= htmlspecialchars($event['file_name']) ?>" alt="Event Cover">
                    <div class="event-details">
                        <h4><?= htmlspecialchars($event['event_name']) ?></h4>
                        <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?> @
                            <?= htmlspecialchars($event['event_time']) ?>
                        </p>
                        <p><?= htmlspecialchars($event['event_description']) ?></p>
                        <form action="select_seat.php" method="GET">
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
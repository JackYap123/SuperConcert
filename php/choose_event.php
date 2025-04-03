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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #001f3f;
            color: white;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
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
    <div class="container">
        <h2 class="text-center mb-4" style="color: gold;">Available Events</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($event = $result->fetch_assoc()): ?>
                <div class="event-card">
                    <img src="../img/<?= htmlspecialchars($event['file_name']) ?>" alt="Event Cover">
                    <div class="event-details">
                        <h4><?= htmlspecialchars($event['event_name']) ?></h4>
                        <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?> @
                            <?= htmlspecialchars($event['event_time']) ?></p>
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
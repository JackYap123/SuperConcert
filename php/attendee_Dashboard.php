<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    header("Location: organiser_login.php");
    exit();
}

$attendee_id = $_SESSION['attendee_id'];
$query = "SELECT b.*, e.event_date FROM bookings b JOIN event e ON b.event_id = e.event_id WHERE b.attendee_id = ? AND b.status = 'active' ORDER BY b.booking_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $attendee_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Attendee Dashboard</title>
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
        }

        header h1 {
            font-size: 32px;
            color: gold;
            text-align: center;
            margin-bottom: 20px;
        }

        .promo-bar {
            background-color: #ffc107;
            color: black;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 30px;
        }

        .nav-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .nav-buttons .btn {
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .nav-buttons .btn:hover {
            background-color: #45a049;
        }

        table {
            background-color: #112;
        }

        .table th {
            background-color: #007bff;
            color: white;
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
        <header>
            <h1>Welcome to Your Event Dashboard</h1>
        </header>

        <div class="promo-bar">
            <marquee behavior="scroll" direction="left">
                ✨ Use code <strong>EVENT50</strong> for 50% off your next ticket! ✨
            </marquee>
        </div>

        <div class="nav-buttons">
            <a href="choose_event.php" class="btn">🎟️ Choose Event</a>
            <a href="waiting_list.php" class="btn">⏳ Join Waiting List</a>
            <a href="waiting_list.php" class="btn">⏳ Check Waiting List</a>
        </div>

        <div class="mt-5">
            <h3>Your Active Bookings</h3>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Event ID</th>
                        <th>Seat</th>
                        <th>Price (RM)</th>
                        <th>Booking Date</th>
                        <th>Event Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $today = date('Y-m-d');
                    if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center text-warning">No active bookings found.</td>
                        </tr>
                    <?php else:
                        while ($row = $result->fetch_assoc()):
                            $cancelAllowed = ($row['event_date'] >= $today);
                            ?>
                            <tr>
                                <td><?= $row['event_id'] ?></td>
                                <td><?= $row['seat_number'] ?></td>
                                <td><?= number_format($row['price'], 2) ?></td>
                                <td><?= date('Y-m-d', strtotime($row['booking_time'])) ?></td>
                                <td><?= $row['event_date'] ?></td>
                                <td>
                                    <?php if ($cancelAllowed): ?>
                                        <form method="POST" action="cancel_booking.php"
                                            onsubmit="return confirm('Cancel this seat?');">
                                            <input type="hidden" name="event_id" value="<?= $row['event_id'] ?>">
                                            <input type="hidden" name="seat_number" value="<?= $row['seat_number'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Cancellation closed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
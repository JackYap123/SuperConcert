<?php
session_start();
include '../../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    header("Location: ../organiser_login.php");
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
    <link rel="stylesheet" href="../../css/attendee/attendee-dashboard.css">
    <link rel="stylesheet" href="../../css/attendee/attendee-sidebar.css">
</head>

<body>
    <div class="sidebar">
        <h1>Attendee Panel</h1>
        <ul>
            <li><a href="attendee-dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="choose-event.php"><i class="fas fa-calendar-alt"></i> Choose Event</a></li>
            <li><a href="waiting-list.php"><i class="fas fa-clock"></i> Join Waiting List</a></li>
            <li><a href="attendee-waitinglist.php"><i class="fas fa-bell"></i> View Waiting List</a></li>
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
            <a href="choose-event.php" class="btn">🎟️ Choose Event</a>
            <a href="waiting-list.php" class="btn">⏳ Join Waiting List</a>
            <a href="attendee-waitinglist.php" class="btn">⏳ Check Waiting List</a>
        </div>

        <div class="mt-5">
            <h3 class="text-warning mb-4">🧾 Your Ticket Receipts</h3>

            <?php
            $today = date('Y-m-d');
            if ($result->num_rows === 0): ?>
                <div class="alert alert-info">You have no active bookings at the moment.</div>
            <?php else:
                // Group bookings by event
                $grouped = [];
                while ($row = $result->fetch_assoc())
                {
                    $grouped[$row['event_id']]['event_date'] = $row['event_date'];
                    $grouped[$row['event_id']]['seats'][] = $row;
                }

                foreach ($grouped as $event_id => $data):
                    $seats = $data['seats'];
                    $event_date = $data['event_date'];
                    $total_price = array_sum(array_column($seats, 'price'));
                    ?>
                    <div class="card mb-4" style="background-color: #112; border-left: 5px solid #ffc107; color: white;">
                        <div class="card-body">
                            <h5 class="card-title text-info">🎫 Event #<?= $event_id ?></h5>
                            <p><strong>Event Date:</strong> <?= date('F j, Y', strtotime($event_date)) ?></p>
                            <p><strong>Total Paid:</strong> RM <?= number_format($total_price, 2) ?></p>
                            <hr>

                            <div class="row">
                                <?php foreach ($seats as $seat):
                                    $cancelAllowed = ($seat['event_date'] >= $today);
                                    ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 border rounded" style="background-color: #223;">
                                            <p><strong>Seat:</strong> <?= $seat['seat_number'] ?></p>
                                            <p><strong>Price:</strong> RM <?= number_format($seat['price'], 2) ?></p>
                                            <p><strong>Booked On:</strong> <?= date('F j, Y', strtotime($seat['booking_time'])) ?>
                                            </p>
                                            <?php if ($cancelAllowed): ?>
                                                <form method="POST" action="cancel-booking.php"
                                                    onsubmit="return confirm('Cancel this seat?');">
                                                    <input type="hidden" name="event_id" value="<?= $seat['event_id'] ?>">
                                                    <input type="hidden" name="seat_number" value="<?= $seat['seat_number'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Cancellation closed</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
        </div>

    </div>
</body>

</html>
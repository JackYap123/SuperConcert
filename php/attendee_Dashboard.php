<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendee Home</title>
    <link rel="stylesheet" href="../css/attendee_Dashboard.css">
</head>

<body>
    <div class="sidebar">
        <h1>Admin Dashboard</h1>
        <ul>
            <li><a href=""><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href=""><i class="fas fa-users"></i> Choose Event</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Waiting List</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Payment</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <header>
            <h1>Attendee Home</h1>
        </header>

        <div class="promo-bar">
            <marquee behavior="scroll" direction="left">✨ Use code <strong>EVENT50</strong> for 50% off your next
                ticket! ✨</marquee>
        </div>

        <div class="nav-buttons">
            <a href="choose_event.php" class="btn">Choose Event</a>
            <a href="waiting_list.php" class="btn">Waiting List</a>
            <a href="payment.php" class="btn">Payment</a>
        </div>
    </div>
</body>

</html>
<?php
session_start();
if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    header("Location: organiser_login.php");
    exit();
}

$attendee_id = $_SESSION['attendee_id'];
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
    </style>
</head>

<body>
    <div class="sidebar">
        <h1>Attendee Panel</h1>
        <ul>
            <li><a href="attendee_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="choose_event.php"><i class="fas fa-calendar-alt"></i> Choose Event</a></li>
            <li><a href="waiting_list.php"><i class="fas fa-clock"></i> Waiting List</a></li>
            <li><a href="payment.php"><i class="fas fa-credit-card"></i> Payment</a></li>
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
            <a href="waiting_list.php" class="btn">⏳ Waiting List</a>
            <a href="payment.php" class="btn">💳 Payment</a>
        </div>
    </div>
</body>

</html>
<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    echo "<script>alert('Please log in.'); window.location.href='organiser_login.php';</script>";
    exit();
}

$attendee_id = $_SESSION['attendee_id'];

$query = "SELECT w.*, e.event_name, e.event_date 
          FROM waiting_list w
          JOIN event e ON w.event_id = e.event_id
          WHERE w.attendee_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $attendee_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Waiting List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        h2 {
            color: gold;
            text-align: center;
            margin-bottom: 40px;
            font-weight: bold;
        }

        .card {
            background: rgba(17, 17, 34, 0.95);
            border: none;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: scale(1.015);
        }

        .event-title {
            color: #00d9ff;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .badge-date,
        .badge-time {
            display: inline-block;
            margin-top: 10px;
            margin-right: 10px;
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 20px;
        }

        .badge-date {
            background-color: #ffc107;
            color: black;
        }

        .badge-time {
            background-color: #17a2b8;
        }

        .empty {
            background-color: rgba(255, 255, 255, 0.05);
            padding: 50px;
            text-align: center;
            border: 2px dashed #777;
            border-radius: 12px;
            margin-top: 40px;
        }

        .btn-back {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 8px;
            margin-top: 30px;
            transition: background 0.3s ease;
        }

        .btn-back:hover {
            background-color: #1f8135;
        }

        .icon {
            margin-right: 10px;
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
        <h2>📋 Your Waiting List</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="event-title">🎤 <?= htmlspecialchars($row['event_name']) ?></div>
                    <span class="badge badge-date">📅 <?= date("F j, Y", strtotime($row['event_date'])) ?></span>
                    <span class="badge badge-time">⏱ Joined: <?= date("g:i A, M d", strtotime($row['request_time'])) ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty">
                <h4>😥 You're not on any waiting lists</h4>
                <p>Browse events and join a waiting list when your favourite concert is full.</p>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <a href="attendee_dashboard.php" class="btn btn-back mt-4">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>

</html>
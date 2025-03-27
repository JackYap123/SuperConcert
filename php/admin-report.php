<?php
session_start();
require_once('../inc/config.php');

// Get the selected filter from the form (default: monthly)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'monthly';

// Set the end date as today
$end_date = date('Y-m-d');

// Set the start date dynamically based on filter selection
if ($filter == "daily")
{
    $start_date = $end_date; // Today's data
}
elseif ($filter == "weekly")
{
    $start_date = date('Y-m-d', strtotime('-6 days')); // Include the last 7 days
}
else
{ // Monthly (default)
    $start_date = date('Y-m-d', strtotime('-29 days')); // Include the last 30 days
}

// Fetch report data based on the selected date range
$query = "
    SELECT COUNT(*) AS total_bookings, 
           COUNT(DISTINCT event_id) AS total_events,
           (COALESCE(SUM(occupied_seats), 0) / COALESCE(SUM(total_seats), 1)) * 100 AS utilization_percentage
    FROM bookings 
    WHERE booking_date BETWEEN ? AND ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$total_bookings = $data['total_bookings'] ?? 0;
$total_events = $data['total_events'] ?? 0;
$utilization_percentage = number_format($data['utilization_percentage'], 2) ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin-report.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="../php/admin_Dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../php/Register_Organizer.php"><i class="fas fa-user-plus"></i> Create Organizer</a></li>
            <li><a href="../php/admin-report.php" class="active"><i class="fas fa-chart-bar"></i> Generate Report</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Auditorium Report</h1>

        <form method="GET" class="filter-container">
            <label for="dateFilter">Select Time Range:</label>
            <select name="filter" id="dateFilter" onchange="this.form.submit();">
                <option value="daily" <?= $filter == "daily" ? "selected" : "" ?>>Daily</option>
                <option value="weekly" <?= $filter == "weekly" ? "selected" : "" ?>>Weekly</option>
                <option value="monthly" <?= $filter == "monthly" ? "selected" : "" ?>>Monthly</option>
            </select>
        </form>
        <div class="report-card">
            <div id="reportSection">
                <h2>Report Summary (<?= ucfirst($filter) ?> Report)</h2>
                <p>Showing data from <b><?= $start_date ?></b> to <b><?= $end_date ?></b></p>
                <table>
                    <thead>
                        <tr>
                            <th>Total Bookings</th>
                            <th>Total Events Hosted</th>
                            <th>Utilization (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $total_bookings ?></td>
                            <td><?= $total_events ?></td>
                            <td><?= $utilization_percentage ?>%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="report-card">
            <h2>Booking Trends</h2>
            <canvas id="bookingChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('bookingChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Total Bookings',
                        data: [10, 20, 15, 30], // Replace with dynamic data
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true }
                    }
                }
            });
        });
    </script>
</body>

</html>
<?php
session_start();
include '../inc/config.php';

$type = $_GET['type'] ?? 'daily';
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

if ($type === 'weekly')
{
    $groupBy = "%Y-%u";
}
elseif ($type === 'monthly')
{
    $groupBy = "%Y-%m";
}
else
{
    $groupBy = "%Y-%m-%d";
}

$filter = '';
$params = [$groupBy];
$types = 's';

if ($startDate && $endDate)
{
    $filter = "WHERE e.event_date BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    $types .= 'ss';
}

$query = "
    SELECT DATE_FORMAT(e.event_date, ?) AS period,
           COUNT(DISTINCT b.booking_id) AS total_bookings,
           COUNT(DISTINCT e.event_id) AS total_events,
           SUM(e.event_duration) AS total_hours
    FROM event e
    LEFT JOIN bookings b ON e.event_id = b.event_id
    $filter
    GROUP BY period
    ORDER BY period ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc())
{
    $data[] = [
        'period' => $row['period'],
        'bookings' => $row['total_bookings'],
        'events' => $row['total_events'],
        'hours' => $row['total_hours'] ?? 0
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Auditorium Admin Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial;
            background: #001f3f;
            color: white;
            padding-left: 30px;
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

        .sidebar h2 {
            color: #FFD700;
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: bold;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px;
            text-align: center;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            display: block;
            padding: 10px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar ul li a:hover {
            background: #FFD700;
            color: black;
            border-radius: 5px;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #FFD700;
            color: black;
        }

        .chart-container,
        .table-container {
            margin-left: 30px;
            background: #112;
            padding: 20px;
            border-radius: 10px;
            display: none;
        }

        .container {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
            /* 👈 Prevent overlap */
        }

        canvas {
            background-color: white;
            border-radius: 10px;
        }

        .filter-links a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            font-weight: bold;
            padding: 5px 10px;
            background: #007bff;
            border-radius: 6px;
        }

        .filter-links a.active {
            background: #ffc107;
            color: black;
        }

        form {
            margin-bottom: 20px;
        }

        label,
        input {
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #007bff;
        }

        .toggle-btn {
            margin: 10px 0;
            background-color: #ffc107;
            color: black;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="../php/admin_Dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../php/Register_Organizer.php"><i class="fas fa-user-plus"></i> Create Organizer</a></li>
            <li><a href="../php/admin-report.php"><i class="fas fa-chart-bar"></i> Generate Report</a></li>
        </ul>
    </div>
    <div class="container">
        <h2>🏢 Auditorium Usage Report (<?= ucfirst($type) ?>)</h2>

        <div class="filter-links mb-3">
            <a href="?type=daily" class="<?= $type === 'daily' ? 'active' : '' ?>">Daily</a>
            <a href="?type=weekly" class="<?= $type === 'weekly' ? 'active' : '' ?>">Weekly</a>
            <a href="?type=monthly" class="<?= $type === 'monthly' ? 'active' : '' ?>">Monthly</a>
        </div>

        <button class="toggle-btn" onclick="toggleView()">📊 Toggle View</button>

        <div class="chart-container" id="chartView">
            <canvas id="adminChart"></canvas>
        </div>

        <div class="table-container" id="tableView">
            <table>
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Total Bookings</th>
                        <th>Events Hosted</th>
                        <th>Total Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?= $row['period'] ?></td>
                            <td><?= $row['bookings'] ?></td>
                            <td><?= $row['events'] ?></td>
                            <td><?= $row['hours'] ?> hrs</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        const data = <?= json_encode($data) ?>;
        const labels = data.map(d => d.period);
        const bookingsData = data.map(d => d.bookings);
        const eventsData = data.map(d => d.events);
        const hoursData = data.map(d => d.hours);

        const ctx = document.getElementById('adminChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Bookings',
                        data: bookingsData,
                        backgroundColor: 'dodgerblue'
                    },
                    {
                        label: 'Events Hosted',
                        data: eventsData,
                        backgroundColor: 'orange'
                    },
                    {
                        label: 'Total Hours (Utilization)',
                        data: hoursData,
                        backgroundColor: 'purple'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: 'black' },
                        title: { display: true, text: 'Count / Hours', color: 'black' }
                    },
                    x: {
                        ticks: { color: 'black' }
                    }
                },
                plugins: {
                    legend: { labels: { color: 'black' } },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.dataset.label;
                                const val = context.raw;
                                if (label.includes('Hours')) return `${label}: ${val} hrs`;
                                return `${label}: ${val}`;
                            }
                        }
                    }
                }
            }
        });

        function toggleView() {
            const chartDiv = document.getElementById('chartView');
            const tableDiv = document.getElementById('tableView');
            if (chartDiv.style.display === 'none') {
                chartDiv.style.display = 'block';
                tableDiv.style.display = 'none';
            } else {
                chartDiv.style.display = 'none';
                tableDiv.style.display = 'block';
            }
        }

        // Initialize with chart view
        document.getElementById('chartView').style.display = 'block';
    </script>
</body>

</html>
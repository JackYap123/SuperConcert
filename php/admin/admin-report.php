<?php
session_start();
require '../../inc/config.php';

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
    <link rel="stylesheet" href="../../css/admin/admin-sidebar.css">
    <link rel="stylesheet" href="../../css/admin/admin-report.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>

    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="../admin/admin-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../admin/register-organizer.php"><i class="fas fa-user-plus"></i> Create Organizer</a></li>
            <li><a href="../admin/admin-report.php"  class="active"><i class="fas fa-chart-bar"></i> Generate Report</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header class="main-header">
            <h2>🏢 Auditorium Usage Report (<?= ucfirst($type) ?>)</h2>
        </header>
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
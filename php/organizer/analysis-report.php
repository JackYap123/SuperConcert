<?php
session_start();
include '../../inc/config.php';

if (!isset($_SESSION['organiser_id']))
{
    header("Location: organiser_login.php");
    exit();
}

$organiser_id = $_SESSION['organiser_id'];
$type = $_GET['type'] ?? 'daily'; // daily, weekly, monthly

// Prepare query
if ($type === 'weekly')
{
    $groupBy = "%Y-%u"; // Week number
    $labelFormat = "Week %u, %Y";
}
elseif ($type === 'monthly')
{
    $groupBy = "%Y-%m"; // Month
    $labelFormat = "%b %Y";
}
else
{
    $groupBy = "%Y-%m-%d"; // Daily
    $labelFormat = "%d %b";
}

$query = "
    SELECT DATE_FORMAT(b.booking_time, ?) AS period,
           COUNT(*) AS tickets_sold,
           SUM(price) / 100 AS revenue, -- Adjusted revenue scale
           COUNT(*) / (SELECT COUNT(*) FROM event_seats es WHERE es.event_id = b.event_id) * 100 AS occupancy
    FROM bookings b
    JOIN event e ON b.event_id = e.event_id
    WHERE e.organizer_id = ?
    GROUP BY period
    ORDER BY period ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("si", $groupBy, $organiser_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc())
{
    $data[] = [
        'period' => $row['period'],
        'tickets' => $row['tickets_sold'],
        'revenue' => round($row['revenue'], 2),
        'occupancy' => round($row['occupancy'], 2)
    ];
}
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Analysis Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial;
            background: #001f3f;
            color: white;
        }
        .container h2{
            margin-bottom: 20px;
        }

        .chart-container,
        .table-container {
            background: #112;
            padding: 20px;
            border-radius: 10px;
            display: none;
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

        .container {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
            /* 👈 Prevent overlap */
        }
    </style>
</head>

<body>
    <?php
    include "../inc/sidebar.php";
    ?>

    <div class="container">

        <h2>📊 Ticket Sales Report (<?= ucfirst($type) ?>)</h2>

        <div class="filter-links mb-4">
            <a href="?type=daily" class="<?= $type === 'daily' ? 'active' : '' ?>">Daily</a>
            <a href="?type=weekly" class="<?= $type === 'weekly' ? 'active' : '' ?>">Weekly</a>
            <a href="?type=monthly" class="<?= $type === 'monthly' ? 'active' : '' ?>">Monthly</a>
        </div>

        <button class="toggle-btn" onclick="toggleView()">📊 Toggle View</button>

        <div class="chart-container" id="chartContainer">
            <canvas id="ticketChart"></canvas>
        </div>

        <div class="table-container" id="tableContainer">
            <table>
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Tickets Sold</th>
                        <th>Revenue (x100 RM)</th>
                        <th>Occupancy (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?= $row['period'] ?></td>
                            <td><?= $row['tickets'] ?></td>
                            <td>RM <?= number_format($row['revenue'], 2) ?></td>
                            <td><?= number_format($row['occupancy'], 2) ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const data = <?= json_encode($data) ?>;
        const labels = data.map(d => d.period);
        const ticketData = data.map(d => d.tickets);
        const revenueData = data.map(d => d.revenue);
        const occupancyData = data.map(d => d.occupancy);

        const chartContainer = document.getElementById('chartContainer');
        const tableContainer = document.getElementById('tableContainer');

        chartContainer.style.display = 'block';

        const ctx = document.getElementById('ticketChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Tickets Sold',
                        data: ticketData,
                        backgroundColor: 'dodgerblue'
                    },
                    {
                        label: 'Revenue (x100 RM)',
                        data: revenueData,
                        backgroundColor: 'orange'
                    },
                    {
                        label: 'Occupancy (%)',
                        data: occupancyData,
                        backgroundColor: 'limegreen'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Tickets / Revenue / Occupancy (%)'
                        },
                        ticks: {
                            color: 'black'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'black'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'black'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.dataset.label || '';
                                const value = context.raw;
                                if (label.includes('Revenue')) return `${label}: RM ${value.toFixed(2)}`;
                                if (label.includes('Occupancy')) return `${label}: ${value.toFixed(1)}%`;
                                return `${label}: ${value}`;
                            }
                        }
                    }
                }
            }
        });

        function toggleView() {
            if (chartContainer.style.display === 'none') {
                chartContainer.style.display = 'block';
                tableContainer.style.display = 'none';
                document.querySelector('.toggle-btn').innerText = 'Switch to Table View';
            } else {
                chartContainer.style.display = 'none';
                tableContainer.style.display = 'block';
                document.querySelector('.toggle-btn').innerText = 'Switch to Chart View';
            }
        }
    </script>
</body>

</html>
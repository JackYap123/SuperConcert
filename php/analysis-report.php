<?php
session_start();
require_once('../inc/config.php');

// 确保用户是组织者
if (!isset($_SESSION['organiser_id'])) {
    die("Access denied. Please log in as an organizer.");
}

$organiser_id = $_SESSION['organiser_id'];

// 获取分析数据
function getAnalyticsData($conn, $organiser_id) {
    $query = "
    SELECT DATE(t.sale_date) AS date, 
           COUNT(t.sale_id) AS ticket_sales, 
           SUM(t.price) AS revenue, 
           COUNT(s.booking_id) AS seat_occupancy
    FROM event e
    LEFT JOIN ticket_sales t ON e.event_id = t.event_id
    LEFT JOIN seat_bookings s ON e.event_id = s.event_id
    WHERE e.organizer_id = ?
    GROUP BY date
    ORDER BY date ASC;
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $organiser_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['date'])) { // 确保日期不为空
            $row['ticket_sales'] = $row['ticket_sales'] ?? 0;
            $row['revenue'] = $row['revenue'] ?? 0;
            $row['seat_occupancy'] = $row['seat_occupancy'] ?? 0;
            $data[] = $row;
        }
    }
    return $data;
}

$data = getAnalyticsData($conn, $organiser_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/analytics.css">
    <style>
        .content-section { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .hidden { display: none; }
        .btn-container { margin-bottom: 20px; }
        .btn { padding: 10px; margin-right: 10px; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 5px; }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Analytics Dashboard</h1>

    <div class="btn-container">
        <button class="btn" onclick="showSection('chartSection')">Show Chart</button>
        <button class="btn" onclick="showSection('tableSection')">Show Table</button>
    </div>

    <!-- 图表区域 -->
    <div id="chartSection" class="content-section">
        <canvas id="analyticsChart"></canvas>
    </div>

    <!-- 数据表 -->
    <div id="tableSection" class="content-section hidden">
        <h2>Data Table</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Ticket Sales</th>
                    <th>Revenue</th>
                    <th>Seat Occupancy</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['ticket_sales']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($row['revenue'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($row['seat_occupancy']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No data available</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function showSection(sectionId) {
            document.getElementById('chartSection').classList.add('hidden');
            document.getElementById('tableSection').classList.add('hidden');
            document.getElementById(sectionId).classList.remove('hidden');
        }

        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('analyticsChart').getContext('2d');
            
            var chartData = {
                labels: <?php echo json_encode(array_column($data, 'date')); ?>,
                datasets: [
                    {
                        label: "Ticket Sales",
                        data: <?php echo json_encode(array_map('intval', array_column($data, 'ticket_sales'))); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    },
                    {
                        label: "Revenue",
                        data: <?php echo json_encode(array_map('floatval', array_column($data, 'revenue'))); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)'
                    },
                    {
                        label: "Seat Occupancy",
                        data: <?php echo json_encode(array_map('intval', array_column($data, 'seat_occupancy'))); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)'
                    }
                ]
            };

            console.log("Chart Data:", chartData);

            new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</body>
</html>

<?php
session_start();
include '../../inc/config.php';

$organiser_id = $_SESSION['organiser_id'] ?? null;
if (!$organiser_id) {
    header("Location: organiser_login.php");
    exit();
}

$type = $_GET['type'] ?? 'daily'; // daily, weekly, monthly

if ($type === 'weekly') {
    $groupBy = "%Y-%u";
} elseif ($type === 'monthly') {
    $groupBy = "%Y-%m";
} else {
    $groupBy = "%Y-%m-%d";
}

$query = "
    SELECT DATE_FORMAT(b.booking_time, ?) AS period,
           COUNT(*) AS tickets_sold,
           SUM(price) / 100 AS revenue,
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
while ($row = $result->fetch_assoc()) {
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
    <link rel="stylesheet" href="../../css/organizer/analysis-report.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/organizer/organizer-sidebar.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
<div class="sidebar">
    <h2>SuperConcert</h2>
    <ul>
        <li><a href="../organizer/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="../organizer/event-creation.php"><i class="fas fa-calendar-plus"></i> Create Event</a></li>
        <li><a href="../organizer/browse-event.php"><i class="fas fa-magnifying-glass"></i> Browse Events</a></li>
        <li><a href="../organizer/select-event.php"><i class="fas fa-ticket-alt"></i> Ticket Setup</a></li>
        <li><a href="../organizer/analysis-report.php" class="active"><i class="fas fa-book"></i> Analysis Report</a></li>
    </ul>
</div>

<div class="content">
    <div class="header">
        <h1>📊 Ticket Sales Report (<?= ucfirst($type) ?>)</h1>
    </div>

    <div class="container">
        <div class="filter-links mb-4">
            <a href="?type=daily" class="<?= $type === 'daily' ? 'active' : '' ?>">Daily</a>
            <a href="?type=weekly" class="<?= $type === 'weekly' ? 'active' : '' ?>">Weekly</a>
            <a href="?type=monthly" class="<?= $type === 'monthly' ? 'active' : '' ?>">Monthly</a>
        </div>

        <?php if (empty($data)): ?>
            <div class="alert alert-warning">No ticket sales data available for the selected period.</div>
        <?php else: ?>
            <button class="toggle-btn" onclick="toggleView()">📊 Table View</button>
            <button class="btn btn-secondary mb-3" onclick="downloadPDF()">📥 Download PDF</button>

            <div class="chart-container" id="chartContainer">
                <canvas id="ticketChart"></canvas>
            </div>

            <div class="table-container" id="tableContainer">
                <table id="reportTable">
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
        <?php endif; ?>
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
    if (data.length > 0) chartContainer.style.display = 'block';

    const ctx = document.getElementById('ticketChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Tickets Sold', data: ticketData, backgroundColor: 'dodgerblue' },
                    { label: 'Revenue (x100 RM)', data: revenueData, backgroundColor: 'orange' },
                    { label: 'Occupancy (%)', data: occupancyData, backgroundColor: 'limegreen' }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                stacked: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Value or %' },
                        ticks: { color: 'black' }
                    },
                    x: { ticks: { color: 'black' } }
                },
                plugins: {
                    legend: { labels: { color: 'black' } },
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
    }

    function toggleView() {
        if (chartContainer.style.display === 'none') {
            chartContainer.style.display = 'block';
            tableContainer.style.display = 'none';
            document.querySelector('.toggle-btn').innerText = '📊 Table View';
        } else {
            chartContainer.style.display = 'none';
            tableContainer.style.display = 'block';
            document.querySelector('.toggle-btn').innerText = '📈 Chart View';
        }
    }

    function downloadPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(18);
        doc.text("Ticket Sales Report", 14, 22);

        const headers = [["Period", "Tickets Sold", "Revenue (x100 RM)", "Occupancy (%)"]];
        const rows = data.map(row => [row.period, row.tickets, `RM ${row.revenue.toFixed(2)}`, `${row.occupancy.toFixed(2)}%`]);

        doc.autoTable({ startY: 30, head: headers, body: rows });
        doc.save("ticket_sales_report.pdf");
    }
</script>
</body>
</html>

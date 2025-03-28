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
            <li><a href="../php/admin-report.php" class="active"><i class="fas fa-chart-bar"></i> Generate Report</a>
            </li>
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
            <h2>Booking Analysis</h2>
            <div id="chartSection">
                <canvas id="bookingChart"></canvas>
            </div>
        </div>

        <div class="btn-container">
            <button class="btn" id="toggleTableBtn">Show Table</button>
        </div>

        <div class="report-card hidden" id="reportSection">
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


        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let adminReportChart;
                let ctx = document.getElementById("bookingChart").getContext("2d");

                function updateAdminReportChart(data) {
                    let labels = ['Total Bookings', 'Total Events Hosted', 'Utilization (%)'];
                    let bookings = data.total_bookings || 0;
                    let events = data.total_events || 0;
                    let utilization = data.utilization_percentage || 0;

                    // Destroy existing chart before creating a new one
                    if (adminReportChart) {
                        adminReportChart.destroy();
                    }

                    adminReportChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: "📅 Total Bookings",
                                    data: [bookings, 0, 0],
                                    backgroundColor: 'rgba(54, 162, 235, 0.9)',
                                    borderRadius: 12,
                                    borderSkipped: false
                                },
                                {
                                    label: "🎭 Total Events Hosted",
                                    data: [0, events, 0],
                                    backgroundColor: 'rgba(255, 99, 132, 0.9)',
                                    borderRadius: 12,
                                    borderSkipped: false
                                },
                                {
                                    label: "📊 Utilization (%)",
                                    data: [0, 0, utilization],
                                    backgroundColor: 'rgba(75, 192, 192, 0.9)',
                                    borderRadius: 12,
                                    borderSkipped: false
                                }

                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: { padding: 20 },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: "top",
                                    labels: {
                                        font: { size: 14, weight: 'bold' },
                                        color: "#333"
                                    }
                                },
                                tooltip: {
                                    backgroundColor: "#222",
                                    titleColor: "#fff",
                                    bodyColor: "#fff",
                                    borderWidth: 1,
                                    borderColor: "#fff",
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                x: {
                                    display: true,
                                    grid: { display: false },
                                    ticks: {
                                        autoSkip: false,
                                        font: {
                                            size: 12
                                        },
                                        color: "#333"
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: "rgba(200, 200, 200, 0.5)" },
                                    ticks: {
                                        font: {
                                            size: 12
                                        },
                                        color: "#333"
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            }
                        }
                    });
                }

                function fetchAdminReportData(filter = "monthly") {
                    fetch(`admin-report-data.php?filter=${filter}`)
                        .then(response => response.json())
                        .then(data => updateAdminReportChart(data))
                        .catch(error => console.error("Error fetching report data:", error));
                }

                fetchAdminReportData(); // Load default data on page load

                document.getElementById("dateFilter").addEventListener("change", function () {
                    fetchAdminReportData(this.value);
                });

                document.getElementById('toggleTableBtn').addEventListener('click', function () {
                    const reportSection = document.getElementById('reportSection');
                    if (reportSection.classList.contains('hidden')) {
                        reportSection.classList.remove('hidden');
                        this.textContent = "Hide Table";
                    } else {
                        reportSection.classList.add('hidden');
                        this.textContent = "Show Table";
                    }
                });
            });



        </script>
</body>

</html>
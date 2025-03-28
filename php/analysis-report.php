<?php
session_start();
require_once('../inc/config.php');

// 确保用户是组织者
if (!isset($_SESSION['organiser_id']))
{
    die("Access denied. Please log in as an organizer.");
}

$organiser_id = $_SESSION['organiser_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/analytics.css">
</head>

<body>
    <?php include "../inc/sidebar.php"; ?>

    <div class="main-content">
        <h1 class="header">Analytics Dashboard</h1>
        <div class="filter-container">
            <label for="dateFilter">Select Time Range:</label>
            <select id="dateFilter">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>

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
                <tbody id="dataTableBody">
                    <!-- AJAX 动态填充数据 -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            document.getElementById('chartSection').classList.add('hidden');
            document.getElementById('tableSection').classList.add('hidden');
            document.getElementById(sectionId).classList.remove('hidden');
        }

        document.addEventListener("DOMContentLoaded", function () {
            let ctx = document.getElementById('analyticsChart').getContext('2d');
            let analyticsChart;

            function fetchAnalyticsData(filter) {
                fetch("fetch_analytics.php?filter=" + filter)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);  // Check if data is received correctly

                        updateTable(data);
                        updateChart(data);
                    });
            }

            function updateTable(data) {
                let tableBody = document.getElementById("dataTableBody");
                tableBody.innerHTML = "";
                data.forEach(row => {
                    let tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${row.date}</td>
                        <td>${row.ticket_sales}</td>
                        <td>${parseFloat(row.revenue).toFixed(2)}</td>
                        <td>${row.seat_occupancy}</td>
                    `;
                    tableBody.appendChild(tr);
                });
            }

            function updateChart(data) {
                let labels = data.map(row => row.date);
                let ticketSales = data.map(row => row.ticket_sales);
                let revenue = data.map(row => parseFloat(row.revenue) / 100);
                let seatOccupancy = data.map(row => row.seat_occupancy);

                if (analyticsChart) analyticsChart.destroy();

                analyticsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: "🎟 Ticket Sales",
                                data: ticketSales,
                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                borderRadius: 8,
                            },
                            {
                                label: "💰 Revenue (x100)",
                                data: revenue,
                                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                                borderRadius: 8,
                            },
                            {
                                label: "📍 Seat Occupancy",
                                data: seatOccupancy,
                                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                                borderRadius: 8,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: 20,
                                right: 20,
                                top: 20,
                                bottom: 20,
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: "top",
                                labels: {
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    color: "#333"
                                }
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

            document.getElementById("dateFilter").addEventListener("change", function () {
                fetchAnalyticsData(this.value);
            });

            fetchAnalyticsData("daily");
        });
    </script>
</body>

</html>
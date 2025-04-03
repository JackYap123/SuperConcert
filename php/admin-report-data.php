<?php
session_start();
require_once('../inc/config.php');

// Get the selected filter from the request (default: 'monthly')
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'monthly';

// Set the end date as today
$end_date = date('Y-m-d');

// Set the start date dynamically based on filter selection
if ($filter == "daily") {
    $start_date = $end_date; // Today's data
} elseif ($filter == "weekly") {
    $start_date = date('Y-m-d', strtotime('-6 days')); // Last 7 days
} else { // Monthly (default)
    $start_date = date('Y-m-d', strtotime('-29 days')); // Last 30 days
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

// Ensure data exists and format it correctly
$response = [
    'total_bookings' => $data['total_bookings'] ?? 0,
    'total_events' => $data['total_events'] ?? 0,
    'utilization_percentage' => number_format($data['utilization_percentage'], 2) ?? 0
];

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>

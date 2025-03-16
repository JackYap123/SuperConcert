<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database configuration file
include '../inc/config.php';

// Check if the database connection was successful
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection is null']);
    exit;
}

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Fetch seat data using prepared statements
$sql = "SELECT seat_number, section, is_reserved, category, price FROM seats";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

if (!$stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
if (!$result) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Get result failed: ' . $stmt->error]);
    exit;
}

// Fetch the data
$seats = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $seats[] = $row;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($seats);

// Close the statement and the database connection
$stmt->close();
$conn->close();
?>
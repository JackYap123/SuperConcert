<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database configuration file
include '../inc/config.php';

// Get the selected seats data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['seats'])) {
    foreach ($data['seats'] as $seat) {
        $seatNumber = $seat['seatNumber'];
        $price = $seat['price'];

        // Check if the seat is already reserved
        $checkSql = "SELECT is_reserved FROM seats WHERE seat_number = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $seatNumber);
        $checkStmt->execute();
        $checkStmt->bind_result($isReserved);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($isReserved) {
            echo json_encode(['status' => 'error', 'message' => "Seat $seatNumber is already reserved"]);
            exit;
        }

        // Update the seat in the database
        $sql = "UPDATE seats SET is_reserved = TRUE, price = ? WHERE seat_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ds", $price, $seatNumber);
        $stmt->execute();
    }

    echo json_encode(['status' => 'success', 'message' => 'Seats reserved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No seats selected']);
}

$conn->close();
?>
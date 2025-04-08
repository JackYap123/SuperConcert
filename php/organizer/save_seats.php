<?php
session_start();
ini_set('display_errors', 0); // Prevent HTML error output
ini_set('log_errors', 1);     // Log errors to error log
error_reporting(E_ALL);

include '../../inc/config.php';

header('Content-Type: application/json'); // Always return JSON

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

// Get POST data as JSON
$data = json_decode(file_get_contents('php://input'), true);

// Check for decoding error
if ($data === null) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit();
}

// Validate presence of event_id and seats
$event_id = $data['event_id'] ?? null;
$seats = $data['seats'] ?? [];

if (!$event_id || !is_array($seats) || count($seats) === 0) {
    echo json_encode(['success' => false, 'error' => 'Missing or invalid event_id or seats']);
    exit();
}

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("
        INSERT INTO event_seats (event_id, row_label, seat_number, category, price)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE category = VALUES(category), price = VALUES(price)
    ");

    foreach ($seats as $seat) {
        // Ensure each seat has required data
        if (
            !isset($seat['row']) ||
            !isset($seat['seat_number']) ||
            !isset($seat['category']) ||
            !isset($seat['price'])
        ) {
            throw new Exception("Seat data is incomplete: " . json_encode($seat));
        }

        $row = $seat['row'];
        $seat_number = $seat['seat_number'];
        $category = $seat['category'];
        $price = floatval($seat['price']);

        $stmt->bind_param("isssd", $event_id, $row, $seat_number, $category, $price);
        $stmt->execute();
    }

    $stmt->close();
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => 'Error saving seats: ' . $e->getMessage()]);
}
?>

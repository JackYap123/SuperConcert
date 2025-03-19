<?php
session_start();
include '../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$event_id = $data['event_id'];
$seats = $data['seats'];

if (!$event_id || empty($seats)) {
    echo json_encode(['success' => false, 'error' => 'Missing event ID or seats data']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO event_seats (event_id, row_label, seat_number, category, price) 
                        VALUES (?, ?, ?, ?, ?) 
                        ON DUPLICATE KEY UPDATE category = VALUES(category), price = VALUES(price)");

foreach ($seats as $seat) {
    $stmt->bind_param("isssd", $event_id, $seat['row'], $seat['seat_number'], $seat['category'], $seat['price']);
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true]);
?>

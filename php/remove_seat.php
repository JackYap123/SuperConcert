<?php
session_start();
include '../inc/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$event_id = $data['event_id'];
$seat_id = $data['seat_id'];

if (!$event_id || !$seat_id) {
    echo json_encode(['success' => false, 'error' => 'Missing event ID or seat ID']);
    exit();
}

$row_label = substr($seat_id, 0, 1); // 获取行号 (A, B, C)
$seat_number = substr($seat_id, 1); // 获取座位号 (1, 2, 3)

$stmt = $conn->prepare("DELETE FROM event_seats WHERE event_id = ? AND row_label = ? AND seat_number = ?");
$stmt->bind_param("iss", $event_id, $row_label, $seat_number);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Seat not found']);
}

$stmt->close();
$conn->close();
?>

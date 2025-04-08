<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../../inc/config.php'; // 确保连接数据库

    $data = json_decode(file_get_contents("php://input"), true);
    $event_id = $data['event_id'] ?? null;
    $seat_id = $data['seat_id'] ?? null;

    if (!$event_id || !$seat_id) {
        echo json_encode(["success" => false, "error" => "Missing event_id or seat_id"]);
        exit;
    }

    // **从数据库删除座位**
    $stmt = $conn->prepare("DELETE FROM event_seats WHERE event_id = ? AND seat_number = ?");
    $stmt->bind_param("is", $event_id, $seat_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Cannot find seat"]);
    }

    $stmt->close();
    $conn->close();
}
?>

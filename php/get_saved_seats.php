<?php
session_start();
include '../inc/config.php';

$event_id = $_GET['event_id'];

header('Content-Type: application/json'); // 确保返回 JSON

$query = "SELECT * FROM event_seats WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];
while ($row = $result->fetch_assoc()) {
    $seats[] = $row;
}

echo json_encode(["seats" => $seats]);
?>

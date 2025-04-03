<?php
session_start();
require '../inc/config.php'; // 连接数据库

$event_id = $_GET['event_id'] ?? null;
if (!$event_id) {
    echo "Invalid event ID";
    exit;
}

// **查询最新的座位信息**
$stmt = $conn->prepare("SELECT seat_number, status FROM event_seats WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

// **输出最新的座位 HTML**
while ($row = $result->fetch_assoc()) {
    $seat_id = $row['seat_number'];
    $status = $row['status'];
    $seatClass = ($status == 'booked') ? 'booked-seat' : 'available-seat';
    
    echo "<div class='seat $seatClass' data-seat-id='$seat_id' onclick='toggleSeatSelection(\"$seat_id\")'>$seat_id</div>";
}

$stmt->close();
$conn->close();
?>

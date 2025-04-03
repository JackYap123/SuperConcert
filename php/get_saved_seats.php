<?php
session_start();
include '../inc/config.php';

// Set response header to JSON
header('Content-Type: application/json');

// Validate event_id
if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id']))
{
    echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
    exit();
}

$event_id = intval($_GET['event_id']);

try
{
    $query = "SELECT row_label, seat_number, category, price FROM event_seats WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $seats = [];
    while ($row = $result->fetch_assoc())
    {
        $seats[] = $row;
    }

    echo json_encode(['success' => true, 'seats' => $seats]);
}
catch (Exception $e)
{
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
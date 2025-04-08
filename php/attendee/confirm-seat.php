<?php
session_start();
include '../../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    header("Location: ../organiser_login.php");
    exit();
}

$attendee_id = $_SESSION['attendee_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $event_id = intval($_POST['event_id']);
    $selected_seats = isset($_POST['selected_seats']) ? explode(',', $_POST['selected_seats']) : [];

    if (empty($selected_seats))
    {
        echo "<script>alert('No seats selected.'); window.history.back();</script>";
        exit();
    }

    // Fetch seat info again from DB for validation and price confirmation
    $placeholders = implode(',', array_fill(0, count($selected_seats), '?'));
    $params = str_repeat('s', count($selected_seats));

    $query = "SELECT * FROM event_seats WHERE event_id = ? AND seat_number IN ($placeholders)";
    $stmt = $conn->prepare($query);

    $types = 'i' . $params;
    $bind_values = array_merge([$event_id], $selected_seats);
    $stmt->bind_param($types, ...$bind_values);
    $stmt->execute();
    $result = $stmt->get_result();

    $valid_seats = [];
    $total_price = 0;

    while ($row = $result->fetch_assoc())
    {
        $valid_seats[] = $row;
        $total_price += $row['price'];
    }

    if (count($valid_seats) !== count($selected_seats))
    {
        echo "<script>alert('Some selected seats are invalid or no longer available.'); window.history.back();</script>";
        exit();
    }

    // Save booking
    foreach ($valid_seats as $seat)
    {
        $insert = $conn->prepare("INSERT INTO bookings (attendee_id, event_id, row_label, seat_number, category, price) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param("iisssd", $attendee_id, $event_id, $seat['row_label'], $seat['seat_number'], $seat['category'], $seat['price']);
        $insert->execute();
    }

    $_SESSION['pending_payment'] = [
        'event_id' => $event_id,
        'selected_seats' => implode(',', $selected_seats),
        'total' => $total_price
    ];
    header("Location: payment.php");
    exit();
}
else
{
    echo "<script>alert('Invalid access.'); window.location.href = 'choose-event.php';</script>";
    exit();
}
?>
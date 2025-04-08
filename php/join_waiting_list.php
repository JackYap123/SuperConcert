<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    echo "<script>alert('Please log in first.'); window.location.href = 'organiser_login.php';</script>";
    exit();
}

$attendee_id = $_SESSION['attendee_id'];
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($event_id <= 0)
{
    echo "<script>alert('Invalid event ID.'); window.location.href='attendee_dashboard.php';</script>";
    exit();
}

// ✅ Step 1: Check if the event exists
$check_event = $conn->prepare("SELECT event_id FROM event WHERE event_id = ?");
$check_event->bind_param("i", $event_id);
$check_event->execute();
$event_result = $check_event->get_result();

if ($event_result->num_rows === 0)
{
    echo "<script>alert('This event does not exist.'); window.location.href='attendee_dashboard.php';</script>";
    exit();
}
$check_event->close();

// ✅ Step 2: Check if the event is full
$seat_count_query = $conn->prepare("SELECT COUNT(*) AS total_seats FROM event_seats WHERE event_id = ?");
$seat_count_query->bind_param("i", $event_id);
$seat_count_query->execute();
$total_seats_result = $seat_count_query->get_result();
$total_seats_row = $total_seats_result->fetch_assoc();
$total_seats = $total_seats_row['total_seats'];
$seat_count_query->close();

// Check how many seats have been booked
$booked_query = $conn->prepare("SELECT COUNT(*) AS booked FROM bookings WHERE event_id = ?");
$booked_query->bind_param("i", $event_id);
$booked_query->execute();
$booked_result = $booked_query->get_result();
$booked_row = $booked_result->fetch_assoc();
$booked_seats = $booked_row['booked'];
$booked_query->close();

if ($booked_seats < $total_seats)
{
    echo "<script>alert('This event still has available seats. Please book directly.'); window.location.href='select_seat.php?event_id=$event_id';</script>";
    exit();
}

// ✅ Step 3: Check if user already joined the waiting list
$check_existing = $conn->prepare("SELECT * FROM waiting_list WHERE attendee_id = ? AND event_id = ?");
$check_existing->bind_param("ii", $attendee_id, $event_id);
$check_existing->execute();
$existing_result = $check_existing->get_result();

if ($existing_result->num_rows > 0)
{
    echo "<script>alert('You are already on the waiting list for this event.'); window.location.href='attendee_dashboard.php';</script>";
    exit();
}
$check_existing->close();

// ✅ Step 4: Insert into waiting_list
$insert_wait = $conn->prepare("INSERT INTO waiting_list (attendee_id, event_id, status, request_time) VALUES (?, ?, 'pending', NOW())");
$insert_wait->bind_param("ii", $attendee_id, $event_id);

if ($insert_wait->execute())
{
    echo "<script>alert('You have successfully joined the waiting list. You will be notified if any seat becomes available.'); window.location.href='attendee_dashboard.php';</script>";
}
else
{
    echo "<script>alert('Something went wrong. Please try again later.'); window.location.href='attendee_dashboard.php';</script>";
}
$insert_wait->close();
?>
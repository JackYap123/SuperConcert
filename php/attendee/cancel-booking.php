<?php
session_start();
include '../../inc/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in']) {
    echo "<script>alert('Please login first.'); window.location.href='../organiser_Login.php';</script>";
    exit();
}

if (!isset($_POST['event_id'], $_POST['seat_number'])) {
    echo "<script>alert('Missing event or seat data.'); window.location.href='../attendee-dashboard.php';</script>";
    exit();
}

$attendee_id = $_SESSION['attendee_id'];
$event_id = intval($_POST['event_id']);
$seat_number = $_POST['seat_number'];

// 1. Verify booking
$check_stmt = $conn->prepare("SELECT * FROM bookings WHERE attendee_id = ? AND event_id = ? AND seat_number = ?");
$check_stmt->bind_param("iis", $attendee_id, $event_id, $seat_number);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Booking not found or already cancelled.'); window.location.href='../attendee-dashboard.php';</script>";
    exit();
}

$booking = $result->fetch_assoc();
$refund_amount = $booking['price'];
$check_stmt->close();

// 2. Delete booking
$delete_stmt = $conn->prepare("DELETE FROM bookings WHERE attendee_id = ? AND event_id = ? AND seat_number = ?");
$delete_stmt->bind_param("iis", $attendee_id, $event_id, $seat_number);
$delete_stmt->execute();
$delete_stmt->close();

// 3. Send cancellation email to current user
$attendee_email = $_SESSION['attendee_email'] ?? '';
$attendee_name = $_SESSION['attendee_name'] ?? 'Attendee';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'yapfongkiat53@gmail.com';
    $mail->Password = 'momfaxlauusnbnvl';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('yapfongkiat53@gmail.com', 'SuperConcert');
    $mail->addAddress($attendee_email, $attendee_name);
    $mail->isHTML(true);
    $mail->Subject = 'Your SuperConcert Booking Cancellation';
    $mail->Body = "
        <html>
        <body style='font-family: Arial;'>
            <h2>Hi $attendee_name!</h2>
            <p>Your booking for seat <strong>$seat_number</strong> (Event #$event_id) has been successfully cancelled.</p>
            <p>Refund Amount: <strong>RM $refund_amount</strong></p>
            <p>If you have any questions, please contact us.</p>
        </body>
        </html>
    ";
    $mail->send();
} catch (Exception $e) {
    error_log("PHPMailer Error (user cancel email): " . $mail->ErrorInfo);
}

// 4. Notify first in waiting list
$waitQuery = $conn->prepare("SELECT wl.attendee_id, a.email, a.full_name 
    FROM waiting_list wl 
    JOIN attendee a ON wl.attendee_id = a.attendee_id 
    WHERE wl.event_id = ? 
    ORDER BY wl.request_time ASC LIMIT 1");
$waitQuery->bind_param("i", $event_id);
$waitQuery->execute();
$waitResult = $waitQuery->get_result();

if ($waitResult->num_rows > 0) {
    $person = $waitResult->fetch_assoc();
    $email = $person['email'];
    $name = htmlspecialchars($person['full_name']);
    $target_id = $person['attendee_id'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yapfongkiat53@gmail.com';
        $mail->Password = 'momfaxlauusnbnvl';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('yapfongkiat53@gmail.com', 'SuperConcert');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Seat Now Available for SuperConcert';
        $mail->Body = "
            <html>
            <body style='font-family: Arial;'>
                <h2>Hi $name!</h2>
                <p>A seat has opened up for the event you were waiting for!</p>
                <p><a href='http://localhost/SuperConcert/php/select-seat.php?event_id=$event_id'>Click here to grab your seat now</a></p>
                <p>Don't miss your chance to attend. Act fast before it's taken again!</p>
                <hr>
                <small>This message was sent automatically. If you've already bought a ticket, please ignore this email.</small>
            </body>
            </html>
        ";
        $mail->send();
        error_log("Notification sent to waiting list user: $email");

        $deleteWL = $conn->prepare("DELETE FROM waiting_list WHERE attendee_id = ? AND event_id = ?");
        $deleteWL->bind_param("ii", $target_id, $event_id);
        $deleteWL->execute();
        $deleteWL->close();
    } catch (Exception $e) {
        error_log("PHPMailer Error (waitlist): " . $mail->ErrorInfo);
    }
} else {
    error_log("No one in the waiting list for event ID $event_id.");
}

echo "<script>window.location.href='attendee-dashboard.php';</script>";
?>

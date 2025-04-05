<?php
session_start();
include '../inc/config.php';
require '../vendor/autoload.php'; // Make sure PHPMailer is loaded

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in']) {
    echo "<script>alert('Please login first.'); window.location.href='organiser_login.php';</script>";
    exit();
}

if (!isset($_POST['event_id'], $_POST['seat_number'])) {
    echo "<script>alert('Missing event or seat data.'); window.location.href='attendee_dashboard.php';</script>";
    exit();
}

$attendee_id = $_SESSION['attendee_id'];
$event_id = intval($_POST['event_id']);
$seat_number = $_POST['seat_number'];

// Get booking info
$check_stmt = $conn->prepare("SELECT * FROM bookings WHERE attendee_id = ? AND event_id = ? AND seat_number = ?");
$check_stmt->bind_param("iis", $attendee_id, $event_id, $seat_number);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('This booking does not exist.'); window.location.href='attendee_dashboard.php';</script>";
    exit();
}

$booking = $result->fetch_assoc();
$refund_amount = $booking['price'];

// Fetch attendee email/name
$user_stmt = $conn->prepare("SELECT full_name, email FROM attendee WHERE attendee_id = ?");
$user_stmt->bind_param("i", $attendee_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

$name = $user['name'];
$email = $user['email'];

// Delete the booking
$delete_stmt = $conn->prepare("DELETE FROM bookings WHERE attendee_id = ? AND event_id = ? AND seat_number = ?");
$delete_stmt->bind_param("iis", $attendee_id, $event_id, $seat_number);
$delete_stmt->execute();

// Send cancellation email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'yapfongkiat53@gmail.com';  // Your email
    $mail->Password = 'momfaxlauusnbnvl';         // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('yapfongkiat53@gmail.com', 'SuperConcert');
    $mail->addAddress($email, $name);

    $mail->isHTML(true);
    $mail->Subject = '❌ Ticket Cancellation Confirmed';
    $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2 style='color: red;'>Your Ticket Has Been Cancelled</h2>
            <p>Hi <strong>$name</strong>,</p>
            <p>This is a confirmation that your booking for <strong>Seat $seat_number</strong> (Event ID: <strong>$event_id</strong>) has been successfully cancelled.</p>
            <p>A refund of <strong>RM" . number_format($refund_amount, 2) . "</strong> will be processed shortly.</p>
            <br>
            <p>If you have any issues, please contact our support.</p>
            <p>Thank you for using SuperConcert</p>
        </body>
        </html>
    ";
    $mail->send();
} catch (Exception $e) {
    error_log("❌ Email sending failed: " . $mail->ErrorInfo);
}

echo "<script>alert('Your booking for seat $seat_number has been cancelled. RM$refund_amount will be refunded.'); window.location.href='attendee_dashboard.php';</script>";
?>

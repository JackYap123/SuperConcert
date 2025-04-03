<?php
session_start();
include '../inc/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

if (!isset($_POST['event_id'], $_POST['selected_seats'], $_POST['total'], $_POST['payment_method']))
{
    echo json_encode(['success' => false, 'message' => 'Incomplete payment data.']);
    exit();
}

$attendee_id = $_SESSION['attendee_id'] ?? null;
$attendee_email = $_SESSION['attendee_email'] ?? 'user@example.com';
$attendee_name = $_SESSION['attendee_name'] ?? 'Attendee';
$event_id = intval($_POST['event_id']);
$selected_seats = explode(',', $_POST['selected_seats']);
$total = floatval($_POST['total']);
$payment_method = $_POST['payment_method'];
$timestamp = date('Y-m-d H:i:s');

function simulatePayment($method, $amount)
{
    return [
        'status' => 'success',
        'message' => ucfirst($method) . ' payment successful',
        'reference' => strtoupper($method) . '_' . uniqid(),
    ];
}

$response = simulatePayment($payment_method, $total);

if ($response['status'] === 'success')
{
    $stmt = $conn->prepare("INSERT IGNORE INTO bookings (attendee_id, event_id, seat_number, payment_method, payment_ref, price, booking_time) VALUES (?, ?, ?, ?, ?, ?, ?)");

    foreach ($selected_seats as $seat)
    {
        // Check if seat already booked
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE event_id = ? AND seat_number = ?");
        $check_stmt->bind_param("is", $event_id, $seat);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        $seat_query = $conn->prepare("SELECT price FROM event_seats WHERE event_id = ? AND seat_number = ?");
        $seat_query->bind_param("is", $event_id, $seat);
        $seat_query->execute();
        $seat_result = $seat_query->get_result();
        $seat_data = $seat_result->fetch_assoc();
        $seat_price = $seat_data['price'];

        $stmt->bind_param("iisssds", $attendee_id, $event_id, $seat, $payment_method, $response['reference'], $seat_price, $timestamp);
        $stmt->execute();
    }

    // === Send Confirmation Email to Attendee ===
    $mail = new PHPMailer(true);
    try
    {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yapfongkiat53@gmail.com';  // Your email
        $mail->Password = 'momfaxlauusnbnvl';        // App password (not regular password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('yapfongkiat53@gmail.com', 'SuperConcert');
        $mail->addAddress($attendee_email, $attendee_name ?? 'Attendee');

        $mail->isHTML(true);
        $mail->Subject = '🎟️ Your SuperConcert Booking is Confirmed!';
        $mail->Body = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <h2 style='color: #007bff;'>Thank you for your purchase!</h2>
                <p>Hi <strong>$attendee_name</strong>,</p>
                <p>Your ticket(s) for Event <strong>#$event_id</strong> have been successfully booked.</p>
                <p><strong>Seats:</strong> " . implode(', ', $selected_seats) . "</p>
                <p><strong>Total Paid:</strong> RM " . number_format($total, 2) . "</p>
                <p><strong>Payment Method:</strong> $payment_method</p>
                <p><strong>Reference No:</strong> {$response['reference']}</p>
                <br>
                <p>Enjoy the show! 🎤🎶</p>
                <hr>
                <p style='font-size: 12px; color: #666;'>If you didn’t make this purchase, please contact us immediately.</p>
            </body>
            </html>
        ";

        $mail->send();
    }
    catch (Exception $e)
    {
        error_log('Email Error: ' . $mail->ErrorInfo);
    }

    unset($_SESSION['pending_payment']);

    echo "<script>alert('Payment successful! Confirmation sent to your email.'); window.location.href = 'attendee_dashboard.php';</script>";
}
else
{
    echo "<script>alert('Payment failed. Please try again.'); window.location.href = 'payment.php';</script>";
}
?>
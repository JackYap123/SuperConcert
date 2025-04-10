<?php
include '../inc/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Get all active events with waitlists
$events = $conn->query("
    SELECT wl.*, a.email, a.name, e.event_date
    FROM waiting_list wl
    JOIN attendees a ON wl.attendee_id = a.id
    JOIN event e ON wl.event_id = e.event_id
    WHERE wl.status = 'waiting'
");

while ($row = $events->fetch_assoc()) {
    $event_id = $row['event_id'];
    $email = $row['email'];
    $name = $row['name'];
    $attendee_id = $row['attendee_id'];
    $today = date('Y-m-d');

    // Check if seats available
    $seats = $conn->prepare("SELECT COUNT(*) as available FROM event_seats 
                             WHERE event_id = ? 
                             AND seat_number NOT IN 
                             (SELECT seat_number FROM bookings WHERE event_id = ?)");
    $seats->bind_param("ii", $event_id, $event_id);
    $seats->execute();
    $seat_result = $seats->get_result()->fetch_assoc();

    if ($seat_result['available'] > 0 && $today <= $row['event_date']) {
        // Notify attendee
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
            $mail->Subject = "🎟️ A seat just opened for your event!";
            $mail->Body = "<p>Hi $name,</p><p>A seat has just become available for your event.</p>
                           <p><a href='http://localhost/SuperConcert/php/select-seat.php?event_id=$event_id'>Click here to book now!</a></p>";

            $mail->send();

            // Update waiting list
            $conn->query("UPDATE waiting_list 
                          SET status='notified', notified_time=NOW() 
                          WHERE event_id = $event_id AND attendee_id = $attendee_id");
        } catch (Exception $e) {
            error_log("Failed to notify $email: " . $mail->ErrorInfo);
        }
    } elseif ($today > $row['event_date']) {
        // Event passed - send expiration email
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
            $mail->Subject = "😢 No seats became available.";
            $mail->Body = "<p>Hi $name,</p><p>We're sorry to inform you that the event you registered for has ended and no seats became available.</p>";

            $mail->send();

            $conn->query("UPDATE waiting_list SET status='expired' WHERE event_id = $event_id AND attendee_id = $attendee_id");
        } catch (Exception $e) {
            error_log("Expiration mail failed: " . $mail->ErrorInfo);
        }
    }
}
?>

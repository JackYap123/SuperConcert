<?php
session_start();
require '../inc/config.php';

if (!isset($_SESSION['organiser_email']))
{
    header("Location: organiser_login.php");
    exit();
}
$organiser_id = $_SESSION['organiser_id'];
$is_first_login = $_SESSION['is_first_login'] ?? false;

// Check if the organiser has existing events
$stmt = $conn->prepare("SELECT COUNT(*) AS event_count FROM event WHERE organizer_id = ?");
$stmt->bind_param("i", $organiser_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$event_count = $row['event_count'] ?? 0;
$stmt->close();
$show_modal = (!$is_first_login && $event_count == 0);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password']))
{
    $response = ["success" => false, "message" => "Unknown error occurred."];

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password)
    {
        $email = $_SESSION['organiser_email'];

        $stmt = $conn->prepare("UPDATE Organisers SET password = ?, is_first_login = 0 WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);

        if ($stmt->execute())
        {
            $_SESSION['is_first_login'] = 0; // 
            $response["success"] = true;
            $response["message"] = "Password updated successfully!";
        }
        else
        {
            $response["message"] = "Error updating password. Please try again.";
        }
        $stmt->close();
    }
    else
    {
        $response["message"] = "Passwords do not match. Please try again.";
    }

    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/Dashboard.css">
    <link rel="icon" type="image/x-icon" href="../img/Logo.webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
    <style>

    </style>
</head>

<body>
    <?php
        include "../inc/sidebar.php";
    ?>
    <?php if ($show_modal): ?>
        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventModalLabel">No Events Found</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>You have not created any events yet. Please create an event to continue.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="eventCreation.php" class="btn btn-primary">Create Event</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="content">
        <div class="header">
            <h1>Dashboard</h1>
        </div>

        <div class="dashboard">
            <a href="eventCreation.php" class="card-link">
                <div class="card">
                    <i class="fas fa-calendar-plus fa-3x" style="color: black;"></i>
                    <h2>Create Events</h2>
                    <p>Manage and set up new events</p>
                </div>
            </a>

            <a href="browseEvent.php" class="card-link">
                <div class="card">
                    <i class="fas fa-magnifying-glass fa-3x" style="color: black;"></i>
                    <h2>Browse Events</h2>
                    <p>Browse through existing events</p>
                </div>
            </a>

            <a href="ticket_setup.php" class="card-link">
                <div class="card">
                    <i class="fas fa-ticket-alt fa-3x" style="color: black;"></i>
                    <h2>Ticket Setup</h2>
                    <p>Configure ticketing options</p>
                </div>
            </a>

            <a href="waiting_list.php" class="card-link">
                <div class="card">
                    <i class="fas fa-users fa-3x" style="color: black;"></i>
                    <h2>Wait List</h2>
                    <p>Manage event waiting lists</p>
                </div>
            </a>

            <a href="create_promotion.php" class="card-link">
                <div class="card">
                    <i class="fas fa-bullhorn fa-3x" style="color: black;"></i>
                    <h2>Create Promotion</h2>
                    <p>Set up promotional campaigns</p>
                </div>
            </a>

        </div>
    </div>
    <?php if ($is_first_login): ?>
        <div id="change-password-modal"
            style="display: flex; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center;">
            <div class="modal-content" style="background: white; padding: 20px; border-radius: 5px; width: 300px;">
                <h2>Change Your Password</h2>
                <form id="change-password-form">
                    <input type="hidden" name="change_password" value="1">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" style="margin-top: 10px;">Update Password</button>
                </form>

                <p id="change-password-message" style="color: red; display: none; margin-top: 10px;"></p>
            </div>
        </div>
    <?php endif; ?>
    <script src="../javascript/Dashboard.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php if ($show_modal): ?>
            var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
            myModal.show();
        <?php endif; ?>
    });
    </script>


</html>
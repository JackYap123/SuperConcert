<?php
session_start();
require '../inc/config.php';

if (!isset($_SESSION['organiser_email']))
{
    header("Location: organiser_login.php");
    exit();
}

$is_first_login = $_SESSION['is_first_login'] ?? false;

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
    <title>Dashboard</title>
    <style>

    </style>
</head>

<body>
    <?php
    include "../inc/sidebar.php";
    ?>

    <div class="content">
        <div class="header">
            <h1>Dashboard</h1>
            <p>Administrator</p>
        </div>

        <div class="dashboard">
            <div class="card">
                <i class="fas fa-calendar-plus fa-3x" style="color: black;"></i>
                <h2>Create Event</h2>
                <p>Manage and set up new events</p>
            </div>
            <div class="card">
                <i class="fas fa-ticket-alt fa-3x" style="color: black;"></i>
                <h2>Ticket Setup</h2>
                <p>Configure ticketing options</p>
            </div>
            <div class="card">
                <i class="fas fa-users fa-3x" style="color: black;"></i>
                <h2>Waiting List</h2>
                <p>Manage event waiting lists</p>
            </div>
            <div class="card">
                <i class="fas fa-bullhorn fa-3x" style="color: black;"></i>
                <h2>Create Promotion</h2>
                <p>Set up promotional campaigns</p>
            </div>
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

</html>s
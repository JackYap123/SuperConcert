<?php
session_start();
require '../inc/config.php'; // Ensure this file connects to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (isset($_POST['login_organiser'])) {
        // Organiser Login
        $stmt = $conn->prepare("SELECT password FROM organisers WHERE email = ?");
    } elseif (isset($_POST['login_attendee'])) {
        // Attendee Login
        $stmt = $conn->prepare("SELECT password FROM attendees WHERE email = ?");
    } else {
        exit("<p style='color:red;'>Invalid login attempt.</p>");
    }

    // Bind parameter and execute query
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result( $db_password);
        $stmt->fetch();

        // **Simple string comparison (No hashing)**
        if ($password === $db_password) {
            if (isset($_POST['login_organiser'])) {
                $_SESSION['organiser_logged_in'] = true;
                $_SESSION['organiser_id'] = $user_id;
                $_SESSION['organiser_email'] = $email;
                session_write_close();
                header("Location: Dashboard.php");
                exit();
            } elseif (isset($_POST['login_attendee'])) {
                $_SESSION['attendee_logged_in'] = true;
                $_SESSION['attendee_id'] = $user_id;
                $_SESSION['attendee_email'] = $email;
                session_write_close();
                header("Location: Attendee_Home.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid password. Please try again.'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.location.href='login.php';</script>";
        exit();
    }

    $stmt->close();
}
$conn->close();
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/Register_Login.css">
    <link rel="icon" type="image/x-icon" href="../img/Logo.webp">
    <title>SuperConcert - Login</title>
    <style>
        .tab-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .tab-buttons button {
            background-color: #ccc;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 5px;
            transition: background 0.3s;
        }

        .tab-buttons button.active {
            background-color: #007bff;
            color: white;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>SuperConcert</h1>

        <!-- Tab Buttons -->
        <div class="tab-buttons">
            <button class="tab-link active" onclick="openTab('organiser')">Organiser Login</button>
            <button class="tab-link" onclick="openTab('attendee')">Attendee Login</button>
        </div>

        <!-- Organiser Login Form -->
        <div id="organiser" class="form-section active">
            <h2>Organiser Login</h2>
            <form action="organiser_login.php" method="POST">
                <input type="hidden" name="login_organiser" value="1">
                <div class="form-group">
                    <label for="organiser-email">Email</label>
                    <input type="email" id="organiser-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="organiser-password">Password</label>
                    <input type="password" id="organiser-password" name="password" placeholder="Enter your password"
                        required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>

        <!-- Attendee Login Form -->
        <div id="attendee" class="form-section">
            <h2>Attendee Login</h2>
            <form action="organiser_login.php" method="POST">
                <input type="hidden" name="login_attendee" value="1">
                <div class="form-group">
                    <label for="attendee-email">Email</label>
                    <input type="email" id="attendee-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="attendee-password">Password</label>
                    <input type="password" id="attendee-password" name="password" placeholder="Enter your password"
                        required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>

    </div>

    <script>
        function openTab(tabName) {
            document.querySelectorAll(".form-section").forEach(section => {
                section.classList.remove("active");
            });

            document.getElementById(tabName).classList.add("active");

            document.querySelectorAll(".tab-link").forEach(button => {
                button.classList.remove("active");
            });

            document.querySelector(`button[onclick="openTab('${tabName}')"]`).classList.add("active");
        }
    </script>

</body>

</html>
<?php
session_start();
require '../inc/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.'); window.location.href='attendee_register.php';</script>";
        exit();
    }

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT attendee_id FROM Attendee WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already registered. Please use another email.'); window.location.href='attendee_register.php';</script>";
        exit();
    }
    $stmt->close();

    // Insert new attendee
    $stmt = $conn->prepare("INSERT INTO Attendee (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now log in.'); window.location.href='organiser_Login.php';</script>";
    } else {
        echo "<script>alert('Error during registration. Please try again.'); window.location.href='attendee_register.php';</script>";
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
    <title>Attendee Registration</title>
    <link rel="stylesheet" href="../css/attendee_register.css">
</head>
<body>
    <div class="container">
        <h2>Attendee Registration</h2>
        <form action="attendee_register.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="organiser_Login.php">Login here</a>
        </div>
    </div>
</body>
</html>

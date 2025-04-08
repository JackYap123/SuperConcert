<?php
session_start();
require '../../inc/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0)
    {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        if ($password === $db_password) 
        {
            $_SESSION['is_admin'] = true;  
            $_SESSION['admin_email'] = $email; 
            header("Location: admin-dashboard.php");
            exit();
        }
        else
        {
            echo "<script>alert('Invalid password. Please try again.'); window.location.href='admin_Login.php';</script>";
        }
    }
    else
    {
        echo "<script>alert('No account found with that email..'); window.location.href='admin_Login.php';</script>";
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
    <link rel="stylesheet" href="../../css/Register_Login.css">
    <link rel="icon" type="image/x-icon" href="../img/Logo.webp">
    <title>SuperConcert</title>
</head>

<body>
    <div class="container">
        <h1>SuperConcert</h1>

        <!-- Login Form -->
        <div id="login-section" class="form-section active">
            <h2>Admin Login</h2>
            <form id="login-form" action="admin_Login.php" method="POST">
                <input type="hidden" name="login" value="1">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" placeholder="Enter your password"
                        required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
    <script src="../javascript/Register_Login.js"></script>
</body>

</html>
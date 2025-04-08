<?php
session_start();
require '../../inc/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST['register']))
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $organization_name = $_POST['organization_name'] ?? null;
        $default_password = substr(md5(uniqid()), 0, 8);

        $checkEmailQuery = "SELECT email FROM Organisers WHERE email = ?";
        $checkStmt = $conn->prepare($checkEmailQuery);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0)
        {
            echo "<script> 
                alert('The email address is already registered. Please use a different email or log in.');
                window.location.href='register-organizer.php';
            </script>";
        }
        else
        {
            $stmt = $conn->prepare("INSERT INTO Organisers (name, email, phone_number, organization_name, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone_number, $organization_name, $default_password);

            if ($stmt->execute())
            {
                $mail = new PHPMailer(true);
                try
                {
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
                    $mail->Subject = 'Welcome to SuperConcert!';
                    $mail->Body = "
                        <html>
                        <body style='font-family: Arial, sans-serif;'>
                        <h1 style='color: #007bff;'>Welcome to SuperConcert!</h1>
                        <p>Dear $name,</p>
                        <p>Your account has been created successfully. Below are your login details:</p>
                        <p>Email: $email</p>
                        <p>Password: <strong>$default_password</strong></p>
                        <p>Please log in and change your password for security purposes.</p>
                        <p><a href='http://localhost/SuperConcert/php/organiser_Login.php' style='color: #007bff;'>Login Now</a></p>
                        </body>
                        </html>
                    ";

                    $mail->send();
                    echo "<script> 
                            alert('Registration Successful! Your account has been created successfully. Please check your email for login details.');
                            window.location.href='register-organizer.php';
                        </script>";
                }
                catch (Exception $e)
                {
                    echo "<script> 
                    alert('Error in sending email: {$mail->ErrorInfo}');
                    window.location.href='register-organizer.php';
                  </script>";
                }
            }
            else
            {
                echo "<script> 
                    alert('Error: " . $stmt->error . "');
                    window.location.href='register-organizer.php';
                  </script>";
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/admin/admin-sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/admin/register-organizer.css">
    <link rel="icon" type="image/x-icon" href="../img/Logo.webp">
    <title>Register Organizer - SuperConcert</title>
    <style>

    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="../admin/admin-dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../admin/register-organizer.php" class="active"><i class="fas fa-user-plus"></i> Create Organizer</a></li>
            <li><a href="../admin/admin-report.php"><i class="fas fa-chart-bar"></i> Generate Report</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header class="main-header">
            <h2>Register Organizer</h2>
        </header>

        <div class="form-section">
            <form id="register-form" action="register-organizer.php" method="POST">
                <input type="hidden" name="register" value="1">

                <div class="form-group">
                    <label for="register-name">Full Name</label>
                    <input type="text" id="register-name" name="name" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="register-phone">Phone Number</label>
                    <input type="text" id="register-phone" name="phone_number" placeholder="Enter your phone number"
                        required>
                </div>

                <div class="form-group">
                    <label for="register-organization">Organization Name</label>
                    <input type="text" id="register-organization" name="organization_name"
                        placeholder="Enter organization name">
                </div>

                <button type="submit">Register</button>
            </form>
        </div>
    </div>
</body>

</html>
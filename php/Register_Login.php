<?php
session_start();
require '../inc/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// 定义管理员默认账号和密码
$admin_email = "admin@superconcert.com";
$admin_password = "Admin123"; // 默认密码

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // 登录逻辑
    if (isset($_POST['login']))
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($email === $admin_email && $password === $admin_password)
        {
            $_SESSION['is_admin'] = true;
            header("Location: admin_Dashboard.php");
            exit();
        }

        $stmt = $conn->prepare("SELECT password, is_first_login FROM Organisers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0)
        {
            $stmt->bind_result($db_password, $is_first_login);
            $stmt->fetch();
            if ($password === $db_password)
            {
                $_SESSION['email'] = $email;
                $_SESSION['is_first_login'] = $is_first_login; // 存储首次登录状态
                if($is_first_login){
                    header("Location: Dashboard.php?first_login=1"); // 跳转并提示用户更改密码
                }else{
                    header("Location: Dashboard.php");
                }
                exit();
            }
            else
            {
                echo "<p style='color:red;'>Invalid password. Please try again.</p>";
            }
        }
        else
        {
            echo "<p style='color:red;'>No account found with that email.</p>";
        }
        $stmt->close();
    }

    // 注册逻辑
    if (isset($_POST['register']))
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $organization_name = $_POST['organization_name'] ?? null;

        $checkEmailQuery = "SELECT email FROM Organisers WHERE email = ?";
        $checkStmt = $conn->prepare($checkEmailQuery);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0)
        {
            echo "<p style='color:red;'>The email address is already registered. Please use a different email or log in.</p>";
        }
        else
        {
            $stmt = $conn->prepare("INSERT INTO Organisers (name, email, phone_number, organization_name, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("ssss", $name, $email, $phone_number, $organization_name);

            if ($stmt->execute())
            {
                // 发送邮件给管理员
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
                        <body>
                        <h1>New Registration Request</h1>
                        <p>Name: $name</p>
                        <p>Email: $email</p>
                        <p>Organization: $organization_name</p>
                        <p>Please review the request in the Admin Dashboard.</p>
                        </body>
                        </html>
                    ";

                    $mail->send();
                    echo "<p style='color:green;'>Registration request submitted successfully.</p>";
                }
                catch (Exception $e)
                {
                    echo "<p style='color:red;'>Error in sending email to admin: {$mail->ErrorInfo}</p>";
                }
            }
            else
            {
                echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
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
    <link rel="stylesheet" href="../css/Register_Login.css">
    <link rel="icon" type="image/x-icon" href="../img/Logo.webp">
    <title>SuperConcert</title>
</head>

<body>
    <div class="container">
        <h1>SuperConcert</h1>

        <!-- Login Form -->
        <div id="login-section" class="form-section active">
            <h2>Login</h2>
            <form id="login-form" action="Register_Login.php" method="POST">
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
            <button class="toggle-button" onclick="toggleForm('register')">Don't have an account? Register</button>
        </div>

        <!-- Register Form -->
        <div id="register-section" class="form-section">
            <h2>Register</h2>
            <form id="register-form" action="Register_Login.php" method="POST">
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
            <button class="toggle-button" onclick="toggleForm('login')">Already have an account? Login</button>
        </div>
    </div>
    <script src="../javascript/Register_Login.js"></script>
</body>

</html>
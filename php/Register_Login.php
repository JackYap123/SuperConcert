<?php
// 引入 PHPMailer
session_start(); // 启用会话
require '../inc/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// 连接数据库
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 处理注册请求
    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $organization_name = $_POST['organization_name'] ?? null;

        // 检查邮箱是否已存在
        $checkEmailQuery = "SELECT email FROM Organisers WHERE email = ?";
        $checkStmt = $conn->prepare($checkEmailQuery);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            echo "<p style='color:red;'>The email address is already registered. Please use a different email or log in.</p>";
            $checkStmt->close();
        } else {
            // 生成随机密码（明文存储）
            $default_password = bin2hex(random_bytes(4));

            // 插入数据到数据库
            $stmt = $conn->prepare("INSERT INTO Organisers (name, email, phone_number, organization_name, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone_number, $organization_name, $default_password);

            if ($stmt->execute()) {
                // 使用 PHPMailer 发送电子邮件
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
                    $mail->Subject = 'Welcome to SuperConcert!';
                    $mail->Body = "
                        <html>
                        <body>
                        <h1>Welcome, $name!</h1>
                        <p>Thank you for registering with SuperConcert. Here are your login details:</p>
                        <p><strong>Email:</strong> $email</p>
                        <p><strong>Password:</strong> $default_password</p>
                        <p><a href='http://localhost/SuperConcert/php/Register_Login.php'>Login to SuperConcert</a></p>
                        <p>Thank you,<br>SuperConcert Team</p>
                        </body>
                        </html>
                    ";

                    $mail->send();
                    echo "<p style='color:green;'>Registration successful. Please check your email.</p>";
                } catch (Exception $e) {
                    echo "<p style='color:red;'>Registration successful, but email could not be sent. Error: {$mail->ErrorInfo}</p>";
                }
            } else {
                echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
    }

    // 处理登录请求
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // 管理员登录
        if ($email === "admin@example.com" && $password === "admin123") {
            session_start();
            $_SESSION['email'] = $email;
            header("Location: admin_dashboard.php");
            exit();
        }

        // 普通用户登录
        $stmt = $conn->prepare("SELECT password FROM Organisers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_password);
            $stmt->fetch();

            if ($password === $db_password) {
                session_start();
                $_SESSION['email'] = $email;
                header("Location: Dashboard.php");
                exit();
            } else {
                echo "<p style='color:red;'>Invalid password. Please try again.</p>";
            }
        } else {
            echo "<p style='color:red;'>No account found with that email.</p>";
        }

        $stmt->close();
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
    <style>

    </style>
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
                    <input type="password" id="login-password" name="password" placeholder="Enter your password" required>
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
                    <input type="text" id="register-phone" name="phone_number" placeholder="Enter your phone number" required>
                </div>
                <div class="form-group">
                    <label for="register-organization">Organization Name</label>
                    <input type="text" id="register-organization" name="organization_name" placeholder="Enter organization name">
                </div>
                <button type="submit">Register</button>
            </form>
            <button class="toggle-button" onclick="toggleForm('login')">Already have an account? Login</button>
        </div>
    </div>
    <script src="../javascript/Register_Login.js"></script>
</body>

</html>

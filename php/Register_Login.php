<?php
// 引入 PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';


// 连接数据库
$servername = "localhost";
$username = "root"; // 数据库用户名
$password = ""; // 数据库密码
$dbname = "SuperConcert";

$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 处理注册请求
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        // 生成随机密码
        $default_password = bin2hex(random_bytes(4));
        $hashed_password = password_hash($default_password, PASSWORD_BCRYPT);

        // 插入数据到数据库
        $stmt = $conn->prepare("INSERT INTO Organisers (name, email, phone_number, organization_name, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone_number, $organization_name, $hashed_password);

        if ($stmt->execute()) {
            // 使用 PHPMailer 发送电子邮件
            $mail = new PHPMailer(true);
            try {
                // 配置 SMTP 设置
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // 使用 Gmail SMTP 服务器
                $mail->SMTPAuth = true;
                $mail->Username = 'yapfongkiat53@gmail.com'; // 替换为你的 Gmail 地址
                $mail->Password = 'momfaxlauusnbnvl'; // 替换为你的 Gmail 应用密码
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // 发件人和收件人
                $mail->setFrom('yapfongkiat53@gmail.com', 'SuperConcert');
                $mail->addAddress($email, $name); // 收件人地址和名字

                // 邮件内容
                $mail->isHTML(true);
                $mail->Subject = 'Welcome to SuperConcert!';
                $mail->Body = "
                    <html>
                    <head>
                    <title>Welcome to SuperConcert</title>
                    </head>
                    <body>
                    <h1>Welcome, $name!</h1>
                    <p>Thank you for registering with SuperConcert. Here are your login details:</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Password:</strong> $default_password</p>
                    <p>Please log in using the link below and change your password upon first login:</p>
                    <p><a href='http://localhost/SuperConcert/php/Register_Login.php'>Login to SuperConcert</a></p>
                    <p>Thank you,<br>SuperConcert Team</p>
                    </body>
                    </html>
                ";

                $mail->send();
                echo '<p style="color:green;">Registration successful. Please check your email.</p>';
            } catch (Exception $e) {
                echo "<p style='color:red;'>Registration successful, but email could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
                $mail->SMTPDebug = SMTP::DEBUG_SERVER; // 启用调试模式

            }
        } else {
            echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
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
</head> 

<body>
    <div class="container">
        <h1 style="color: #ffd700; font-family: Arial, sans-serif; text-align: center; margin-bottom: 20px;">
            SuperConcert</h1>
        <!-- Login Section -->
        <div id="login-section" class="form-section active">
            <h2>Login</h2>
            <form id="login-form">
                <div class="form-group">
                    <label for="login-username">Email</label>
                    <input type="email" id="login-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" placeholder="Enter your password"
                        required>
                </div>
                <button type="submit">Login</button>
            </form>
            <button class="toggle-button" onclick="toggleForm('register')">Don’t have an account? Register</button>
        </div>

        <!-- Register Section -->
        <div id="register-section" class="form-section">
            <h2>Register</h2>
            <form id="register-form" action="Register_Login.php" method="POST">
                <div class="form-group">
                    <label for="register-name">Full Name</label>
                    <input type="text" id="register-name" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Organiser Email</label>
                    <input type="email" id="register-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="register-phone">Contact Information</label>
                    <input type="text" id="register-phone" name="phone_number" placeholder="Enter Your Phone Number"
                        required>
                </div>
                <div class="form-group">
                    <label for="register-organization">Organization Name</label>
                    <input type="text" id="register-organization" name="organization_name"
                        placeholder="Enter Organization Name">
                </div>
                <button type="submit">Register</button>
            </form>
            <button class="toggle-button" onclick="toggleForm('login')">Already have an account? Login</button>
        </div>
    </div>
    <script src="../javascript/Register_Login.js"></script>
</body>

</html>

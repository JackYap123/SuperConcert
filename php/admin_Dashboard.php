<?php
session_start();
require '../inc/config.php';

// 检查是否为Admin登录


// 引入PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// 处理Organiser注册
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_organiser']))
{
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

    if ($checkStmt->num_rows > 0)
    {
        $error_message = "The email address is already registered.";
        $checkStmt->close();
    }
    else
    {
        // 生成随机密码
        $default_password = bin2hex(random_bytes(4));

        // 插入到数据库
        $stmt = $conn->prepare("INSERT INTO Organisers (name, email, phone_number, organization_name, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone_number, $organization_name, $default_password);

        if ($stmt->execute())
        {
            // 发送邮件
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
                    <h1>Welcome, $name!</h1>
                    <p>Your account has been created by Admin. Here are your login details:</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Password:</strong> $default_password</p>
                    <p><a href='http://localhost/SuperConcert/php/Register_Login.php'>Login to SuperConcert</a></p>
                ";

                $mail->send();
                $success_message = "Organiser registered successfully and email sent.";
            }
            catch (Exception $e)
            {
                $error_message = "Registration successful, but email could not be sent. Error: " . $mail->ErrorInfo;
            }
        }
        else
        {
            $error_message = "Error registering organiser: " . $stmt->error;
        }
        $stmt->close();
    }
}

// 获取所有Organiser数据
$organisers = [];
$query = "SELECT id, name, email, phone_number, organization_name FROM Organisers";
$result = $conn->query($query);
if ($result)
{
    $organisers = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            background-color: #0d1b2a;
            color: white;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h1 {
            font-size: 24px;
            color: #f4d160;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar ul li a:hover {
            color: #f4d160;
        }

        .sidebar .logout {
            text-align: center;
            margin-top: auto;
        }

        .sidebar .logout a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            background-color: #f4d160;
            border-radius: 5px;
        }

        .sidebar .logout a:hover {
            background-color: #e4c150;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 2px solid #e4e4e4;
        }

        .main-header h2 {
            color: #0d1b2a;
        }

        .main-header button {
            background-color: #f4d160;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .main-header button:hover {
            background-color: #e4c150;
        }

        /* Table Section */
        .table-section {
            margin-top: 30px;
        }

        .table-section table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table-section table th,
        .table-section table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e4e4e4;
        }

        .table-section table th {
            background-color: #0d1b2a;
            color: white;
        }

        .table-section table tr:hover {
            background-color: #f9f9f9;
        }

        .table-section table .action-btn {
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
            text-decoration: none;
        }

        .table-section table .edit-btn {
            background-color: #4CAF50;
        }

        .table-section table .delete-btn {
            background-color: #f44336;
        }

        .table-section table .edit-btn:hover {
            background-color: #45a049;
        }

        .table-section table .delete-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h1>Admin Panel</h1>
        <ul>
            <li><a href="#"><i class="fas fa-users"></i> Organisers</a></li>
            <li><a href="#"><i class="fas fa-calendar-alt"></i> Events</a></li>
            <li><a href="#"><i class="fas fa-chart-bar"></i> Analytics</a></li>
            <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
        </ul>
        <div class="logout">
            <a href="#">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="main-header">
            <h2>Welcome, Admin</h2>
            <button>Add New Organiser</button>
        </div>

        <!-- Organisers Table -->
        <div class="table-section">
            <h3>Registered Organisers</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Organization</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($organisers)): ?>
                        <?php foreach ($organisers as $organiser): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($organiser['id']); ?></td>
                                <td><?php echo htmlspecialchars($organiser['name']); ?></td>
                                <td><?php echo htmlspecialchars($organiser['email']); ?></td>
                                <td><?php echo htmlspecialchars($organiser['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($organiser['organization_name']); ?></td>
                                <td>
                                    <a href="edit_organiser.php?id=<?php echo $organiser['id']; ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="delete_organiser.php?id=<?php echo $organiser['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No organisers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

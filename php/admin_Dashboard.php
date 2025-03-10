<?php
session_start();
require '../inc/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if (!isset($_SESSION['is_admin']))
{
    header("Location: admin_Login.php");
    exit();
}


// Fetch all organisers (Accepted and Pending only)
$query = "SELECT id, name, email, phone_number, organization_name FROM Organisers";
$result = $conn->query($query);
$organisers = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_Dashboard.css">
</head>

<body>
    <div class="sidebar">
        <h1>Admin Dashboard</h1>
        <ul>
            <li><a href="../php/admin_Dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../php/Register_Organizer.php"><i class="fas fa-users"></i> Create Organiser</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Generate Report</a></li>
        </ul>
        <div class="logout">
        </div>
    </div>

    <div class="main-content">
        <header class="main-header">
            <h2>Organiser Requests</h2>
        </header>

        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Organization</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($organisers as $organiser): ?>
                        <tr>
                            <td><?= htmlspecialchars($organiser['name']) ?></td>
                            <td><?= htmlspecialchars($organiser['email']) ?></td>
                            <td><?= htmlspecialchars($organiser['phone_number']) ?></td>
                            <td><?= htmlspecialchars($organiser['organization_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($organisers)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">No pending or accepted requests</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
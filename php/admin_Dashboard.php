<?php
session_start();
require '../inc/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if (!isset($_SESSION['is_admin']))
{
    header("Location: Register_Login.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id']))
{
    $action = $_GET['action'];
    $id = $_GET['id'];

    // Fetch organiser details
    $stmt = $conn->prepare("SELECT email, name FROM Organisers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($email, $name);
    $stmt->fetch();
    $stmt->close();

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

        if ($action === "accept")
        {
            $default_password = bin2hex(random_bytes(4));
            $updateStmt = $conn->prepare("UPDATE Organisers SET status = 'accepted', password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $default_password, $id);
            if ($updateStmt->execute())
            {
                $mail->Subject = 'Registration Approved';
                $mail->Body = "<h1>Welcome, $name!</h1>
                               <p>Your registration has been approved.</p>
                               <p><strong>Email:</strong> $email</p>
                               <p><strong>Password:</strong> $default_password</p>";
                $mail->send();
            }
            $updateStmt->close();
        }
        elseif ($action === "reject")
        {
            $mail->Subject = 'Registration Rejected';
            $mail->Body = "<h1>Dear $name,</h1>
                           <p>We regret to inform you that your registration request has been rejected.</p>";
            if ($mail->send())
            {
                $deleteStmt = $conn->prepare("DELETE FROM Organisers WHERE id = ?");
                $deleteStmt->bind_param("i", $id);
                $deleteStmt->execute();
                $deleteStmt->close();
            }
        }
    }
    catch (Exception $e)
    {
        echo "<p style='color:red;'>Error: {$mail->ErrorInfo}</p>";
    }
}

// Fetch all organisers (Accepted and Pending only)
$query = "SELECT id, name, email, phone_number, organization_name, status FROM Organisers WHERE status != 'rejected'";
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
            <li><a href="#"><i class="fas fa-users"></i> Manage Users</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
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
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($organisers as $organiser): ?>
                        <tr>
                            <td><?= htmlspecialchars($organiser['name']) ?></td>
                            <td><?= htmlspecialchars($organiser['email']) ?></td>
                            <td><?= htmlspecialchars($organiser['phone_number']) ?></td>
                            <td><?= htmlspecialchars($organiser['organization_name']) ?></td>
                            <td>
                                <span
                                    class="action-btn <?= ($organiser['status'] === 'accepted') ? 'accepted' : 'pending' ?>">
                                    <?= ucfirst($organiser['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($organiser['status'] === 'pending'): ?>
                                    <a href="?action=accept&id=<?= $organiser['id'] ?>" class="edit-btn">Accept</a>
                                    <a href="?action=reject&id=<?= $organiser['id'] ?>" class="delete-btn">Reject</a>
                                <?php else: ?>
                                    <span class="disabled-btn">No Action</span>
                                <?php endif; ?>
                            </td>
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
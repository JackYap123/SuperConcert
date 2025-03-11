<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SuperConcert"; // Make sure this is your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add user
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("INSERT INTO waitlist (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $stmt->close();
}

// Remove user
if (isset($_POST['remove_user'])) {
    $id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM waitlist WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Notify user (Dummy Example - Replace with real mail function if needed)
if (isset($_POST['notify_user'])) {
    $email = $_POST['user_email'];
    // mail($email, "Tickets Available", "Seats are now available!"); // Example mail function
    echo "<script>alert('Notification sent to $email');</script>";
}

// Fetch all users
$result = $conn->query("SELECT * FROM waitlist");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Waitlist Panel</title>
    <link rel="stylesheet" href="../css/admin_Waitlist.css"> <!-- Link to your CSS -->
</head>
<body>

    <!-- ✅ Back Button -->
    <button class="back-btn" onclick="window.location.href='dashboard.php'">← Back</button>

    <div class="container">
        <h2>Admin Waitlist Panel</h2>

        <!-- ✅ Add User Form -->
        <form method="POST">
            <div class="input-group">
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <button type="submit" name="add_user" class="waitlist-btn">Add User</button>
        </form>

        <!-- ✅ Waitlist Users Section -->
        <h3>Waitlist Users</h3>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="input-group" style="flex-direction: row; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <div>
                    <strong><?php echo htmlspecialchars($row['name']); ?></strong> - <?php echo htmlspecialchars($row['email']); ?>
                </div>
                <div>
                    <!-- Remove Button -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="remove_user" class="waitlist-btn" style="background: red; color: white; padding: 8px; font-size: 14px;">Remove</button>
                    </form>
                    <!-- Notify Button -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="user_email" value="<?php echo $row['email']; ?>">
                        <button type="submit" name="notify_user" class="waitlist-btn" style="background: yellow; color: black; padding: 8px; font-size: 14px;">Notify</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>

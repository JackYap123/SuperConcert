<?php
session_start();
require '../inc/config.php';

// 确保用户已登录
if (!isset($_SESSION['email'])) {
    header("Location: Register_Login.php");
    exit();
}

// 定义消息变量
$success_message = '';
$error_message = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $organization_name = !empty($_POST['organization_name']) ? $_POST['organization_name'] : NULL; // 可选字段
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 验证密码是否匹配
    if (!empty($new_password) && $new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $currentEmail = $_SESSION['email'];
        $passwordSql = !empty($new_password) ? ", password = ?" : "";

        $sql = "UPDATE organisers SET name = ?, email = ?, phone_number = ?, organization_name = ?" . $passwordSql . " WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $error_message = "SQL statement preparation failed: " . $conn->error;
        } else {
            if (!empty($new_password)) {
                $stmt->bind_param("ssssss", $name, $email, $phone_number, $organization_name, $new_password, $currentEmail);
            } else {
                $stmt->bind_param("sssss", $name, $email, $phone_number, $organization_name, $currentEmail);
            }

            if ($stmt->execute()) {
                $_SESSION['email'] = $email; // 更新 session 的 email
                $success_message = "Profile updated successfully!";
            } else {
                $error_message = "Error updating profile: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

// 获取用户数据
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT name, email, phone_number, organization_name FROM organisers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

// 确保在所有数据库操作完成后关闭连接
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="../css/Profile.css">
    <script>
        // 如果有成功消息，弹出提示框
        <?php if (!empty($success_message)) : ?>
            alert("<?php echo $success_message; ?>");
        <?php endif; ?>
    </script>
</head>

<body>
    <?php include "../inc/sidebar.php"; ?>

    <div class="profile-container">
        <h1>My Profile</h1>
        <?php if (!empty($error_message)) : ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number"
                    value="<?php echo htmlspecialchars($userData['phone_number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="organization_name">Organization Name</label>
                <input type="text" id="organization_name" name="organization_name"
                    value="<?php echo htmlspecialchars($userData['organization_name'] ?? ''); ?>">
            </div>
            <h2>Change Password</h2>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>

</html>

<?php
session_start();
require '../inc/config.php';

// 确保用户已登录
if (!isset($_SESSION['email'])) {
    echo "Unauthorized access.";
    exit();
}

// 获取 POST 数据
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$organization_name = $_POST['organization_name'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// 验证密码是否匹配
if (!empty($new_password) && $new_password !== $confirm_password) {
    echo "Passwords do not match.";
    exit();
}

// 检查数据库连接
if (!$conn) {
    echo "Database connection failed: " . mysqli_connect_error();
    exit();
}

// 更新用户数据
$currentEmail = $_SESSION['email'];
$passwordSql = !empty($new_password) ? ", password = ?" : "";

$sql = "UPDATE organisers SET name = ?, email = ?, phone_number = ?, organization_name = ?" . $passwordSql . " WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "SQL statement preparation failed: " . $conn->error;
    exit();
}

if (!empty($new_password)) {
    $stmt->bind_param("ssssss", $name, $email, $phone_number, $organization_name, $new_password, $currentEmail);
} else {
    $stmt->bind_param("sssss", $name, $email, $phone_number, $organization_name, $currentEmail);
}

if ($stmt->execute()) {
    // 更新 session 的 email 值
    $_SESSION['email'] = $email;

    // 成功消息
    echo "Profile updated successfully!";
} else {
    echo "Error updating profile: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['organiser_id']))
{
    die("Access denied. Please log in as an organizer.");
}

$organizer_id = $_SESSION['organiser_id']; // ✅ 直接从 SESSION 获取 ID

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $event_name = $_POST['eventName'];
    $event_date = $_POST['eventDate'];
    $event_time = $_POST['eventTime'];
    $event_duration = $_POST['eventDuration'];
    $event_description = $_POST['eventDescription'];

    // **处理图片上传**
    $targetPath = NULL; // 默认 NULL
    if (isset($_FILES["eventImage"]) && $_FILES["eventImage"]["error"] == 0)
    {
        $uploadDir = "uploads/";
        $imageName = basename($_FILES["eventImage"]["name"]);
        $targetPath = $uploadDir . $imageName;

        // 允许的图片格式
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        $imageFileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

        if (!in_array($imageFileType, $allowed_types))
        {
            die("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (!move_uploaded_file($_FILES["eventImage"]["tmp_name"], $targetPath))
        {
            die("Error uploading image.");
        }
    }

    // **检查 event_image 列是否存在**
    $stmt = $conn->prepare("INSERT INTO event (event_name, event_date, event_time, event_duration, event_description, organizer_id, file_name) VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($targetPath === NULL)
    {
        // 没有图片时，存 NULL
        $stmt->bind_param("sssssis", $event_name, $event_date, $event_time, $event_duration, $event_description, $organizer_id, $targetPath);
    }
    else
    {
        // 有图片时，存路径
        $stmt->bind_param("sssssis", $event_name, $event_date, $event_time, $event_duration, $event_description, $organizer_id, $targetPath);
    }

    if ($stmt->execute())
    {
        echo "<script>alert('Event created successfully!'); window.location.href='browseEvent.php';</script>";
    }
    else
    {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="../css/event.css">
    <?php include "../inc/sidebar.php"; ?>
</head>

<body>
    <div class="big-container">
        <div class="container">
            <h1>Create Event</h1>
            <form id="eventForm" action="" method="POST" enctype="multipart/form-data">


                <label for="eventName">Event Name:</label>
                <input type="name" id="eventName" name="eventName" required>

                <label for="eventDate">Date:</label>
                <input type="date" id="eventDate" name="eventDate" required>

                <label for="eventTime">Time:</label>
                <input type="time" id="eventTime" name="eventTime" required>

                <label for="eventDuration">Duration (Hours):</label>
                <input type="number" id="eventDuration" name="eventDuration" min="1" required>

                <label for="eventDescription">Description:</label>
                <textarea id="eventDescription" name="eventDescription" rows="3" required></textarea>

                <button type="submit">Create Event</button>
            </form>

            <a href="browseEvent.php" class="view-events">View Existing Events</a>
        </div>
    </div>

    <script src="../javascript/eventScript.js"></script>
</body>

</html>
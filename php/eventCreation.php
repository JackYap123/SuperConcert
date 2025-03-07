<?php
include 'conn_dB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['eventName'];
    $event_date = $_POST['eventDate'];
    $event_time = $_POST['eventTime'];
    $event_duration = $_POST['eventDuration'];
    $event_description = $_POST['eventDescription'];
    $organizer_id = 1;

    // Handle File Upload
    $file_name = ""; // Default empty string in case no file is uploaded
    if (isset($_FILES['eventCover']) && $_FILES['eventCover']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['eventCover']['name']); // Get original file name
        $target_directory = "../img/";
        $target_file = $target_directory . $file_name;

        // Ensure the uploads directory exists
        if (!is_dir($target_directory)) {
            mkdir($target_directory, 0777, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($_FILES['eventCover']['tmp_name'], $target_file)) {
            echo "Error uploading file.";
            exit;
        }
    }

    // INSERT into database, making sure `file_name` is correctly mapped
    $stmt = $conn->prepare("INSERT INTO event (event_name, event_date, event_time, event_duration, event_description, organizer_id, file_name) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $event_name, $event_date, $event_time, $event_duration, $event_description, $organizer_id, $file_name);

    if ($stmt->execute()) {
        echo "Event created successfully!";
    } else {
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
</head>
<body>
    <div class="container">
        <h1>Create Event</h1>
        <form id="eventForm" action="" method="POST" enctype="multipart/form-data">

            <label for="eventName">Event Name:</label>
            <input type="text" id="eventName" name="eventName" required>

            <label for="eventDate">Date:</label>
            <input type="date" id="eventDate" name="eventDate" required>

            <label for="eventTime">Time:</label>
            <input type="time" id="eventTime" name="eventTime" required>

            <label for="eventDuration">Duration (Hours):</label>
            <input type="number" id="eventDuration" name="eventDuration" min="1" required>

            <label for="eventDescription">Description:</label>
            <textarea id="eventDescription" name="eventDescription" rows="3" required></textarea>

            <label for="eventCover">Event Cover Image:</label>
            <input type="file" id="eventCover" name="eventCover" accept="image/*" required>

            <button type="submit">Create Event</button>
        </form>

        <a href="existsEvent.php" class="view-events">View Existing Events</a>
    </div>

    <script src="../javascript/eventScript.js"></script>
</body>
</html>

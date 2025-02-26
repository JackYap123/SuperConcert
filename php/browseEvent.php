<?php
include 'conn_dB.php';

$query = "SELECT event_id, event_name, event_date, event_time, event_duration, event_description, file_name FROM Event";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/browseEvent.css">

</head>
<body>
    <div class="sidebar">
        <h2 class="text-center text-white">Menu</h2>
        <a href="#">Dashboard</a>
        <a href="#">Create Event</a>
        <a href="#">Manage Tickets</a>
        <a href="#">Logout</a>
    </div>
    
    <div class="main-content">
        <h1 class="text-center mb-4">Event Dashboard</h1>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col">
                    <div class="card event-card">
                        <?php if (!empty($row['file_name'])) { ?>
                            <img src="uploads/<?php echo $row['file_name']; ?>" class="card-img-top event-image" alt="Event Image">
                        <?php } ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['event_name']); ?></h5>
                            <p class="card-text">
                                <strong>Date:</strong> <?php echo $row['event_date']; ?><br>
                                <strong>Time:</strong> <?php echo $row['event_time']; ?><br>
                                <strong>Duration:</strong> <?php echo $row['event_duration']; ?> hours<br>
                                <strong>Description:</strong> <?php echo htmlspecialchars($row['event_description']); ?>
                            </p>
                            <a href="#" class="btn btn-primary">Set Up Seats</a>
                            <button class="btn btn-danger" onclick="deleteEvent('<?php echo $row['event_id']; ?>')">Delete</button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteEvent(eventId) {
            if (confirm("Are you sure you want to delete this event?")) {
                window.location.href = 'deleteEvent.php?event_id=' + eventId;
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>

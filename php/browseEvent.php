<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['organiser_id']))
{
    die("Access denied. Please log in as an organizer.");
}

$organizer_id = $_SESSION['organiser_id'];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_event_id']))
{
    $event_id = $_POST['delete_event_id'];
    $query = "DELETE FROM Event WHERE event_id = ?";

    if ($stmt = $conn->prepare($query))
    {
        $stmt->bind_param("s", $event_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch events from DB
$sql = "SELECT event_id, event_name, event_date, event_time, event_duration, event_description, file_name 
        FROM Event WHERE organizer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $organizer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/browseEvent.css">

    <script>
        function confirmDelete(eventId) {
            if (confirm("Are you sure you want to delete this event?")) {
                document.getElementById("delete-form-" + eventId).submit();
            }
        }
    </script>
</head>

<body>
    <?php
    include "../inc/sidebar.php";
    ?>

    <?php if ($result->num_rows > 0): ?>

        <div class="main-content">
            <h1 class="text-center mb-4">Event Dashboard</h1>

            <div class="event-grid">
                <?php while ($row = $result->fetch_assoc())
                { ?>
                    <div class="card event-card">
                        <img src="../img/<?php echo htmlspecialchars($row['file_name']); ?>" class="event-image"
                            alt="Event Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['event_name']); ?></h5>
                            <p class="card-text">
                                <strong>Date:</strong> <?php echo $row['event_date']; ?><br>
                                <strong>Time:</strong> <?php echo $row['event_time']; ?><br>
                                <strong>Duration:</strong> <?php echo $row['event_duration']; ?> hours<br>
                                <strong>Description:</strong> <?php echo htmlspecialchars($row['event_description']); ?>
                            </p>
                            <a href="#" class="btn btn-primary">Set Up Seats</a>
                            <form method="POST" id="delete-form-<?php echo $row['event_id']; ?>" style="display:inline;">
                                <input type="hidden" name="delete_event_id" value="<?php echo $row['event_id']; ?>">
                                <button type="button" class="btn btn-danger"
                                    onclick="confirmDelete('<?php echo $row['event_id']; ?>')">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php else: ?>
            <p class="text-center">No events created yet.</p>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>
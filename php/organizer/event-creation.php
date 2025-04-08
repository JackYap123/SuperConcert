<?php
session_start();
include '../../inc/config.php';

if (!isset($_SESSION['organiser_id']))
{
    header("Location: ../organiser_login.php");
    exit();
}

$organizerId = $_SESSION['organiser_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $eventName = trim($_POST["eventName"]);
    $eventDate = $_POST["eventDate"];
    $eventTime = $_POST["eventTime"];
    $eventDuration = $_POST["eventDuration"];
    $eventDescription = $_POST["eventDescription"];

    $checkQuery = "SELECT COUNT(*) FROM event WHERE event_name = ? AND organizer_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("si", $eventName, $organizerId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0)
    {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var eventExistsModal = new bootstrap.Modal(document.getElementById('eventExistsModal'));
                eventExistsModal.show();
            });
        </script>";
    }
    else
    {
        $targetDir = "../../img/";
        $fileName = basename($_FILES["eventCover"]["name"]);
        $uniqueFileName = time() . "_" . $fileName;
        $targetFilePath = $targetDir . $uniqueFileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowedTypes = array("jpg", "jpeg", "png", "gif");

        if (in_array($fileType, $allowedTypes))
        {
            if (move_uploaded_file($_FILES["eventCover"]["tmp_name"], $targetFilePath))
            {
                $sql = "INSERT INTO event (organizer_id, event_name, event_date, event_time, event_duration, event_description, file_name) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issssss", $organizerId, $eventName, $eventDate, $eventTime, $eventDuration, $eventDescription, $uniqueFileName);

                if ($stmt->execute())
                {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                            successModal.show();
                        });
                    </script>";
                }
                else
                {
                    echo "<script>alert('Error: " . $stmt->error . "');</script>";
                }
            }
            else
            {
                echo "<script>alert('File upload failed.');</script>";
            }
        }
        else
        {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="../../css/organizer/event-creation.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../css/organizer/organizer-sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>

<body>
    <!-- Success Modal -->
    <div id="successModal" class="modal fade" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Event created successfully!</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Exists Modal -->
    <div id="eventExistsModal" class="modal fade" tabindex="-1" aria-labelledby="eventExistsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventExistsModalLabel">Event Name Taken</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Event name already exists for your account. Please choose another.</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->

    <div class="sidebar">
        <h2>SuperConcert</h2>
        <ul>

            <li><a href="../organizer/dashboard.php" ><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="../organizer/event-creation.php"class="active"><i class="fas fa-calendar-plus"></i> Create Event</a></li>
            <li><a href="../organizer/browse-event.php"><i class="fas fa-magnifying-glass"></i> Browse Events</a></li>
            <li><a href="../organizer/select-event.php"><i class="fas fa-ticket-alt"></i> Ticket Setup</a></li>
            <li><a href="../organizer/analysis-report.php"><i class="fas fa-book"></i> Analysis Report</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1 class="text-center">Create Event</h1>
        </div>
        <div class="container">
            <?php if (isset($errorMsg)): ?>
                <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
            <?php endif; ?>

            <form action="event-creation.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="eventName" class="form-label">Event Name:</label>
                    <input type="text" id="eventName" name="eventName" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="eventDate" class="form-label">Date:</label>
                    <input type="date" id="eventDate" name="eventDate" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="eventTime" class="form-label">Time:</label>
                    <input type="time" id="eventTime" name="eventTime" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="eventDuration" class="form-label">Duration (Hours):</label>
                    <input type="number" id="eventDuration" name="eventDuration" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="eventDescription" class="form-label">Description:</label>
                    <textarea id="eventDescription" name="eventDescription" class="form-control" rows="3"
                        required></textarea>
                </div>

                <div class="mb-3">
                    <label for="eventCover" class="form-label">Event Cover Image:</label>
                    <input type="file" id="eventCover" name="eventCover" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Create Event</button>
            </form>

            <a href="browse-event.php" class="btn btn-secondary w-100 mt-3">View Existing Events</a>
        </div>
    </div>

    <script src="../javascript/eventScript.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            flatpickr("#eventDate", {
                dateFormat: "Y-m-d",
                disableMobile: "true",
                static: true,
                altInput: true,
                altFormat: "F j, Y",
                theme: "material_blue"
            });
        });
    </script>
</body>

</html>
<?php
session_start();
include '../../inc/config.php';

if (!isset($_SESSION['organiser_id']))
{
    header("Location: ../organiser_Login.php");
    exit();
}

$organiser_id = $_SESSION['organiser_id'];

// Fetch all events with full promotion info
$query = "SELECT event_id, event_name, vip_price, regular_price, economy_price, promo_code, promo_discount, promo_limit FROM event WHERE organizer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $organiser_id);
$stmt->execute();
$result = $stmt->get_result();

$eventPrices = [];
while ($row = $result->fetch_assoc())
{
    $event_id = $row['event_id'];
    $eventPrices[$event_id] = [
        'event_name' => $row['event_name'],
        'vip_price' => $row['vip_price'],
        'regular_price' => $row['regular_price'],
        'economy_price' => $row['economy_price'],
        'promo_code' => $row['promo_code'],
        'promo_discount' => $row['promo_discount'],
        'promo_limit' => $row['promo_limit']
    ];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (!empty($_POST['event_id']) && isset($_POST['vip_price'], $_POST['regular_price'], $_POST['economy_price']))
    {
        $event_id = $_POST['event_id'];
        $vip_price = $_POST['vip_price'];
        $regular_price = $_POST['regular_price'];
        $economy_price = $_POST['economy_price'];
        $promo_code = $_POST['promo_code'] ?? null;
        $promo_discount = $_POST['promo_discount'] ?? null;
        $promo_limit = $_POST['promo_limit'] ?? null;

        $updateQuery = "UPDATE event SET vip_price = ?, regular_price = ?, economy_price = ? , promo_code = ?, promo_discount = ?, promo_limit = ? WHERE event_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("dddsdii", $vip_price, $regular_price, $economy_price, $promo_code, $promo_discount, $promo_limit, $event_id);

        if ($stmt->execute())
        {
            $_SESSION['selected_event'] = $event_id;
            $_SESSION['vip_price'] = $vip_price;
            $_SESSION['regular_price'] = $regular_price;
            $_SESSION['economy_price'] = $economy_price;

            header("Location: ticket-setup.php");
            exit();
        }
        else
        {
            $error = "Failed to update event promotion.";
        }
    }
    else
    {
        $error = "Please select an event and enter ticket prices.";
    }
}
?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/organizer/select-event.css">
    <link rel="stylesheet" href="../../css/organizer/organizer-sidebar.css">
    <title>Choose Event</title>
</head>

<body>
    <div class="sidebar">
        <h2>SuperConcert</h2>
        <ul>

            <li><a href="../organizer/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="../organizer/event-creation.php"><i class="fas fa-calendar-plus"></i> Create Event</a></li>
            <li><a href="../organizer/browse-event.php" ><i class="fas fa-magnifying-glass"></i> Browse Events</a></li>
            <li><a href="../organizer/select-event.php" class="active"><i class="fas fa-ticket-alt"></i> Ticket Setup</a></li>
            <li><a href="../organizer/analysis-report.php"><i class="fas fa-book"></i> Analysis Report</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="header">
            <h1>Choose Event</h1>
        </div>
        <div class="container">
            <?php if (!empty($error))
                echo "<p class='error'>$error</p>"; ?>
            <form method="post">
                <label for="event">Select Event:</label>
                <select name="event_id" id="event" required>
                    <option value="">Please Select...</option>
                    <?php foreach ($eventPrices as $id => $event): ?>
                        <option value="<?= $id ?>"><?= htmlspecialchars($event['event_name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="vip_price">VIP Ticket Price:</label>
                <input type="number" step="0.01" name="vip_price" id="vip_price" placeholder="VIP Ticket Price"
                    required>

                <label for="regular_price">Regular Ticket Price:</label>
                <input type="number" step="0.01" name="regular_price" id="regular_price"
                    placeholder="Regular Ticket Price" required>

                <label for="economy_price">Economy Ticket Price:</label>
                <input type="number" step="0.01" name="economy_price" id="economy_price"
                    placeholder="Economy Ticket Price" required>

                <label for="promo_code">Promotion Code:</label>
                <input type="text" name="promo_code" id="promo_code" placeholder="e.g. EARLYBIRD">

                <label for="promo_discount">Discount (%) :</label>
                <input type="number" step="0.01" min="0" max="100" name="promo_discount" id="promo_discount"
                    placeholder="e.g. 15">

                <label for="promo_limit">Promo Usage Limit:</label>
                <input type="number" min="0" name="promo_limit" id="promo_limit" placeholder="e.g. 100">

                <button type="submit">Move to Seat Management</button>
            </form>
        </div>
    </div>

    <script>
        const eventPrices = <?= json_encode($eventPrices) ?>;

        document.getElementById('event').addEventListener('change', function () {
            const eventId = this.value;
            if (eventId && eventPrices[eventId]) {
                const e = eventPrices[eventId];
                document.getElementById('vip_price').value = e.vip_price || '';
                document.getElementById('regular_price').value = e.regular_price || '';
                document.getElementById('economy_price').value = e.economy_price || '';
                document.getElementById('promo_code').value = e.promo_code || '';
                document.getElementById('promo_discount').value = e.promo_discount || '';
                document.getElementById('promo_limit').value = e.promo_limit || '';
            } else {
                ['vip_price', 'regular_price', 'economy_price', 'promo_code', 'promo_discount', 'promo_limit']
                    .forEach(id => document.getElementById(id).value = '');
            }
        });

        const form = document.querySelector("form");
        form.addEventListener("submit", function (e) {
            const vip = parseFloat(document.getElementById("vip_price").value) || 0;
            const reg = parseFloat(document.getElementById("regular_price").value) || 0;
            const eco = parseFloat(document.getElementById("economy_price").value) || 0;

            localStorage.setItem("defaultPrices", JSON.stringify({
                VIP: vip,
                Regular: reg,
                Economy: eco
            }));
        });

    </script>
</body>

</html>
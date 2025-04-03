<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['organiser_id']))
{
    header("Location: organiser_Login.php");
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

            header("Location: ticket_setup.php");
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
    <title>Choose Event</title>
    <style>
        h2 {
            color: gold;
        }

        body {
            display: flex;
            background-color: #001f3f;
            font-family: Arial, sans-serif;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #222;
            color: white;
        }

        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            width: 360px;
            text-align: center;
        }

        select,
        input,
        button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <?php include "../inc/sidebar.php"; ?>
    </div>
    <div class="content">
        <div class="container">
            <h2>Choose Event</h2>
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
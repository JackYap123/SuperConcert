<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in']) {
    header("Location: organiser_login.php");
    exit();
}
if (!isset($_SESSION['pending_payment'])) {
    echo "<script>alert('Missing seat selection.'); window.location.href = 'attendee_dashboard.php';</script>";
    exit();
}

$event_id = $_SESSION['pending_payment']['event_id'];
$seatIDs = explode(',', $_SESSION['pending_payment']['selected_seats']);

// Fetch seats from event_seats for validation and pricing
$placeholders = implode(',', array_fill(0, count($seatIDs), '?'));
$types = str_repeat('s', count($seatIDs));

$sql = "SELECT * FROM event_seats WHERE event_id = ? AND seat_number IN ($placeholders)";
$stmt = $conn->prepare($sql);
$bindParams = array_merge([$event_id], $seatIDs);
$stmt->bind_param('i' . $types, ...$bindParams);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $seats[] = $row;
    $total += $row['price'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #001f3f, #003366);
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 850px;
            margin: 60px auto;
            background: rgba(17, 34, 51, 0.95);
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
        }

        h2 {
            font-size: 2.2rem;
            margin-bottom: 30px;
            text-shadow: 2px 2px #000;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table th {
            background-color: #004080;
            color: #fff;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .btn-pay {
            background: linear-gradient(90deg, #00c853, #64dd17);
            color: #fff;
            font-size: 1.2rem;
            padding: 14px 40px;
            border-radius: 10px;
            border: none;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0, 200, 83, 0.4);
            transition: all 0.3s ease-in-out;
        }

        .btn-pay:hover {
            background: linear-gradient(90deg, #00b74a, #00e676);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 255, 128, 0.4);
        }

        .total-box {
            background: #005288;
            padding: 18px;
            border-radius: 12px;
            color: #ffffff;
            font-size: 1.2rem;
            text-align: right;
            margin-top: 25px;
            box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.1);
        }

        .glow-text {
            color: #ffc107;
            text-shadow: 0 0 5px #ffc107, 0 0 10px #ffc107, 0 0 20px #ffeb3b;
        }

        .payment-methods {
            margin: 30px 0 20px;
            text-align: center;
        }

        .payment-methods label {
            display: inline-block;
            margin: 0 20px;
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center glow-text">✨ Review Your Seats & Make Payment ✨</h2>
        <table class="table table-striped table-bordered mt-4">
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Seat</th>
                    <th>Category</th>
                    <th>Price (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($seats as $seat): ?>
                    <tr>
                        <td><?= htmlspecialchars($seat['row_label']) ?></td>
                        <td><?= htmlspecialchars($seat['seat_number']) ?></td>
                        <td><?= htmlspecialchars($seat['category']) ?></td>
                        <td><?= number_format($seat['price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-box">
            <strong>Total to Pay:</strong> RM <?= number_format($total, 2) ?>
        </div>

        <form action="process_payment.php" method="post" class="text-center mt-4">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            <input type="hidden" name="selected_seats" value="<?= htmlspecialchars($_SESSION['pending_payment']['selected_seats']) ?>">
            <input type="hidden" name="total" value="<?= $total ?>">

            <div class="payment-methods">
                <h5 class="text-warning">Select Payment Method:</h5>
                <label><input type="radio" name="payment_method" value="Visa" required> Visa</label>
                <label><input type="radio" name="payment_method" value="TNG"> TNG</label>
            </div>

            <button type="submit" class="btn btn-pay">Proceed to Payment</button>
        </form>
    </div>
</body>

</html>

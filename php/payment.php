<?php
// payment.php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    echo "<script>alert('Please login first.'); window.location.href='organiser_login.php';</script>";
    exit();
}

if (!isset($_SESSION['pending_payment']))
{
    echo "<script>alert('Missing seat selection.'); window.location.href='attendee_dashboard.php';</script>";
    exit();
}

$event_id = $_SESSION['pending_payment']['event_id'];
$seatIDs = explode(',', $_SESSION['pending_payment']['selected_seats']);

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
while ($row = $result->fetch_assoc())
{
    $seats[] = $row;
    $total += $row['price'];
}
$stmt->close();

$total_before_discount = $total;
$discount = 0;
$discount_code = isset($_POST['discount_code']) ? trim($_POST['discount_code']) : '';
$discount_error = '';

// Fetch promo code and discount percentage from event table
$promoQuery = $conn->prepare("SELECT promo_code, promo_discount FROM event WHERE event_id = ?");
$promoQuery->bind_param("i", $event_id);
$promoQuery->execute();
$promoResult = $promoQuery->get_result();
$promoData = $promoResult->fetch_assoc();
$promoQuery->close();

if (!empty($discount_code))
{
    if (strcasecmp($discount_code, $promoData['promo_code']) === 0)
    {
        $discount = $promoData['promo_discount'];
        $total = $total * ((100 - $discount) / 100);
    }
    else
    {
        $discount_error = "❌ Invalid discount code.";
    }
}
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
        <h2 class="text-center text-warning">Review & Pay</h2>
        <table class="table table-bordered mt-4">
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
            <tfoot>
                <?php if ($discount > 0): ?>
                    <tr>
                        <th colspan="3">Discount (<?= $discount ?>%)</th>
                        <th>- RM <?= number_format($total_before_discount * $discount / 100, 2) ?></th>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th colspan="3">Total</th>
                    <th>RM <?= number_format($total, 2) ?></th>
                </tr>
            </tfoot>
        </table>

        <?php if ($discount_error): ?>
            <div class="error"><?= $discount_error ?></div>
        <?php endif; ?>

        <form action="payment.php" method="post" class="mb-4">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            <input type="hidden" name="selected_seats"
                value="<?= htmlspecialchars($_SESSION['pending_payment']['selected_seats']) ?>">
            <div class="mb-3">
                <label for="discount_code" class="form-label">Enter Discount Code</label>
                <input type="text" class="form-control" id="discount_code" name="discount_code"
                    value="<?= htmlspecialchars($discount_code) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Apply Code</button>
        </form>

        <form action="process_payment.php" method="post">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            <input type="hidden" name="selected_seats"
                value="<?= htmlspecialchars($_SESSION['pending_payment']['selected_seats']) ?>">
            <input type="hidden" name="total" value="<?= $total ?>">
            <input type="hidden" name="discount_code" value="<?= htmlspecialchars($discount_code) ?>">

            <div class="mb-3">
                <label for="payment_method" class="form-label">Select Payment Method</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="">-- Select --</option>
                    <option value="Visa">Visa</option>
                    <option value="TNG">Touch 'n Go</option>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">Pay Now</button>
            </div>
        </form>
    </div>
</body>

</html>
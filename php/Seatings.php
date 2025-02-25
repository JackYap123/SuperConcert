<?php
include 'conn_dB.php';

// Handle POST request to insert data into the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['event_id']) || !isset($data['seats'])) {
        echo json_encode(["success" => false, "message" => "Invalid input data"]);
        exit;
    }

    $event_id = $data['event_id'];
    $seats = $data['seats'];

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO Ticket (ticket_id, event_id, categories, prices, seat) VALUES (?, ?, ?, ?, ?)");

        foreach ($seats as $seat) {
            $ticket_id = uniqid(); // Generate a unique ID
            $stmt->bind_param("sssss", $ticket_id, $event_id, $seat['category'], $seat['price'], $seat['seat_id']);
            $stmt->execute();
        }

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Seats booked successfully"]);
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Booking System</title>
    <link rel="stylesheet" href="../css/seating.css">
</head>
<body>

    <h1>Auditorium Seat Booking</h1>

    <div class="selection-container">
        <label for="selectMode">Selection Mode:</label>
        <select id="selectMode">
            <option value="single">Single Seat</option>
            <option value="row">Entire Row</option>
        </select>
    
        <label for="seatCategory">Seat Category:</label>
        <select id="seatCategory">
            <option value="available">Public</option>
            <option value="vip">VIP</option>
            <option value="balcony">Balcony</option>
        </select>
    
        <label for="seatPrice">Set Price:</label>
        <input type="number" id="seatPrice" placeholder="Enter price">
    </div>
    
    <button id="setCategoryButton">Set Category</button>

    <div class="legend">
        <div><span class="seat available"></span> Normal</div>
        <div><span class="seat vip"></span> VIP</div>
        <div><span class="seat balcony"></span> Balcony</div>
    </div>

    <div class="stage">Stage</div>

    <div class="cinema-container">
        <div class="row-labels">
            <?php
            $rows = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K"];
            foreach ($rows as $row) {
                echo "<span>$row</span>";
            }
            ?>
        </div>
        <div class="seats" id="seatingArea"></div>
    </div>

    <button id="purchaseButton">Purchase Tickets</button>

    <script src="../javascript/seatScript.js"></script>

</body>
</html>

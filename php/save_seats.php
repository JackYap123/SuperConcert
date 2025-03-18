<?php
include '../inc/config.php'; 

// Get JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo "No data received.";
    exit;
}

try {
    $pdo->beginTransaction(); // Start a transaction

    $stmt = $pdo->prepare("INSERT INTO seats (event_id, seat_row, seat_number, category, price)
                           VALUES (:event_id, :seat_row, :seat_number, :category, :price)
                           ON DUPLICATE KEY UPDATE category = VALUES(category), price = VALUES(price)");

    foreach ($data as $seat) {
        $stmt->execute([
            ':event_id' => $seat['event_id'],
            ':seat_row' => $seat['seat_row'],
            ':seat_number' => $seat['seat_number'],
            ':category' => $seat['category'],
            ':price' => $seat['price']
        ]);
    }

    $pdo->commit();
    echo "Seats successfully saved.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error saving seats: " . $e->getMessage();
}
?>

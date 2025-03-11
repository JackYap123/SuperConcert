<?php
// ticket_setup.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketTypes = $_POST['ticketType'] ?? [];
    $prices = $_POST['price'] ?? [];
    $seats = $_POST['seats'] ?? [];
    $discountCodes = $_POST['discountCode'] ?? [];
    $discountValues = $_POST['discountValue'] ?? [];

    $tickets = [];
    for ($i = 0; $i < count($ticketTypes); $i++) {
        $tickets[] = [
            'type' => $ticketTypes[$i],
            'price' => $prices[$i],
            'seats' => $seats[$i]
        ];
    }

    $discounts = [];
    for ($i = 0; $i < count($discountCodes); $i++) {
        $discounts[$discountCodes[$i]] = $discountValues[$i];
    }

    // Save to JSON file or database (simulating file storage here)
    file_put_contents('tickets.json', json_encode(['tickets' => $tickets, 'discounts' => $discounts]));
    echo "<p>Ticket setup saved successfully!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Setup</title>
    <link rel="stylesheet" href="styles.css">
    <script defer src="script.js"></script>
</head>
<body>
    <h2>Ticket Setup</h2>
    <form method="POST" action="">
        <div id="ticket-container">
            <div class="ticket-row">
                <input type="text" name="ticketType[]" placeholder="Ticket Type (e.g., VIP)" required>
                <input type="number" name="price[]" placeholder="Price" required>
                <input type="number" name="seats[]" placeholder="Seats Available" required>
                <button type="button" onclick="removeTicket(this)">Remove</button>
            </div>
        </div>
        <button type="button" onclick="addTicket()">Add Ticket</button>
        
        <h3>Promotional Discounts</h3>
        <div id="discount-container">
            <div class="discount-row">
                <input type="text" name="discountCode[]" placeholder="Discount Code">
                <input type="number" name="discountValue[]" placeholder="Discount (%)">
                <button type="button" onclick="removeDiscount(this)">Remove</button>
            </div>
        </div>
        <button type="button" onclick="addDiscount()">Add Discount</button>
        
        <br><br>
        <button type="submit">Save Ticket Setup</button>
    </form>
</body>
</html>

<style>
/* styles.css */
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    padding: 20px;
}
.ticket-row, .discount-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}
button {
    background-color: blue;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}
button:hover {
    background-color: darkblue;
}
</style>

<script>
// script.js
function addTicket() {
    const container = document.getElementById('ticket-container');
    const div = document.createElement('div');
    div.classList.add('ticket-row');
    div.innerHTML = `
        <input type="text" name="ticketType[]" placeholder="Ticket Type (e.g., VIP)" required>
        <input type="number" name="price[]" placeholder="Price" required>
        <input type="number" name="seats[]" placeholder="Seats Available" required>
        <button type="button" onclick="removeTicket(this)">Remove</button>
    `;
    container.appendChild(div);
}

function removeTicket(button) {
    button.parentElement.remove();
}

function addDiscount() {
    const container = document.getElementById('discount-container');
    const div = document.createElement('div');
    div.classList.add('discount-row');
    div.innerHTML = `
        <input type="text" name="discountCode[]" placeholder="Discount Code">
        <input type="number" name="discountValue[]" placeholder="Discount (%)">
        <button type="button" onclick="removeDiscount(this)">Remove</button>
    `;
    container.appendChild(div);
}

function removeDiscount(button) {
    button.parentElement.remove();
}
</script>

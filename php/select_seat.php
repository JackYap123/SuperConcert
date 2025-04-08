<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['attendee_logged_in']) || !$_SESSION['attendee_logged_in'])
{
    header("Location: organiser_login.php");
    exit();
}

if (!isset($_GET['event_id']))
{
    echo "<p style='color: red;'>No event selected.</p>";
    exit();
}

$event_id = intval($_GET['event_id']);

// Get organiser-approved seats
$query = "SELECT * FROM event_seats WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$availableSeats = [];
while ($row = $result->fetch_assoc())
{
    $availableSeats[$row['seat_number']] = [
        'row' => $row['row_label'],
        'category' => $row['category'],
        'price' => $row['price']
    ];
}
$stmt->close();

// Get already booked seats
$bookedSeats = [];
$bookingQuery = "SELECT seat_number FROM bookings WHERE event_id = ?";
$bookingStmt = $conn->prepare($bookingQuery);
$bookingStmt->bind_param("i", $event_id);
$bookingStmt->execute();
$bookingResult = $bookingStmt->get_result();
while ($b = $bookingResult->fetch_assoc())
{
    $bookedSeats[] = $b['seat_number'];
}
$bookingStmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Select Seat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 0;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            background-color: #001f3f;
            color: #fff;
        }

        .sidebar {
            width: 250px;
            background-color: #222;
            height: 100vh;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        .sidebar h1 {
            font-size: 20px;
            color: gold;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: #ccc;
            text-decoration: none;
            font-size: 16px;
        }

        .sidebar ul li a:hover {
            color: #fff;
        }

        .sidebar .logout a {
            margin-top: 50px;
            display: block;
            color: red;
            text-decoration: none;
            font-weight: bold;
        }


        .container {
            text-align: center;
            flex: 1;
            padding: 30px;
            margin-left: 250px;
            /* 👈 Prevent overlap */
        }

        .row-container {
            display: flex;
            align-items: center;
        }


        .row {
            display: flex;
            align-items: center;
            margin: 2px;
        }

        .row-label {
            width: 30px;
            text-align: center;
            font-weight: bold;
        }

        .seat {
            width: 45px;
            height: 40px;
            background-color: white;
            border: 2px solid gold;
            line-height: 40px;
            font-weight: bold;
            color: #001f3f;
            border-radius: 5px;
            margin: 3px;
            padding: 0;
            cursor: pointer;
            user-select: none;
            transition: all 0.3s ease;

        }

        .vip {
            background-color: gold;
            color: black;
        }

        .regular {
            background-color: dodgerblue;
            color: white;
        }

        .economy {
            background-color: mediumseagreen;
            color: white;
        }

        .disabled {
            background-color: grey !important;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }

        .selected {
            outline: 3px solid red;
        }

        .selected-seat {
            background-color: red !important;
            color: white;
            pointer-events: none;
        }

        .legend {
            margin-top: 30px;
        }

        .legend span {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            margin-right: 10px;
            font-weight: bold;
        }

        .legend .vip {
            background: gold;
            color: black;
        }

        .legend .regular {
            background: dodgerblue;
            color: white;
        }

        .legend .economy {
            background: mediumseagreen;
            color: white;
        }

        .legend .disabled {
            background: grey;
            color: white;
        }

        .legend .selected-seat {
            background: red;
            color: white;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h1>Attendee Panel</h1>
        <ul>
            <li><a href="attendee_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="choose_event.php"><i class="fas fa-calendar-alt"></i> Choose Event</a></li>
            <li><a href="waiting_list.php"><i class="fas fa-clock"></i> Join Waiting List</a></li>
            <li><a href="attendee_waiting_list.php"><i class="fas fa-bell"></i> View Waiting List</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2 class="text-center" style="color: gold;">Select Your Seats</h2>
        <form method="post" action="confirm_seat.php">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">

            <h4 class="mt-4">Front Stage</h4>
            <div id="frontStage"></div>

            <h4 class="mt-4">Balcony</h4>
            <div id="balcony"></div>

            <div class="legend mt-4">
                <span class="vip">VIP</span>
                <span class="regular">Regular</span>
                <span class="economy">Economy</span>
                <span class="selected-seat">Booked</span>
                <span class="disabled">Disabled</span>
            </div>

            <div class="mt-4">
                <h5>Selected Seats:</h5>
                <ul id="selected-list"></ul>
                <input type="hidden" id="selectedSeatsInput" name="selected_seats">
                <button type="submit" class="btn btn-success mt-3">Confirm Selection</button>
            </div>
        </form>
    </div>

    <script>
        const availableSeats = <?= json_encode($availableSeats) ?>;
        const bookedSeats = <?= json_encode($bookedSeats) ?>;
        const selectedSeats = new Set();
        const selectedList = document.getElementById('selected-list');
        const selectedInput = document.getElementById('selectedSeatsInput');

        function createSeating(containerId, rows, leftSeats, midSeats, rightSeats, isBalcony) {
            const container = document.getElementById(containerId);
            const rowLabels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
            const balconyRowLabels = rowLabels.map(label => label + label);

            for (let r = 0; r < rows; r++) {
                const rowLabel = isBalcony ? balconyRowLabels[r] : rowLabels[r];
                const rowContainer = document.createElement("div");
                rowContainer.classList.add("row-container");

                const labelLeft = document.createElement("div");
                labelLeft.classList.add("row-label");
                labelLeft.innerText = rowLabel;

                const left = createSeatRow(leftSeats, r * (leftSeats + midSeats + rightSeats), rowLabel);
                const mid = createSeatRow(midSeats, r * (leftSeats + midSeats + rightSeats) + leftSeats, rowLabel);
                const right = createSeatRow(rightSeats, r * (leftSeats + midSeats + rightSeats) + leftSeats + midSeats, rowLabel);

                const labelRight = document.createElement("div");
                labelRight.classList.add("row-label");
                labelRight.innerText = rowLabel;

                rowContainer.append(labelLeft, left, mid, right, labelRight);
                container.appendChild(rowContainer);
            }
        }

        function createSeatRow(seats, start, rowLabel) {
            const row = document.createElement("div");
            row.classList.add("row");
            row.dataset.rowLabel = rowLabel;

            for (let i = 0; i < seats; i++) {
                const seatNum = start + i + 1;
                const seatID = `${rowLabel}${seatNum}`;
                const seat = document.createElement("div");

                seat.classList.add("seat");
                seat.innerText = seatNum;
                seat.dataset.seatId = seatID;

                if (bookedSeats.includes(seatID)) {
                    seat.classList.add("selected-seat");
                    seat.classList.add("disabled");
                } else if (availableSeats[seatID]) {
                    const category = availableSeats[seatID].category;
                    seat.classList.add(category.toLowerCase());
                    seat.dataset.category = category;
                    seat.dataset.price = availableSeats[seatID].price;

                    seat.addEventListener("click", () => {
                        if (selectedSeats.has(seatID)) {
                            selectedSeats.delete(seatID);
                            seat.classList.remove("selected");
                        } else {
                            selectedSeats.add(seatID);
                            seat.classList.add("selected");
                        }
                        updateSelectionDisplay();
                    });
                } else {
                    seat.classList.add("disabled");
                }

                row.appendChild(seat);
            }
            return row;
        }

        function updateSelectionDisplay() {
            selectedList.innerHTML = "";
            selectedInput.value = Array.from(selectedSeats).join(",");
            selectedSeats.forEach(seat => {
                const li = document.createElement("li");
                li.innerText = seat;
                selectedList.appendChild(li);
            });
        }

        window.onload = function () {
            createSeating("frontStage", 8, 10, 12, 10, false);
            createSeating("balcony", 6, 10, 12, 10, true);
        };
    </script>
</body>

</html>
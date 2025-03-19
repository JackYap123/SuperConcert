<?php
session_start();
include '../inc/config.php';

if (!isset($_SESSION['selected_event']))
{
    header("Location: select_event.php");
    exit();
}

// 选中的 event_id
$event_id = $_SESSION['selected_event'];
$vip_price = $_SESSION['vip_price'];
$regular_price = $_SESSION['regular_price'];
$economy_price = $_SESSION['economy_price'];
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    // Read JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data)
    {
        echo json_encode(["message" => "Invalid JSON input"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO seats (event_id, seat_row, seat_number, category, price) VALUES (?, ?, ?, ?, ?)");

    foreach ($data as $seat)
    {
        $stmt->bind_param("isisd", $seat['event_id'], $seat['rowLabel'], $seat['seatID'], $seat['category'], $seat['price']);
        $stmt->execute();
    }

    echo json_encode(["message" => "Seats saved successfully!"]);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Setup</title>
    <link rel="stylesheet" href="../css/ticket_setup.css">
</head>

<body>

    <!-- Seat Selection Section (Imported from seatings.php) -->
    <div class="seat-selection">
        <?php include 'seatings.php'; ?>
    </div>

    <!-- Ticket Pricing & Category Setup -->
    <h2>Manage Ticket Pricing</h2>
    <label>
        <input type="radio" name="selectionType" value="row" onclick="toggleSelection('row')"> Select Row
    </label>
    <label>
        <input type="radio" name="selectionType" value="single" onclick="toggleSelection('single')" checked> Select
        Single Seat
    </label>

    <table border="1">
        <thead>
            <tr>
                <th>Row</th>
                <th>Seat No</th>
                <th>Category</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="seatTable">
            <!-- Seats will be loaded dynamically -->
        </tbody>
    </table>

    <button id="saveButton" onclick="save()">Save Seats</button>

    <!-- Seat Editing Form (Hidden by Default) -->
    <div id="editForm" style="display:none;">
        <h3>Edit Seat</h3>
        <input type="hidden" id="seatID">
        <label>Category:</label>
        <select id="category">
            <option value="VIP">VIP</option>
            <option value="Regular">Regular</option>
            <option value="Economy">Economy</option>
        </select><br>
        <label>Price:</label>
        <input type="number" id="price"><br>
        <button onclick="saveSeat()">Save</button>
        <button onclick="cancelEdit()">Cancel</button>
    </div>

    <script>
        let selectionType = 'single';
        let selectedSeats = {}; // Stores individual seats {seatID: {row, seatNumber}}
        let selectedRows = {}; // Stores rows {rowLabel: [seatNumbers]}
        let defaultPrices = {
            VIP: <?= $vip_price ?>,
            Regular: <?= $regular_price ?>,
            Economy: <?= $economy_price ?>
        };

        function toggleSelection(type) {
            selectionType = type;
        }

        document.addEventListener("DOMContentLoaded", function () {
            let seats = document.querySelectorAll(".seat");

            seats.forEach(seat => {
                seat.addEventListener("click", function () {
                    let seatNumber = seat.dataset.seatNumber;
                    let rowLabel = seat.closest(".row").dataset.rowLabel;

                    if (selectionType === "single") {
                        toggleSeatSelection(seat, rowLabel, seatNumber);
                    } else if (selectionType === "row") {
                        selectRow(seat.parentElement, rowLabel);
                    }
                });
            });
        });

        function toggleSeatSelection(seat, rowLabel, seatNumber) {
            let seatId = seat.dataset.seatId;  // 确保使用 seatId 作为唯一标识符

            if (selectedSeats[seatId]) {
                // 取消选中
                seat.classList.remove("selected");
                delete selectedSeats[seatId];
            } else {
                // 选中
                seat.classList.add("selected");
                selectedSeats[seatId] = {
                    row: rowLabel,
                    seatNumber: seatNumber,
                    seatId: seatId
                };
            }

            updateSeatTable();
        }


        function selectRow(rowElement, rowLabel) {
            let seats = rowElement.querySelectorAll(".seat");
            let seatNumbers = [];

            // Check if the row is already selected
            let isRowSelected = selectedRows[rowLabel] !== undefined;

            if (isRowSelected) {
                // Unselect row
                seats.forEach(seat => {
                    let seatNumber = seat.dataset.seatNumber;
                    seat.classList.remove("selected");
                });

                delete selectedRows[rowLabel]; // Remove from selectedRows
            } else {
                // Select row
                seats.forEach(seat => {
                    let seatNumber = seat.dataset.seatNumber;
                    if (!seat.classList.contains("selected")) {
                        seat.classList.add("selected");
                        seatNumbers.push(seatNumber);
                    }
                });

                if (seatNumbers.length > 0) {
                    selectedRows[rowLabel] = seatNumbers;
                }
            }

            updateSeatTable();
        }

        function updateSeatTable() {
            let seatTable = document.getElementById("seatTable");
            seatTable.innerHTML = "";

            // Add Row-based selections
            Object.keys(selectedRows).forEach(rowLabel => {
                let seatNumbers = selectedRows[rowLabel];
                let seatRange = `${seatNumbers[0]} - ${seatNumbers[seatNumbers.length - 1]}`;

                let defaultCategory = "VIP";
                let defaultPrice = defaultPrices[defaultCategory];

                let row = `<tr id="row-${rowLabel}">
                <td>${rowLabel}</td>
                <td>${seatRange}</td>
                <td>
                    <select id="category-${rowLabel}" onchange="updatePrice('${rowLabel}')">
                        <option value="VIP">VIP</option>
                        <option value="Regular">Regular</option>
                        <option value="Economy">Economy</option>
                    </select>
                </td>
                <td id="price-${rowLabel}">${defaultPrice}</td> 
                <td><button onclick="removeRow('${rowLabel}')">Remove</button></td>
            </tr>`;
                seatTable.innerHTML += row;

                document.getElementById(`category-${rowLabel}`).value = defaultCategory;
            });

            // Add Individual Seat selections (if any)
            Object.keys(selectedSeats).forEach(seatID => {
                let seat = selectedSeats[seatID];
                let defaultCategory = "VIP";
                let defaultPrice = defaultPrices[defaultCategory];

                let row = `<tr id="seatRow-${seatID}">
                <td>${seat.row}</td>
                <td>${seatID}</td>
                <td>
                    <select id="category-${seatID}" onchange="updatePrice('${seatID}')">
                        <option value="VIP">VIP</option>
                        <option value="Regular">Regular</option>
                        <option value="Economy">Economy</option>
                    </select>
                </td>
                <td id="price-${seatID}">${defaultPrice}</td> 
                <td><button onclick="removeSeat('${seatID}')">Remove</button></td>
            </tr>`;
                seatTable.innerHTML += row;

                document.getElementById(`category-${seatID}`).value = defaultCategory;
            });
        }
        function updatePrice(seatID) {
            let category = document.getElementById(`category-${seatID}`).value;
            document.getElementById(`price-${seatID}`).innerText = defaultPrices[category];
        }

        function removeSeat(seatID) {
            delete selectedSeats[seatID];
            document.getElementById(`seatRow-${seatID}`).remove();
            document.querySelector(`.seat[data-seat-id="${seatID}"]`)?.classList.remove("selected");
        }


        function removeRow(rowLabel) {
            delete selectedRows[rowLabel];
            document.getElementById(`row-${rowLabel}`).remove();

            // Unselect all seats in that row
            let seats = document.querySelectorAll(`.row-label:contains('${rowLabel}')`).parentElement.querySelectorAll(".seat");
            seats.forEach(seat => seat.classList.remove("selected"));
        }

    </script>



</body>

</html>
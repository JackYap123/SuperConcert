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
$selectedSeats = [];


$query = "SELECT row_label, seat_number FROM event_seats WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc())
{
    $selectedSeats[] = $row['row_label'] . $row['seat_number']; // e.g., "A1", "B5"
}

$stmt->close();
$conn->close();

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
    <link rel="stylesheet" href="../css/ticketSetup.css">
</head>

<body>
    <div class="selected-seats-container">
        <table>
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Seat Number</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="selectedSeatsTable">
                <!-- 已存座位将被动态加载 -->
            </tbody>
        </table>
    </div>
    


    <!-- Seat Selection Section (Imported from seatings.php) -->
    <div class="seat-selection">
        <?php include 'seatings.php'; ?>
    </div>

    <!-- Ticket Pricing & Category Setup -->
    <h2>Manage Ticket Pricing</h2>
    <button id="saveButton" onclick="save()">Save Seats</button>

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
        let selectedSeatsFromDB = <?= json_encode($selectedSeats) ?>;
        console.log(<?= json_encode($selectedSeats) ?>);
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
                let seatNumber = seat.dataset.seatNumber;
                let rowLabel = seat.closest(".row").dataset.rowLabel; // 仍保留 rowLabel 以便后续存储

                // **数据库检测：只检查 seatNumber**
                if (selectedSeatsFromDB.includes(seatNumber)) {
                    seat.classList.add("selected-seat"); // 变为已选状态
                    seat.dataset.selected = "true"; // 让它变成不可选状态
                }

                // **点击事件**
                seat.addEventListener("click", function () {
                    if (selectionType === "single") {
                        toggleSeatSelection(seat, rowLabel, seatNumber);
                    }
                });
            });
        });

        function toggleSeatSelection(seat, rowLabel, seatNumber) {
            // **确保数据库座位不可更改**
            if (selectedSeatsFromDB.includes(seatNumber)) {
                return;
            }

            if (selectedSeats[seatNumber]) {
                // **取消选中**
                seat.classList.remove("selected");
                delete selectedSeats[seatNumber];
            } else {
                // **选中**
                seat.classList.add("selected");
                selectedSeats[seatNumber] = {
                    row: rowLabel,  // **存储时仍然记录 rowLabel**
                    seatNumber: seatNumber
                };
            }

            updateSeatTable();
        }

        document.addEventListener("DOMContentLoaded", function () {
            selectedSeatsFromDB.forEach(seatID => {
                let seatElement = document.querySelector(`[data-seat-id="${seatID}"]`);

                if (seatElement) {
                    seatElement.classList.add("selected-seat"); // 加入青色背景
                    seatElement.dataset.selected = "true"; // 让它变成不可选状态
                }
            });
        });

        function toggleSeat(seatID) {
            let seat = document.getElementById(seatID);

            // 如果座位已锁定，则解除选中
            if (seat.dataset.selected === "true") {
                removeSeatFromDB(seatID);
            } else {
                seat.classList.toggle("selected-seat");
            }
        }

        function removeSeatFromDB(seatID) {
            if (!confirm("Are you sure you want to remove this seat?")) return;

            fetch("remove_seat.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ event_id: <?= $_SESSION['selected_event']; ?>, seat_id: seatID })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`selectedSeatRow-${seatID}`).remove(); // 从表格移除
                        let seatElement = document.querySelector(`[data-seat-id="${seatID}"]`);
                        if (seatElement) {
                            seatElement.classList.remove("selected-seat"); // 取消高亮
                            seatElement.dataset.selected = "false";
                        }
                    } else {
                        alert("Remove failed: " + data.error);
                    }
                })
                .catch(error => console.error("Error:", error));
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

        document.addEventListener("DOMContentLoaded", function () {
            loadSavedSeats(); // 加载已选座位
        });

        // **加载数据库中的已存座位**
        function loadSavedSeats() {
            let eventId = <?= $_SESSION['selected_event']; ?>;

            fetch('get_saved_seats.php?event_id=' + eventId)
                .then(response => response.json())
                .then(data => {
                    let selectedSeatsTable = document.getElementById("selectedSeatsTable");
                    selectedSeatsTable.innerHTML = ""; // 清空表格

                    data.seats.forEach(seat => {
                        let seatID = seat.seat_number;
                        let rowLabel = seat.row_label;
                        let category = seat.category;
                        let price = seat.price;

                        // **在前端高亮已存座位**
                        let seatElement = document.querySelector(`[data-seat-id="${seatID}"]`);
                        if (seatElement) {
                            seatElement.classList.add("selected-seat");
                            seatElement.dataset.selected = "true";
                        }

                        // **添加到表格**
                        selectedSeatsTable.innerHTML += `
                    <tr id="selectedSeatRow-${seatID}">
                        <td>${rowLabel}</td>
                        <td>${seatID}</td>
                        <td>${category}</td>
                        <td>${price}</td> 
                        <td><button onclick="removeSeatFromDB('${seatID}')">Remove</button></td>
                    </tr>
                `;
                    });
                })
                .catch(error => console.error("Error:", error));
        }
        // **在表格中显示已存座位**
        function addSavedSeatToTable(rowLabel, seatID, category, price) {
            let seatTable = document.getElementById("seatTable");

            let row = `<tr id="savedSeatRow-${seatID}">
        <td>${rowLabel}</td>
        <td>${seatID}</td>
        <td>${category}</td>
        <td>${price}</td> 
        <td><button onclick="removeSeatFromDB('${seatID}')">Remove</button></td>
    </tr>`;

            seatTable.innerHTML += row;
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


        function save() {
            let eventId = <?= $_SESSION['selected_event']; ?>;
            let seatsData = [];

            // 遍历已选座位并收集数据
            Object.keys(selectedSeats).forEach(seatID => {
                let seat = selectedSeats[seatID];
                let category = document.getElementById(`category-${seatID}`).value;
                let price = document.getElementById(`price-${seatID}`).innerText;

                seatsData.push({
                    row: seat.row,
                    seat_number: seatID,
                    category: category,
                    price: parseFloat(price)
                });
            });

            if (seatsData.length === 0) {
                alert("Please select at least one seat.");
                return;
            }

            // 发送 AJAX 请求
            fetch('save_seats.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_id: eventId, seats: seatsData })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Seats saved successfully");
                        window.location.reload(); // 刷新页面
                    } else {
                        alert("Save failed" + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
        }


    </script>



</body>

</html>
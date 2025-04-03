<?php
session_start();
include '../inc/config.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Theater Seat Selection</title>
    <link rel="stylesheet" href="../css/seatings.css" />
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f8f8;
        margin: 0;
        padding: 20px;
    }

    .stage {
        text-align: center;
        background: #333;
        color: white;
        padding: 15px;
        font-size: 18px;
        font-weight: bold;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .seat-container {
        margin-bottom: 40px;
    }

    .section-label {
        font-size: 20px;
        font-weight: bold;
        margin: 20px 0 10px;
    }

    .row-container {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
    }

    .row-label {
        width: 40px;
        text-align: center;
        font-weight: bold;
    }

    .row {
        display: flex;
        gap: 4px;
    }

    .seat {
        width: 30px;
        height: 30px;
        background-color: #ccc;
        text-align: center;
        line-height: 30px;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        transition: 0.3s;
    }

    .seat:hover {
        background-color: #999;
    }

    .selected {
        background-color: green !important;
        color: white;
    }

    .selected-seat {
        border: 2px solid red !important;
    }

    .vip-seat {
        background-color: gold !important;
        color: black;
    }

    .regular-seat {
        background-color: dodgerblue !important;
        color: white;
    }

    .economy-seat {
        background-color: mediumseagreen !important;
        color: white;
    }

    table {
        color:black;
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 6px;
        overflow: hidden;
    }

    th, td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        text-align: center;
    }

    th {
        background-color: #333;
        color: white;
        text-transform: uppercase;
        font-size: 14px;
    }

    tr:hover {
        background-color: #f2f2f2;
    }

    button {
        background-color: #4CAF50;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #45a049;
    }
</style>

</head>

<body>

    <div class="stage">STAGE</div>

    <div class="seat-container">
        <div class="section-label">Front Stage</div>
        <div class="section" id="frontStage"></div>

        <div class="section-label">Balcony</div>
        <div class="section" id="balcony"></div>
    </div>

    <h3>Selected Seats</h3>
    <table id="seatTable">
        <thead>
            <tr>
                <th>Row</th>
                <th>Seat</th>
                <th>Category</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <button onclick="save()">💾 Save Selection</button>

    <script>
        let selectedSeats = {};
        let selectedSeatsFromDB = [];
        let defaultPrices = {
            VIP: 120,
            Regular: 90,
            Economy: 60
        };

        const eventId = <?= $_SESSION['selected_event'] ?? 1 ?>;

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
                const seat = document.createElement("div");
                seat.classList.add("seat");

                const seatNum = start + i + 1;
                const seatID = `${rowLabel}${seatNum}`;

                seat.innerText = seatNum;
                seat.dataset.seatId = seatID;
                seat.dataset.seatNumber = seatNum;

                if (selectedSeatsFromDB.includes(seatID)) {
                    seat.classList.add("selected-seat");
                    seat.dataset.selected = "true";
                    seat.style.backgroundColor = "red";
                }

                seat.addEventListener("click", function () {
                    if (seat.classList.contains("selected-seat")) return;
                    toggleSeatSelection(seat, rowLabel, seatNum, seatID);
                });

                row.appendChild(seat);
            }

            return row;
        }

        function toggleSeatSelection(seat, rowLabel, seatNumber, seatID) {
            if (selectedSeats[seatID]) {
                seat.classList.remove("selected");
                delete selectedSeats[seatID];
            } else {
                seat.classList.add("selected");
                selectedSeats[seatID] = { row: rowLabel, seatNumber };
            }

            updateSeatTable();
        }

        function updateSeatTable() {
            const tbody = document.querySelector("#seatTable tbody");
            tbody.innerHTML = "";

            Object.entries(selectedSeats).forEach(([seatID, seat]) => {
                const defaultCategory = "VIP";
                const defaultPrice = defaultPrices[defaultCategory];

                const row = document.createElement("tr");
                row.id = `seatRow-${seatID}`;
                row.innerHTML = `
        <td>${seat.row}</td>
        <td>${seatID}</td>
        <td>
          <select id="category-${seatID}" class="category-select" data-seat-id="${seatID}">
            <option value="VIP">VIP</option>
            <option value="Regular">Regular</option>
            <option value="Economy">Economy</option>
          </select>
        </td>
        <td id="price-${seatID}">${defaultPrice}</td>
        <td><button onclick="removeSeat('${seatID}')">Remove</button></td>
      `;

                tbody.appendChild(row);

                document.getElementById(`category-${seatID}`).value = defaultCategory;
                applyCategoryColor(document.querySelector(`[data-seat-id="${seatID}"]`), defaultCategory);

                document.getElementById(`category-${seatID}`).addEventListener("change", function () {
                    const seatElement = document.querySelector(`[data-seat-id="${seatID}"]`);
                    updatePrice(seatID);
                    applyCategoryColor(seatElement, this.value);
                });
            });
        }

        function updatePrice(seatID) {
            const category = document.getElementById(`category-${seatID}`).value;
            document.getElementById(`price-${seatID}`).innerText = defaultPrices[category];
        }

        function applyCategoryColor(seatElement, category) {
            if (!seatElement) return;

            // Remove all category colors first
            seatElement.classList.remove("vip-seat", "regular-seat", "economy-seat");

            // Apply category color
            if (category === "VIP") {
                seatElement.classList.add("vip-seat");
            } else if (category === "Regular") {
                seatElement.classList.add("regular-seat");
            } else if (category === "Economy") {
                seatElement.classList.add("economy-seat");
            }
        }


        function removeSeat(seatID) {
            delete selectedSeats[seatID];
            document.getElementById(`seatRow-${seatID}`)?.remove();
            document.querySelector(`[data-seat-id="${seatID}"]`)?.classList.remove("selected");
        }

        function save() {
            const seatsData = [];

            for (const seatID in selectedSeats) {
                const seat = selectedSeats[seatID];
                const category = document.getElementById(`category-${seatID}`).value;
                const price = document.getElementById(`price-${seatID}`).innerText;

                seatsData.push({
                    row: seat.row,
                    seat_number: seatID,
                    category: category,
                    price: parseFloat(price)
                });
            }

            if (seatsData.length === 0) {
                alert("Please select at least one seat.");
                return;
            }

            fetch('save_seats.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_id: eventId, seats: seatsData })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Seats saved successfully!");
                        window.location.reload();
                    } else {
                        alert("Save failed: " + data.error);
                    }
                })
                .catch(console.error);
        }

        function removeSeatFromDB(seatID) {
            if (!confirm("Are you sure you want to remove this seat?")) return;

            fetch("remove_seat.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ event_id: eventId, seat_id: seatID })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Seat removed.");
                        document.getElementById(`selectedSeatRow-${seatID}`)?.remove();
                        document.querySelector(`[data-seat-id="${seatID}"]`)?.classList.remove("selected-seat");
                    } else {
                        alert("Remove failed: " + data.error);
                    }
                })
                .catch(console.error);
        }

        function loadSavedSeats() {
            fetch(`get_saved_seats.php?event_id=${eventId}`)
                .then(res => res.json())
                .then(data => {
                    selectedSeatsFromDB = data.seats.map(seat => seat.seat_number);

                    data.seats.forEach(seat => {
                        const seatID = seat.seat_number;
                        const category = seat.category;
                        const rowLabel = seat.row_label;
                        const price = seat.price;

                        const seatElement = document.querySelector(`[data-seat-id="${seatID}"]`);

                        if (seatElement) {
                            seatElement.classList.add("selected-seat");
                            seatElement.dataset.selected = "true";
                            applyCategoryColor(seatElement, category); // ⭐ Apply color based on category
                        }

                        const table = document.querySelector("#seatTable tbody");
                        table.innerHTML += `
                <tr id="selectedSeatRow-${seatID}">
                    <td>${rowLabel}</td>
                    <td>${seatID}</td>
                    <td>${category}</td>
                    <td>${price}</td>
                    <td><button onclick="removeSeatFromDB('${seatID}')">Remove</button></td>
                </tr>`;
                    });
                })
                .catch(error => {
                    console.error("Error loading saved seats:", error);
                });
        }


        // Load seats
        createSeating("frontStage", 8, 10, 12, 10, false);
        createSeating("balcony", 6, 10, 12, 10, true);
        window.onload = loadSavedSeats;
    </script>

</body>

</html>
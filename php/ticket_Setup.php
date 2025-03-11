<?php
include '../inc/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater Seat Selection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .stage {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .seat-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .section {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 10px 0;
        }
        .section-label {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            width: 100%;
            margin-bottom: 5px;
        }
        .row {
            display: flex;
            justify-content: flex-end; /* Align numbering from right */
            margin: 2px;
        }
        .seat {
            width: 35px;
            height: 35px;
            background-color: lightgray;
            border: 1px solid black;
            text-align: center;
            line-height: 35px;
            margin: 2px;
            cursor: pointer;
            user-select: none;
        }
        .seat.selected {
            background-color: green;
            color: white;
        }
        .seat.reserved {
            background-color: red;
            cursor: not-allowed;
        }
        .seat.vip {
            background-color: gold;
        }
        .seat.general {
            background-color: blue;
            color: white;
        }
        .seat.balcony {
            background-color: purple;
            color: white;
        }
        .selection-panel {
            margin-top: 20px;
        }
        .seat-entry {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 5px;
        }
        .seat-entry input, .seat-entry select {
            margin-left: 10px;
        }
        .confirm-button {
            margin-top: 10px;
            padding: 10px 15px;
            font-size: 16px;
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="stage">STAGE</div>
    
    <div class="seat-container">
        <div class="section-label">Front Stage</div>
        <div class="section">
            <div id="leftSection"></div>
            <div id="midSection"></div>
            <div id="rightSection"></div>
        </div>

        <div class="section-label">Balcony</div>
        <div class="section">
            <div id="balconyLeft"></div>
            <div id="balconyMid"></div>
            <div id="balconyRight"></div>
        </div>
    </div>

    <div class="selection-panel">
        <h3>Selected Seats</h3>
        <div id="selectedSeats"></div>
        <p>Total Price: $<span id="totalPrice">0</span></p>
        <button class="confirm-button" onclick="confirmSelection()">Confirm</button>
    </div>
    
    <script>
        let totalPrice = 0;
        let selectedSeats = {};
        let seatCounter = 1; // Global counter

        function createSeating(areaId, rows, seatsPerRow) {
            const area = document.getElementById(areaId);
            for (let r = 0; r < rows; r++) {
                let row = document.createElement("div");
                row.classList.add("row");
                for (let s = 1; s <= seatsPerRow; s++) { // Left to right numbering
                    let seatNumber = s; // Reset numbering for each row
                    let seat = document.createElement("div");
                    seat.classList.add("seat");
                    seat.innerText = seatNumber;
                    seat.dataset.seatNumber = seatNumber;
                    
                    seat.addEventListener("click", function () {
                        if (!seat.classList.contains("reserved")) {
                            seat.classList.toggle("selected");
                            if (seat.classList.contains("selected")) {
                                addSeatEntry(seat);
                            } else {
                                removeSeatEntry(seat);
                            }
                        }
                    });
                    row.appendChild(seat);
                }
                area.appendChild(row);
            }
        }

        function addSeatEntry(seat) {
            const selectedSeatsContainer = document.getElementById("selectedSeats");
            let seatEntry = document.createElement("div");
            seatEntry.classList.add("seat-entry");
            seatEntry.dataset.seatNumber = seat.dataset.seatNumber;
            
            let seatLabel = document.createElement("span");
            seatLabel.innerText = `Seat ${seat.dataset.seatNumber}:`;
            
            let priceInput = document.createElement("input");
            priceInput.type = "number";
            priceInput.placeholder = "Enter Price";
            priceInput.min = 0;
            priceInput.addEventListener("input", updateTotalPrice);
            
            let categorySelect = document.createElement("select");
            categorySelect.innerHTML = `
                <option value="vip">VIP</option>
                <option value="general">General</option>
                <option value="balcony">Balcony</option>
            `;
            
            seatEntry.appendChild(seatLabel);
            seatEntry.appendChild(priceInput);
            seatEntry.appendChild(categorySelect);
            selectedSeatsContainer.appendChild(seatEntry);

            selectedSeats[seat.dataset.seatNumber] = { seat, priceInput, categorySelect };
            updateTotalPrice();
        }
        
        function removeSeatEntry(seat) {
            delete selectedSeats[seat.dataset.seatNumber];
            updateTotalPrice();
        }

        function updateTotalPrice() {
            totalPrice = 0;
            for (let seat in selectedSeats) {
                let price = parseFloat(selectedSeats[seat].priceInput.value) || 0;
                totalPrice += price;
            }
            document.getElementById("totalPrice").innerText = totalPrice;
        }

        function confirmSelection() {
            for (let seat in selectedSeats) {
                let category = selectedSeats[seat].categorySelect.value;
                selectedSeats[seat].seat.classList.remove("vip", "general", "balcony");
                selectedSeats[seat].seat.classList.add(category);
            }
        }

        createSeating("leftSection", 8, 10);
        createSeating("midSection", 8, 12);
        createSeating("rightSection", 8, 10);
        createSeating("balconyLeft", 6, 10);
        createSeating("balconyMid", 6, 12);
        createSeating("balconyRight", 6, 10);
    </script>
</body>
</html>
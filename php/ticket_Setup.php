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
            justify-content: center;
            margin: 2px;
        }
        .row-labels {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            margin-right: 10px; /* Space between labels and seats */
            font-weight: bold;
            font-size: 16px;
        }

        .row-labels span {
            height: 38px; /* Adjusted to match seat height + margin */
            display: flex;
            align-items: center;
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
    </style>
</head>
<body>
    <div class="stage">STAGE</div>
    
    <div class="seat-container">
    <div class="section-label">Front Stage</div>
    <div class="section">
        <div class="row-labels">
            <span>A</span>
            <span>B</span>
            <span>C</span>
            <span>D</span>
            <span>E</span>
            <span>F</span>
            <span>G</span>
            <span>H</span>
            <span>I</span>
        </div>
        <div id="leftSection"></div>
        <div id="midSection"></div>
        <div id="rightSection"></div>
    </div>

    <div class="section-label">Balcony</div>
    <div class="section">
        <div class="row-labels">
            <span>AA</span>
            <span>BB</span>
            <span>CC</span>
            <span>DD</span>
            <span>EE</span>
        </div>
        <div id="balconyLeft"></div>
        <div id="balconyMid"></div>
        <div id="balconyRight"></div>
    </div>
</div>

    
    <p id="selectedSeat">Selected Seat: None</p>
    
    <script>
        function createSeating(areaId, rows, seatsPerRow) {
            const area = document.getElementById(areaId);
            for (let r = 0; r < rows; r++) {
                let row = document.createElement("div");
                row.classList.add("row");
                for (let s = 0; s < seatsPerRow; s++) {
                    let seat = document.createElement("div");
                    seat.classList.add("seat");
                    seat.innerText = (r * seatsPerRow + s + 1);
                    seat.addEventListener("click", function () {
                        if (!seat.classList.contains("reserved")) {
                            seat.classList.toggle("selected");
                            document.getElementById("selectedSeat").innerText = 
                                "Selected Seat: " + (seat.classList.contains("selected") ? seat.innerText : "None");
                        }
                    });
                    row.appendChild(seat);
                }
                area.appendChild(row);
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
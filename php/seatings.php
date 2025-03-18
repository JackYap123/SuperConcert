<?php
include '../inc/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater Seat Selection</title>
    <link rel="stylesheet" href="../css/seatings.css">
</head>
<body>
    <div class="stage">STAGE</div>
    
    <div class="seat-container">
        <div class="section-label">Front Stage</div>
        <div class="section" id="frontStage"></div>

        <div class="section-label">Balcony</div>
        <div class="section" id="balcony"></div>
    </div>
    
    <script>
        function createSeating(containerId, rows, leftSeats, midSeats, rightSeats) {
            const container = document.getElementById(containerId);
            const rowLabels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split(""); // Row labels A, B, C...

            for (let r = 0; r < rows; r++) {
                let rowContainer = document.createElement("div");
                rowContainer.classList.add("row-container");

                let rowLabelLeft = document.createElement("div");
                rowLabelLeft.classList.add("row-label");
                rowLabelLeft.innerText = rowLabels[r];

                let leftSection = createSeatRow(leftSeats, r * (leftSeats + midSeats + rightSeats));
                let midSection = createSeatRow(midSeats, r * (leftSeats + midSeats + rightSeats) + leftSeats);
                let rightSection = createSeatRow(rightSeats, r * (leftSeats + midSeats + rightSeats) + leftSeats + midSeats);

                let rowLabelRight = document.createElement("div");
                rowLabelRight.classList.add("row-label");
                rowLabelRight.innerText = rowLabels[r];

                rowContainer.appendChild(rowLabelLeft);
                rowContainer.appendChild(leftSection);
                rowContainer.appendChild(midSection);
                rowContainer.appendChild(rightSection);
                rowContainer.appendChild(rowLabelRight);

                container.appendChild(rowContainer);
            }
        }

        function createSeatRow(seats, startNumber) {
            let row = document.createElement("div");
            row.classList.add("row");

            for (let s = 0; s < seats; s++) {
                let seat = document.createElement("div");
                seat.classList.add("seat");
                seat.innerText = startNumber + s + 1;
                seat.dataset.seatNumber = startNumber + s + 1;

                seat.addEventListener("click", function () {
                    if (!seat.classList.contains("reserved")) {
                        seat.classList.toggle("selected");
                    }
                });

                row.appendChild(seat);
            }
            return row;
        }

        // Create front stage and balcony sections
        createSeating("frontStage", 8, 10, 12, 10);
        createSeating("balcony", 6, 10, 12, 10);

    </script>
</body>
</html>

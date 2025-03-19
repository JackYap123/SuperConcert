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
        function createSeating(containerId, rows, leftSeats, midSeats, rightSeats, isBalcony) {
            const container = document.getElementById(containerId);
            const rowLabels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split(""); // Front Stage row labels
            const balconyRowLabels = rowLabels.map(label => label + label); // Balcony row labels (AA, BB, CC...)

            for (let r = 0; r < rows; r++) {
                let rowContainer = document.createElement("div");
                rowContainer.classList.add("row-container");

                let rowLabel = isBalcony ? balconyRowLabels[r] : rowLabels[r];

                let rowLabelLeft = document.createElement("div");
                rowLabelLeft.classList.add("row-label");
                rowLabelLeft.innerText = rowLabel;

                let leftSection = createSeatRow(leftSeats, r * (leftSeats + midSeats + rightSeats), rowLabel);
                let midSection = createSeatRow(midSeats, r * (leftSeats + midSeats + rightSeats) + leftSeats, rowLabel);
                let rightSection = createSeatRow(rightSeats, r * (leftSeats + midSeats + rightSeats) + leftSeats + midSeats, rowLabel);

                let rowLabelRight = document.createElement("div");
                rowLabelRight.classList.add("row-label");
                rowLabelRight.innerText = rowLabel;

                rowContainer.appendChild(rowLabelLeft);
                rowContainer.appendChild(leftSection);
                rowContainer.appendChild(midSection);
                rowContainer.appendChild(rightSection);
                rowContainer.appendChild(rowLabelRight);

                container.appendChild(rowContainer);
            }
        }

        function createSeatRow(seats, startNumber, rowLabel) {
            let row = document.createElement("div");
            row.classList.add("row");
            row.dataset.rowLabel = rowLabel; // 赋值 rowLabel 以便后续获取

            for (let s = 0; s < seats; s++) {
                let seat = document.createElement("div");
                seat.classList.add("seat");

                let seatNum = startNumber + s + 1;
                let seatId = `${rowLabel}-${seatNum}`;

                seat.innerText = seatNum;
                seat.dataset.seatId = seatId;
                seat.dataset.seatNumber = seatNum; // 确保 seatNumber 被赋值

                row.appendChild(seat);
            }
            return row;
        }

       



        // Create front stage and balcony sections
        createSeating("frontStage", 8, 10, 12, 10, false); // Front Stage uses A, B, C...
        createSeating("balcony", 6, 10, 12, 10, true);  // Balcony uses AA, BB, CC...
    </script>

</body>

</html>
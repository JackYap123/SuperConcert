<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Booking</title>
    <link rel="stylesheet" href="../css/ticket_Booking.css"> 
</head>
<body>
    <div class="stage">STAGE</div>
    
    <div class="seat-container">
        <!-- VIP Section (Closest to the stage) -->
        <div class="section-label">VIP Section - $150 per seat</div>
        <div class="section vip-section"></div>

        <!-- General Section (Behind VIP) -->
        <div class="section-label">General Section - $100 per seat</div>
        <div class="section general-section"></div>

        <!-- Balcony Section (Farthest from the stage) -->
        <div class="section-label">Balcony Section - $50 per seat</div>
        <div class="section balcony-section"></div>
    </div>

    <div class="selection-panel">
        <h3>Selected Seats</h3>
        <div id="selectedSeats"></div>
        <p>Total Price: $<span id="totalPrice">0</span></p>
        <button class="confirm-button" onclick="confirmSelection()">Confirm Selection</button>
        <!-- Loading Indicator -->
        <div id="loading" style="display: none;">Saving your selection...</div>
    </div>
    
    <script>
        let totalPrice = 0;
        let selectedSeats = {};

        // Define seat categories and their prices
        const seatCategories = {
            vip: { price: 150, rows: ['A', 'B', 'C', 'D'], seatsPerRow: 12 },
            general: { price: 100, rows: ['E', 'F', 'G', 'H'], seatsPerRow: 12 },
            balcony: { price: 50, rows: ['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'], seatsPerRow: 12 }
        };

        // Generate seats dynamically
        function generateSeats() {
            for (const category in seatCategories) {
                const { price, rows, seatsPerRow } = seatCategories[category];

                // Create a row container for each row
                rows.forEach(row => {
                    const rowContainer = document.createElement("div");
                    rowContainer.classList.add("row");

                    for (let i = 1; i <= seatsPerRow; i++) {
                        const seatNumber = `${row}${i}`;
                        const seatElement = createSeatElement(seatNumber, category, price);
                        rowContainer.appendChild(seatElement); // Append directly to the row container
                    }

                    // Append the row container to the section
                    const section = document.querySelector(`.${category}-section`);
                    section.appendChild(rowContainer);
                });
            }
        }

        // Create a seat element
        function createSeatElement(seatNumber, category, price) {
            const seatElement = document.createElement("div");
            seatElement.classList.add("seat", category);
            seatElement.innerText = seatNumber;
            seatElement.dataset.seatNumber = seatNumber;
            seatElement.dataset.price = price;

            seatElement.addEventListener("click", function () {
                toggleSeatSelection(seatElement);
            });

            return seatElement;
        }

        // Toggle seat selection
        function toggleSeatSelection(seat) {
            seat.classList.toggle("selected");
            if (seat.classList.contains("selected")) {
                addSeatEntry(seat);
            } else {
                removeSeatEntry(seat);
            }
            updateTotalPrice();
        }

        // Add a seat entry to the selection panel
        function addSeatEntry(seat) {
            const selectedSeatsContainer = document.getElementById("selectedSeats");
            let seatEntry = document.createElement("div");
            seatEntry.classList.add("seat-entry");
            seatEntry.dataset.seatNumber = seat.dataset.seatNumber;

            let seatLabel = document.createElement("span");
            seatLabel.innerText = `Seat ${seat.dataset.seatNumber}: $${seat.dataset.price}`;

            seatEntry.appendChild(seatLabel);
            selectedSeatsContainer.appendChild(seatEntry);

            selectedSeats[seat.dataset.seatNumber] = { seat, price: parseFloat(seat.dataset.price) };
        }

        // Remove a seat entry from the selection panel
        function removeSeatEntry(seat) {
            const selectedSeatsContainer = document.getElementById("selectedSeats");
            const seatEntry = selectedSeatsContainer.querySelector(`[data-seat-number="${seat.dataset.seatNumber}"]`);
            if (seatEntry) {
                selectedSeatsContainer.removeChild(seatEntry);
            }
            delete selectedSeats[seat.dataset.seatNumber];
        }

        // Update the total price
        function updateTotalPrice() {
            totalPrice = 0;
            for (let seat in selectedSeats) {
                totalPrice += selectedSeats[seat].price;
            }
            document.getElementById("totalPrice").innerText = totalPrice.toFixed(2);
        }

        // Confirm selection
        function confirmSelection() {
            if (Object.keys(selectedSeats).length === 0) {
                alert("Please select at least one seat.");
                return;
            }

            if (!confirm("Are you sure you want to reserve these seats?")) {
                return;
            }

            const seatsToSave = [];

            for (let seat in selectedSeats) {
                const seatData = {
                    seatNumber: seat,
                    price: selectedSeats[seat].price
                };
                seatsToSave.push(seatData);
            }

            // Show loading indicator
            document.getElementById('loading').style.display = 'block';

            // Redirect to Payment.php with query parameters
            const queryParams = new URLSearchParams({
                seats: JSON.stringify(seatsToSave),
                totalPrice: totalPrice.toFixed(2)
            });
            window.location.href = `Payment.php?${queryParams.toString()}`;
        }

        // Generate seats when the page loads
        generateSeats();
    </script>
</body>
</html>
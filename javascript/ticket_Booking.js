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
        const sectionId = getSectionId(category);
        const section = document.getElementById(sectionId);

        rows.forEach(row => {
            // Create a row container for each row
            const rowContainer = document.createElement("div");
            rowContainer.classList.add("row");
            rowContainer.innerText = `Row ${row}`; // Optional: Display row label

            for (let i = 1; i <= seatsPerRow; i++) {
                const seatNumber = `${row}${i}`;
                const seatElement = createSeatElement(seatNumber, category, price);
                rowContainer.appendChild(seatElement);
            }

            section.appendChild(rowContainer); // Add the row to the section
        });
    }
}

// Helper function to get section ID based on category
function getSectionId(category) {
    switch (category) {
        case 'vip':
            return 'leftSection';
        case 'general':
            return 'midSection';
        case 'balcony':
            return 'balconyLeft';
        default:
            return 'midSection';
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

    // Send data to the server using AJAX
    fetch('save_seats.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ seats: seatsToSave })
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading indicator
        document.getElementById('loading').style.display = 'none';

        if (data.status === 'success') {
            alert("Seats reserved successfully!");
            // Redirect to Payment.php with query parameters
            const queryParams = new URLSearchParams({
                seats: JSON.stringify(seatsToSave),
                totalPrice: totalPrice.toFixed(2)
            });
            window.location.href = `Payment.php?${queryParams.toString()}`;
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while reserving seats.");
        document.getElementById('loading').style.display = 'none';
    });
}

// Generate seats when the page loads
generateSeats();
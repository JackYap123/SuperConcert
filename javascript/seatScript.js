// Define seat categories based on row
const seatPrices = {
    "A": 200, "B": 200, // VIP
    "C": 150, "D": 150, "E": 150, "F": 150, "G": 150, "H": 150, // Normal
    "I": 125, "J": 125, "K": 125 // Balcony
};

const seatingArea = document.getElementById("seatingArea");

// Create seat grid dynamically
const rows = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K"];
const cols = 12;

rows.forEach(row => {
    for (let col = 1; col <= cols; col++) {
        const seat = document.createElement("div");
        seat.classList.add("seat");
        seat.dataset.row = row;
        seat.dataset.col = col;

        // Format seat number (A01, A02, ..., A12)
        const seatNumber = `${row}${col.toString().padStart(2, "0")}`;
        seat.innerText = seatNumber;

        // Assign class based on row
        if (["A", "B"].includes(row)) {
            seat.classList.add("vip");
        } else if (["I", "J", "K"].includes(row)) {
            seat.classList.add("balcony");
        } else {
            seat.classList.add("available");
        }

        // Click event for selecting seats
        seat.addEventListener("click", () => selectSeat(seat));

        seatingArea.appendChild(seat);
    }
});


let selectedSeats = [];

// Handle seat selection
function selectSeat(seat) {
    if (seat.classList.contains("taken")) return;

    const row = seat.dataset.row;
    const col = seat.dataset.col;
    const seatId = `${row}${col}`;
    const price = seatPrices[row];

    if (seat.classList.contains("selected")) {
        seat.classList.remove("selected");
        selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    } else {
        seat.classList.add("selected");
        selectedSeats.push({ id: seatId, price });
    }

    console.log("Selected Seats:", selectedSeats);
}

// Purchase button event
document.getElementById("purchaseButton").addEventListener("click", () => {
    if (selectedSeats.length === 0) {
        alert("Please select at least one seat.");
        return;
    }

    let total = selectedSeats.reduce((sum, seat) => sum + seat.price, 0);
    alert(`You have selected ${selectedSeats.length} seat(s). Total Price: $${total}`);

    // Mark seats as taken
    selectedSeats.forEach(seat => {
        let seatElement = document.querySelector(`[data-row="${seat.id.charAt(0)}"][data-col="${seat.id.slice(1)}"]`);
        if (seatElement) {
            seatElement.classList.add("taken");
            seatElement.classList.remove("selected");
        }
    });

    selectedSeats = [];
});

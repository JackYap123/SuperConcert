const seatingArea = document.getElementById("seatingArea");
const selectMode = document.getElementById("selectMode");
const categorySelect = document.getElementById("seatCategory");
const priceInput = document.getElementById("seatPrice");
const rows = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K"];
const cols = 12;

// Default prices for categories
let seatPrices = {
    "vip": 200,
    "balcony": 125,
    "available": 150
};

// Create seat grid dynamically
rows.forEach(row => {
    for (let col = 1; col <= cols; col++) {
        const seat = document.createElement("div");
        seat.classList.add("seat");
        seat.dataset.row = row;
        seat.dataset.col = col;
        seat.dataset.category = "available";
        seat.dataset.price = seatPrices["available"];

        // Format seat number (A01, A02, ..., A12)
        const seatNumber = `${row}${col.toString().padStart(2, "0")}`;
        seat.innerText = seatNumber;
        
        seat.addEventListener("click", () => selectSeat(seat));
        seatingArea.appendChild(seat);
    }
});

let selectedSeats = [];

function selectSeat(seat) {
    if (seat.classList.contains("taken")) return;
    
    const row = seat.dataset.row;
    const col = seat.dataset.col;
    const seatId = `${row}${col}`;
    const price = parseInt(seat.dataset.price);

    if (selectMode.value === "row") {
        let rowSeats = document.querySelectorAll(`[data-row='${row}']`);
        
        selectedSeats = []; // Clear previous selection
        
        rowSeats.forEach(seat => {
            if (!seat.classList.contains("taken")) {
                seat.classList.add("selected");
                selectedSeats.push({ id: `${seat.dataset.row}${seat.dataset.col}`, price });
            }
        });
    } else {
        toggleSeat(seat, seatId, price);
    }
    console.log("Selected Seats:", selectedSeats);
}


function toggleSeat(seat, seatId, price) {
    if (seat.classList.contains("selected")) {
        seat.classList.remove("selected");
        selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    } else {
        seat.classList.add("selected");
        selectedSeats.push({ id: seatId, price });
    }
}

// Set seat category and price
function setSeatCategory() {
    let selectedCategory = categorySelect.value;
    let newPrice = parseInt(priceInput.value) || seatPrices[selectedCategory];
    
    selectedSeats.forEach(seat => {
        let seatElement = document.querySelector(`[data-row='${seat.id.charAt(0)}'][data-col='${seat.id.slice(1)}']`);
        if (seatElement) {
            seatElement.className = `seat ${selectedCategory}`;
            seatElement.dataset.category = selectedCategory;
            seatElement.dataset.price = newPrice;
        }
    });
    selectedSeats = [];
}

document.getElementById("purchaseButton").addEventListener("click", () => {
    if (selectedSeats.length === 0) {
        alert("Please select at least one seat.");
        return;
    }

    let eventId = "E1234567"; // Replace with the actual event ID

    let seatData = selectedSeats.map(seat => ({
        seat_id: seat.id,
        category: document.querySelector(`[data-row='${seat.id.charAt(0)}'][data-col='${seat.id.slice(1)}']`).dataset.category,
        price: seat.price
    }));

    fetch("../php/insertSeats.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ event_id: eventId, seats: seatData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Tickets purchased successfully!");
            selectedSeats.forEach(seat => {
                let seatElement = document.querySelector(`[data-row='${seat.id.charAt(0)}'][data-col='${seat.id.slice(1)}']`);
                if (seatElement) {
                    seatElement.classList.add("taken");
                    seatElement.classList.remove("selected");
                }
            });
            selectedSeats = [];
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
});


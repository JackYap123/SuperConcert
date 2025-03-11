const seatingArea = document.getElementById("seatingArea");
const selectMode = document.getElementById("selectMode");
const categorySelect = document.getElementById("seatCategory");
const priceInput = document.getElementById("seatPrice");
const setCategoryButton = document.getElementById("setCategoryButton");
const confirmButton = document.getElementById("confirmButton");

const rows = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K"];
const cols = 12;

let selectedSeats = [];
let allSeatsSet = false;

// Create seat grid dynamically
rows.forEach(row => {
    for (let col = 1; col <= cols; col++) {
        const seat = document.createElement("div");
        seat.classList.add("seat");
        seat.dataset.row = row;
        seat.dataset.col = col;
        seat.dataset.category = "";
        seat.dataset.price = "";

        const seatNumber = `${row}${col.toString().padStart(2, "0")}`;
        seat.innerText = seatNumber;

        seat.addEventListener("click", () => selectSeat(seat));
        seatingArea.appendChild(seat);
    }
});

// Enable/Disable Set Category Button based on category AND price inputs
function checkCategoryInputs() {
    setCategoryButton.disabled = !(categorySelect.value !== "" && priceInput.value.trim() !== "");
}

categorySelect.addEventListener("change", checkCategoryInputs);
priceInput.addEventListener("input", checkCategoryInputs);

// Handle seat selection (single or row mode)
function selectSeat(seat) {
    if (seat.classList.contains("taken")) return;

    const seatId = `${seat.dataset.row}${seat.dataset.col}`;

    if (selectMode.value === "row") {
        // ✅ Select all available seats in that row (without clearing existing selections)
        let rowSeats = document.querySelectorAll(`[data-row='${seat.dataset.row}']`);
        rowSeats.forEach(s => {
            if (!s.classList.contains("taken") && !s.classList.contains("selected")) {
                s.classList.add("selected");
                selectedSeats.push({
                    id: `${s.dataset.row}${s.dataset.col}`,
                    row: s.dataset.row,
                    col: s.dataset.col
                });
            }
        });
    } else {
        // Single seat selection toggle
        toggleSeat(seat, seatId);
    }
}

// Toggle individual seat selection
function toggleSeat(seat, seatId) {
    if (seat.classList.contains("selected")) {
        seat.classList.remove("selected");
        selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    } else {
        seat.classList.add("selected");
        selectedSeats.push({
            id: seatId,
            row: seat.dataset.row,
            col: seat.dataset.col
        });
    }
}

// Clear all selected seats
function clearSelections() {
    document.querySelectorAll(".seat.selected").forEach(seat => seat.classList.remove("selected"));
    selectedSeats = [];
}

// Set category and price to selected seats
function setSeatCategory() {
    let category = categorySelect.value;
    const priceValue = priceInput.value.trim();

    // ✅ Alert if price is empty or invalid
    if (priceValue === "" || isNaN(Number(priceValue)) || Number(priceValue) <= 0) {
        alert("Please set a valid price.");
        return;
    }

    // ✅ Alert if no seat selected
    if (selectedSeats.length === 0) {
        alert("Please select at least one seat.");
        return;
    }

    // Apply category and price to each selected seat
    selectedSeats.forEach(seatObj => {
        let seatElement = document.querySelector(
            `[data-row='${seatObj.row}'][data-col='${seatObj.col}']`
        );
        seatElement.className = `seat ${category}`;
        seatElement.dataset.category = category;
        seatElement.dataset.price = priceValue;
        seatElement.title = `${category} - $${priceValue}`; // Tooltip
    });

    clearSelections(); // Reset selections
    checkAllSeatsSet(); // Check if all seats are categorized
}

// Check if all seats are set with a category and price
function checkAllSeatsSet() {
    allSeatsSet = [...document.querySelectorAll(".seat")].every(seat => seat.dataset.category !== "" && seat.dataset.price !== "");
    confirmButton.disabled = !allSeatsSet;
}

// Handle final confirm button click to submit data
confirmButton.addEventListener("click", () => {
    // ✅ Alert if not all seats are set
    if (!allSeatsSet) {
        alert("There are still uncategorized seats.");
        return;
    }

    const event_id = "event_123"; // Example event ID, replace dynamically if needed
    let seatsToSave = [];

    // Collect all seat data
    document.querySelectorAll(".seat").forEach(seat => {
        if (seat.dataset.category && seat.dataset.price) {
            seatsToSave.push({
                seat_id: `${seat.dataset.row}${seat.dataset.col}`,
                category: seat.dataset.category,
                price: seat.dataset.price
            });
        }
    });

    // Send data to server via fetch API
    fetch("seat_management.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ event_id, seats: seatsToSave })
    })
    .then(response => response.json())
    .then(data => alert(data.message)) // Show success or error message from PHP
    .catch(error => alert("Error: " + error));
});

// Add event listener for set category button
setCategoryButton.addEventListener("click", setSeatCategory);

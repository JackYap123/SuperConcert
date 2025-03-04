document.addEventListener("DOMContentLoaded", function () {
    const eventForm = document.getElementById("eventForm");
    const imageInput = document.getElementById("eventImage");
    const imagePreview = document.getElementById("imagePreview");
    const eventsList = document.getElementById("eventsList");

    // Load existing events on the Events page
    function loadEvents() {
        if (eventsList) {
            eventsList.innerHTML = ""; // Clear previous content
            const storedEvents = JSON.parse(localStorage.getItem("events")) || [];
            storedEvents.forEach((event, index) => addEventToPage(event, index));
        }
    }

    // Image preview
    if (imageInput) {
        imageInput.addEventListener("change", function () {
            const file = imageInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:5px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Handle form submission
    if (eventForm) {
        eventForm.addEventListener("submit", function (e) {
            // Ensure form submits only to the server
            eventForm.submit(); // Allow default form submission to backend
        });
    }
    

    // Function to add events to the existing events page
    function addEventToPage(event, index) {
        const eventItem = document.createElement("div");
        eventItem.classList.add("event-item");
        eventItem.innerHTML = `
            <img src="${event.imageSrc}" alt="Event Image">
            <p><strong>${event.date} - ${event.time} (${event.duration} hrs)</strong></p>
            <p>${event.description}</p>
            <button class="delete-btn" onclick="deleteEvent(${index})">Remove</button>
        `;
        eventsList.appendChild(eventItem);
    }

    // Delete an event from localStorage and refresh
    window.deleteEvent = function (index) {
        let storedEvents = JSON.parse(localStorage.getItem("events")) || [];
        storedEvents.splice(index, 1); // Remove event at index
        localStorage.setItem("events", JSON.stringify(storedEvents));
        loadEvents(); // Reload events
    };

    // Load events on page load
    loadEvents();
});

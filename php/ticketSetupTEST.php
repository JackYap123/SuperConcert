<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seating Selection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-width: 600px;
            margin: 20px auto;
        }
        .section {
            background-color: #ddd;
            padding: 40px;
            cursor: pointer;
            font-weight: bold;
            border: 2px solid #333;
            text-align: center;
        }
        .selected {
            background-color: lightblue;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border: 2px solid black;
            box-shadow: 0px 0px 10px gray;
        }
        .popup button {
            margin: 5px;
            padding: 10px;
            cursor: pointer;
        }
        .image-container img {
            width: 90%;
            max-width: 1200px;
            height: auto;
        }
    </style>
</head>
<body>

    <div class="image-container">   
        <img src="../img/seats.png" alt="Seating Plan">
    </div>

    <h2>Select Seating Categories</h2>
    <div class="grid-container">
        <div class="section" data-id="A">Section A</div>
        <div class="section" data-id="B">Section B</div>
        <div class="section" data-id="C">Section C</div>
        <div class="section" data-id="D">Section D</div>
        <div class="section" data-id="E">Section E</div>
        <div class="section" data-id="F">Section F</div>
    </div>

    <form id="seatingForm" action="process_seats.php" method="post">
        <input type="hidden" name="selected_sections" id="selectedSections">
        <button type="submit" style="margin-top: 20px; padding: 10px;">Save Selections</button>
    </form>

    <div class="popup" id="popup">
        <h3>Select Category</h3>
        <button onclick="setCategory('VIP')">VIP</button>
        <button onclick="setCategory('General Public')">General Public</button>
        <button onclick="setCategory('Child')">Child</button>
        <button onclick="setCategory('Senior Citizen')">Senior Citizen</button>
    </div>

    <script>
        let selectedSections = {}; // Store selected sections
        let currentSection = null;

        document.querySelectorAll('.section').forEach(section => {
            section.addEventListener('click', () => {
                currentSection = section.getAttribute('data-id');
                document.getElementById("popup").style.display = "block";
            });
        });

        function setCategory(category) {
            if (currentSection) {
                selectedSections[currentSection] = category;
                document.querySelector(`[data-id="${currentSection}"]`).classList.add("selected");
                document.querySelector(`[data-id="${currentSection}"]`).innerText = `Section ${currentSection}\n(${category})`;
            }
            document.getElementById("popup").style.display = "none";
            document.getElementById("selectedSections").value = JSON.stringify(selectedSections);
        }
    </script>

</body>
</html>

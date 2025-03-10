<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join the Waitlist</title>
    <link rel="stylesheet" href="../css/Waitlist.css">
</head>
<body>

    <!-- Back Button (Same style as Payment Page) -->
    <button class="back-btn" onclick="goBack()">← Back</button>

    <div class="container">
        <div class="waitlist-section">
            <h2>Join the Waitlist</h2>
            <p>Be the first to know when tickets become available!</p>

            <div class="input-group">
                <label>Name</label>
                <input type="text" placeholder="Enter your name">
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" placeholder="Enter your email">
            </div>

            <button class="waitlist-btn">Join Waitlist</button>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

</body>
</html>

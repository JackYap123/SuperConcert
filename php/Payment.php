<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../css/Payment.css">
    <script>
        // Function to validate the payment form
        function validatePaymentForm() {
            const cardNumber = document.getElementById('cardNumber').value;
            const cvv = document.getElementById('cvv').value;
            const expirationDate = document.getElementById('expirationDate').value;

            // Validate card number (16 digits)
            const cardNumberPattern = /^\d{16}$/;
            if (!cardNumberPattern.test(cardNumber)) {
                alert("Please enter a valid card number (16 digits).");
                return false;
            }

            // Validate CVV (3 digits)
            const cvvPattern = /^\d{3}$/;
            if (!cvvPattern.test(cvv)) {
                alert("Please enter a valid CVV (3 digits).");
                return false;
            }

            // Validate expiration date (MM/YY)
            const expirationPattern = /^(0[1-9]|1[0-2])\/?([0-9]{2})$/;
            if (!expirationPattern.test(expirationDate)) {
                alert("Please enter a valid expiration date (MM/YY).");
                return false;
            }

            // If all validations pass
            alert("Payment details are valid. Proceeding to checkout...");
            return true; // You can add further processing here
        }
    </script>
</head>
<body>
    <div class="container">
        <!-- Shopping Cart Section -->
        <div class="cart-section">
            <h2>Shopping Cart</h2>
            <div id="cart-items">
                <?php
                // Retrieve selected seats and total price from the URL
                if (isset($_GET['seats']) && isset($_GET['totalPrice'])) {
                    $seats = json_decode($_GET['seats'], true);
                    $totalPrice = $_GET['totalPrice'];

                    // Display each seat in the cart
                    foreach ($seats as $seat) {
                        echo "<div class='cart-item'>";
                        echo "<span>Seat {$seat['seatNumber']}</span>";
                        echo "<span>RM{$seat['price']}</span>";
                        echo "<span class='trash-icon'>🗑</span>";
                        echo "</div>";
                    }

                    // Display the total price
                    echo "<div class='cart-item' style='font-weight: bold;'>";
                    echo "<span>Total</span>";
                    echo "<span>RM{$totalPrice}</span>";
                    echo "</div>";
                } else {
                    echo "<p>No seats selected.</p>";
                }
                ?>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="payment-section">
            <h2>PAYMENT METHOD</h2>
            <div class="payment-method">
                <button>Visa</button>
                <button>TnG</button>
                <button>FPX</button>
            </div>

            <div class="input-group">
                <label>Name on card</label>
                <input type="text" placeholder="Name">
            </div>
            <div class="input-group">
                <label>Card Number</label>
                <input type="text" id="cardNumber" placeholder="1111 2222 3333 4444">
            </div>

            <!-- Expiration Date & CVV in same row -->
            <div class="input-row">
                <div class="input-group half-width">
                    <label>Expiration date</label>
                    <input type="text" id="expirationDate" placeholder="MM/YY">
                </div>
                <div class="input-group half-width">
                    <label>CVV</label>
                    <input type="text" id="cvv" placeholder="123">
                </div>
            </div>

            <!-- Promotion Code Button -->
            <button class="promo-btn">Enter Promotion Code</button>

            <!-- Checkout Button -->
            <button class="checkout-btn" onclick="if(validatePaymentForm()) { /* Proceed with checkout logic here */ }">RM<?php echo isset($totalPrice) ? $totalPrice : '0'; ?> Checkout →</button>
        </div>
    </div>
</body>
</html>
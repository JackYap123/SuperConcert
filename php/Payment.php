<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../css/Payment.css">
</head>
<body>
    <div class="container">
        <!-- Shopping Cart Section -->
        <div class="cart-section">
            <h2>Shopping Cart</h2>
            <div class="cart-item">
                <span>Event Name - Seat Category</span>
                <span>2 🎟 RM300</span>
                <span class="trash-icon">🗑</span>
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
                <input type="text" placeholder="1111 2222 3333 4444">
            </div>

            <!-- Expiration Date & CVV in same row -->
            <div class="input-row">
                <div class="input-group half-width">
                    <label>Expiration date</label>
                    <input type="text" placeholder="MM/YY">
                </div>
                <div class="input-group half-width">
                    <label>CVV</label>
                    <input type="text" placeholder="123">
                </div>
            </div>

            <!-- Promotion Code Button -->
            <button class="promo-btn">Enter Promotion Code</button>

            <!-- Checkout Button -->
            <button class="checkout-btn">RM300 Checkout →</button>
        </div>
    </div>
</body>

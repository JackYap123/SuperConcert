<?php
// payment.php - Frontend for Payment Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../css/payment.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Super Concert</h1>
            <h2>Payment</h2>
        </header>
        <div class="content">
            <section class="cart">
                <h3>Shopping Cart</h3>
                <p>You have 1 item in your cart</p>
                <div class="cart-item">
                    <div class="item-info">
                        <strong>Event Name</strong>
                        <p>Seat Category</p>
                    </div>
                    <div class="quantity">2 ⬆️</div>
                    <div class="price">RM300</div>
                    <div class="remove">🗑️</div>
                </div>
            </section>
            <section class="payment-method">
                <h3>Payment Method</h3>
                <div class="payment-options">
                    <button>Visa</button>
                    <button>TnG</button>
                    <button>FPX</button>
                </div>
                <form action="process_payment.php" method="POST">
                    <label>Name on Card</label>
                    <input type="text" name="card_name" required>

                    <label>Card Number</label>
                    <input type="text" name="card_number" placeholder="1111 2222 3333 4444" required>

                    <div class="card-details">
                        <div>
                            <label>Expiration Date</label>
                            <input type="text" name="expiry" placeholder="mm/yy" required>
                        </div>
                        <div>
                            <label>CVV</label>
                            <input type="text" name="cvv" placeholder="123" required>
                        </div>
                    </div>

                    <div class="total">
                        <p>Subtotal: RM300</p>
                        <p>Total (Tax incl.): RM300</p>
                    </div>
                    
                    <button class="promo">Enter Promotion Code</button>
                    <button type="submit" class="checkout">Checkout → RM300</button>
                </form>
            </section>
        </div>
    </div>
</body>
</html>

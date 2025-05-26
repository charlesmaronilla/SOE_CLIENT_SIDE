<?php
session_start();
require_once 'db_connection.php';

$session_id = session_id();


$query = "SELECT ci.id, ci.menu_item_id, ci.quantity, mi.name, mi.price, mi.image, mi.stall_id, s.name as stall_name 
          FROM cart_items ci 
          JOIN menu_items mi ON ci.menu_item_id = mi.id 
          JOIN stalls s ON mi.stall_id = s.id
          WHERE ci.session_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}


if (empty($cart_items)) {
    header('Location: view_cart.php');
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            gap: 30px;
        }
        .order-summary {
            flex: 1;
        }
        .order-summary h2 {
            margin-top: 0;
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .item-name {
            font-weight: bold;
        }
        .item-price {
            color: #e67e22;
            font-weight: bold;
        }
        .total-payment {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            color: #e67e22;
        }
        .user-details {
            flex: 1;
        }
        .user-details h2 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"], .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .buttons {
            margin-top: 20px;
            text-align: right;
        }
        .buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
        }
        .cancel-btn {
            background-color: #ccc;
            color: #333;
        }
        .place-order-btn {
            background-color: #e67e22;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="order-summary">
            <h2>Order Summary</h2>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <div>
                        <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div><?= htmlspecialchars($item['quantity']) ?> x ₱<?= number_format($item['price'], 2) ?></div>
                    </div>
                    <div class="item-price">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                </div>
            <?php endforeach; ?>
            <div class="total-payment">Total: ₱<?= number_format($total, 2) ?></div>
        </div>

        <div class="user-details">
            <h2>Your Details</h2>
            <form id="checkoutForm" action="process_order.php" method="POST">
                <div class="form-group">
                    <label for="id_number">Id Number</label>
                    <input type="text" id="id_number" name="id_number" required>
                </div>
                <div class="form-group">
                    <label for="name">Enter Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="message">Message for Seller</label>
                    <textarea id="message" name="message"></textarea>
                </div>
                
                <input type="hidden" name="total" value="<?= htmlspecialchars($total) ?>">

                <div class="buttons">
                    <button type="button" class="cancel-btn" onclick="window.location.href='view_cart.php'">Cancel</button>
                    <button type="submit" class="place-order-btn">Place Order</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html> 
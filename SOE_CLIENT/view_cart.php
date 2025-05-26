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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .back-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .cart-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        .item-details {
            flex-grow: 1;
        }
        .item-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .item-stall {
            color: #666;
            margin: 0 0 10px 0;
        }
        .item-price {
            color: #e67e22;
            font-weight: bold;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .qty-btn {
            background-color: #ddd;
            border: none;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        .qty-input {
            width: 40px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 5px;
        }
        .remove-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        .cart-summary {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .total {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            color: #e67e22;
        }
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #e67e22;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
        }
        .checkout-btn:hover {
            background-color: #cf711d;
        }
        .empty-cart {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="client_dashboard.php" class="back-btn">← Back to Dashboard</a>
            <h1>Your Cart</h1>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Add some delicious items to your cart!</p>
            </div>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" data-id="<?= htmlspecialchars($item['id']) ?>" data-menu-id="<?= htmlspecialchars($item['menu_item_id']) ?>">
                    <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-image">
                    <div class="item-details">
                        <h3 class="item-name"><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="item-stall"><?= htmlspecialchars($item['stall_name']) ?></p>
                        <p class="item-price">₱<?= number_format($item['price'], 2) ?></p>
                    </div>
                    <div class="quantity-controls">
                        <button class="qty-btn" onclick="updateQuantity(<?= htmlspecialchars($item['menu_item_id']) ?>, 'decrease')">-</button>
                        <input type="number" class="qty-input" value="<?= htmlspecialchars($item['quantity']) ?>" min="1" onchange="updateQuantity(<?= htmlspecialchars($item['menu_item_id']) ?>, 'set', this.value)">
                        <button class="qty-btn" onclick="updateQuantity(<?= htmlspecialchars($item['menu_item_id']) ?>, 'increase')">+</button>
                    </div>
                    <button class="remove-btn" onclick="removeItem(<?= htmlspecialchars($item['menu_item_id']) ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>')">Remove</button>
                </div>
            <?php endforeach; ?>

            <div class="cart-summary">
                <div class="total">Total: ₱<?= number_format($total, 2) ?></div>
                <button class="checkout-btn" onclick="window.location.href='checkout.php'">Proceed to Checkout</button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateQuantity(menuItemId, action, value = null) {
            let quantity;
            if (action === 'set') {
                quantity = parseInt(value);
            } else {
                const input = document.querySelector(`.cart-item[data-menu-id="${menuItemId}"] .qty-input`);
                quantity = parseInt(input.value);
                if (action === 'increase') {
                    quantity++;
                } else if (action === 'decrease') {
                    quantity = Math.max(1, quantity - 1);
                }
            }

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `menu_item_id=${menuItemId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); 
                } else {
                    alert(data.message || 'Failed to update quantity');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Could not connect to server');
            });
        }

        function removeItem(menuItemId, itemName) {
            if (confirm(`Are you sure you want to remove ${itemName} from your cart?`)) {
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `menu_item_id=${menuItemId}&quantity=0`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        
                        const cartItem = document.querySelector(`.cart-item[data-menu-id="${menuItemId}"]`);
                        if (cartItem) {
                            cartItem.remove();
                        }
                        
                        
                        const remainingItems = document.querySelectorAll('.cart-item');
                        if (remainingItems.length === 0) {
                            location.reload(); 
                        } else {
                           
                            location.reload(); 
                        }
                    } else {
                        alert(data.message || 'Failed to remove item');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Could not connect to server');
                });
            }
        }

        function checkout() {
            
            alert('Checkout functionality coming soon!');
        }
    </script>
</body>
</html> 
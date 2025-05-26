<?php
session_start();
require_once 'db_connection.php';

$stall_result = $conn->query("SELECT * FROM stalls");
$featured_result = $conn->query("SELECT * FROM menu_items WHERE is_featured = 1 AND available = 1");
$regular_result = $conn->query("SELECT * FROM menu_items WHERE is_featured = 0 AND available = 1");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Client Dashboard</title>
    <style>
      * {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    background-color: #f4f6f8;
}

.sidebar {
    width: 240px;
    background-color: #2c3e50;
    color: white;
    height: 100vh;
    padding: 20px;
    position: fixed;
}

.sidebar h2 {
    font-size: 22px;
    margin-bottom: 20px;
}

.sidebar a {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 8px;
    transition: background 0.3s;
}

.sidebar a:hover {
    background-color: #34495e;
}

.content {
    margin-left: 240px;
    padding: 30px;
    flex-grow: 1;
}

.featured-carousel {
    position: relative;
    height: 280px;
    margin-bottom: 40px;
}

.featured-card {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 300px;
    background: #fff;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    text-align: center;
    opacity: 0;
    transition: opacity 0.5s;
    z-index: 0;
}

.featured-card.active {
    opacity: 1;
    z-index: 1;
}

.featured-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 10px;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 24px;
}

.card {
    background-color: #fff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-4px);
}

.card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
}

.card h3 {
    margin: 10px 0 4px;
}

.card p {
    font-size: 14px;
    color: #555;
}

.card strong {
    color: #e67e22;
    display: block;
    margin-top: 6px;
    font-size: 16px;
}

.add-to-cart-btn {
    margin-top: 10px;
    padding: 10px 14px;
    background-color: #e67e22;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

.add-to-cart-btn:hover {
    background-color: #cf711d;
}

.cart-button {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #e67e22;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1000;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}

.cart-button:hover {
    background-color: #cf711d;
}

.cart-modal {
    display: none;
    position: fixed;
    top: 70px;
    right: 20px;
    width: 320px;
    max-height: 420px;
    overflow-y: auto;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    padding: 20px;
    z-index: 1001;
}

.cart-modal h3 {
    margin-top: 0;
    font-size: 20px;
}

.cart-item {
    margin-bottom: 10px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 8px;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item strong {
    color: #e67e22;
}

.close-cart {
    float: right;
    cursor: pointer;
    font-weight: bold;
    color: #e67e22;
}


.quantity-controls {
    margin-top: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.quantity-controls button {
    font-size: 20px;
    font-weight: bold;
    width: 32px;
    height: 32px;
    cursor: pointer;
    background-color: #e67e22;
    border: none;
    color: white;
    border-radius: 6px;
    user-select: none;
    transition: background-color 0.3s;
    line-height: 0;
}
.quantity-controls button:hover {
    background-color: #cf711d;
}

.quantity-display {
    min-width: 24px;
    font-weight: bold;
    font-size: 16px;
    text-align: center;
    user-select: none;
}
        
        .quantity-controls {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .quantity-controls button {
            font-size: 20px;
            font-weight: bold;
            width: 32px;
            height: 32px;
            cursor: pointer;
            background-color: #e67e22;
            border: none;
            color: white;
            border-radius: 6px;
            user-select: none;
            transition: background-color 0.3s;
            line-height: 0;
        }
        .quantity-controls button:hover {
            background-color: #cf711d;
        }

        .quantity-display {
            min-width: 24px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            user-select: none;
        }

        .add-to-cart-btn {
            margin-top: 10px;
            padding: 10px 14px;
            background-color: #e67e22;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        .add-to-cart-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🍽 Stalls</h2>
    <?php while($stall = $stall_result->fetch_assoc()): ?>
        <a href="stall_dashboard.php?stall_id=<?= $stall['id'] ?>">
            👉 <?= htmlspecialchars($stall['name']) ?>
        </a>
    <?php endwhile; ?>
</div>

<div class="content">
    <h2>🌟 Featured Item</h2>
    <div class="featured-carousel">
        <?php while($item = $featured_result->fetch_assoc()): ?>
            <div class="featured-card" data-id="<?= $item['id'] ?>">
                <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p><?= htmlspecialchars($item['description']) ?></p>
                <strong>₱<?= number_format($item['price'], 2) ?></strong>
                <button class="add-to-cart-btn" data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" data-price="<?= $item['price'] ?>">Add to Cart</button>
            </div>
        <?php endwhile; ?>
    </div>

    <h2>🍔 All Menu Items</h2>
    <div class="menu-grid">
        <?php while($item = $regular_result->fetch_assoc()): ?>
            <div class="card" data-id="<?= $item['id'] ?>">
                <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p><?= htmlspecialchars($item['description']) ?></p>
                <strong>₱<?= number_format($item['price'], 2) ?></strong>

                <div class="quantity-controls">
                    <button class="decrement-btn" data-id="<?= $item['id'] ?>">−</button>
                    <div class="quantity-display" id="qty-<?= $item['id'] ?>">0</div>
                    <button class="increment-btn" data-id="<?= $item['id'] ?>">+</button>
                </div>

                <button class="add-to-cart-btn" data-id="<?= $item['id'] ?>" disabled>Add to Cart</button>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<button class="cart-button" id="cartBtn" onclick="window.location.href='view_cart.php'">🛒 View Cart (0)</button>

<div class="cart-modal" id="cartModal" style="display:none;">
    <h3>Shopping Cart <span class="close-cart" id="closeCart" style="cursor:pointer;color:#e67e22;float:right;">&times;</span></h3>
    <div id="cartItems"></div>
    <div style="margin-top: 15px; text-align: right;">
        <strong>Total: ₱<span id="cartTotal">0.00</span></strong>
        <button onclick="window.location.href='view_cart.php'" style="display: block; width: 100%; margin-top: 10px; padding: 10px; background-color: #e67e22; color: white; border: none; border-radius: 6px; cursor: pointer;">View Full Cart</button>
    </div>
</div>

<script>
    
    const cards = document.querySelectorAll('.featured-card');
    let current = 0;
    if (cards.length > 0) {
        cards[current].classList.add('active');
        setInterval(() => {
            cards[current].classList.remove('active');
            current = (current + 1) % cards.length;
            cards[current].classList.add('active');
        }, 3000);
    }

    
    const cartBtn = document.getElementById('cartBtn');
    const cartModal = document.getElementById('cartModal');
    const cartItemsDiv = document.getElementById('cartItems');
    const cartTotalSpan = document.getElementById('cartTotal');
    const closeCartBtn = document.getElementById('closeCart');

    
    function updateAddToCartBtn(id) {
        const qty = parseInt(document.getElementById(`qty-${id}`).textContent);
        const btn = document.querySelector(`.add-to-cart-btn[data-id="${id}"]`);
        btn.disabled = qty <= 0;
    }

    
    async function loadCart() {
        try {
            const response = await fetch('get_cart.php');
            const data = await response.json();

            console.log('Cart data received:', data);

            if (data.success) {
                cartItemsDiv.innerHTML = '';
                let total = 0, count = 0;
                
                data.items.forEach(item => {
                    // Convert price to a number
                    item.price = parseFloat(item.price);

                    total += item.price * item.qty;
                    count += item.qty;

                    const div = document.createElement('div');
                    div.classList.add('cart-item');
                    div.innerHTML = `
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>${item.name}</strong><br>
                                ₱${item.price.toFixed(2)} x ${item.qty}
                            </div>
                            <button onclick="removeItem(${item.id})" style="background: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Remove</button>
                        </div>
                    `;
                    cartItemsDiv.appendChild(div);
                });

                console.log('Calculated item count:', count);
                console.log('Cart items array:', data.items);

                cartTotalSpan.textContent = total.toFixed(2);
                cartBtn.textContent = `🛒 View Cart (${count})`;
            } else {
                console.error('Error fetching cart data:', data.message || 'Unknown error');
                cartBtn.textContent = `🛒 View Cart (Error)`;
            }
        } catch (error) {
            console.error('Error loading cart:', error);
            cartBtn.textContent = `🛒 View Cart (Error)`;
        }
    }

    
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const id = button.getAttribute('data-id');
            const card = button.closest('.card, .featured-card'); // Get the parent card
            const name = card.querySelector('h3').textContent;

            let quantity = 1; // Default quantity to 1

            // Check if quantity controls exist (for regular items)
            const qtyDisplay = card.querySelector('.quantity-display');
            if (qtyDisplay) {
                quantity = parseInt(qtyDisplay.textContent);
            }

            if (quantity > 0) {
                try {
                    console.log(`Attempting to add item ${id} with quantity ${quantity}`);
                    const response = await fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `menu_item_id=${id}&quantity=${quantity}`
                    });

                    console.log('Response status:', response.status);

                    if (!response.ok) {
                         console.error('HTTP error!', response.status, response.statusText);
                         alert('HTTP error: ' + response.status);
                         return;
                    }

                    const result = await response.json();

                    console.log('add_to_cart.php response:', result);

                    if(result.success) {
                        console.log('Item added successfully, reloading cart...');
                        // Show success message
                        const toast = document.createElement('div');
                        toast.style.cssText = `
                            position: fixed;
                            top: 20px;
                            left: 50%;
                            transform: translateX(-50%);
                            background-color: #4CAF50;
                            color: white;
                            padding: 12px 24px;
                            border-radius: 4px;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                            z-index: 1000;
                        `;
                        toast.textContent = `Added ${quantity} x ${name} to cart!`;
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 3000);

                        // Reset quantity display & disable add button (only for regular items)
                         if (qtyDisplay) {
                            qtyDisplay.textContent = '0';
                            updateAddToCartBtn(id);
                         }

                        // Reload cart display
                        await loadCart();
                        console.log('Cart reloaded.');

                    } else {
                        console.error('Server reported failure:', result.message);
                        alert('Failed to add to cart: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error in fetch or processing response:', error);
                     // Try to read response text if JSON parsing failed
                    if (error instanceof SyntaxError && response) {
                        try {
                            const text = await response.text();
                            console.error('Response text that caused JSON error:', text);
                        } catch (e) {
                            console.error('Could not read response text:', e);
                        }
                    }
                    alert('Could not connect to server or process response');
                }
            }
        });
    });

    
    async function removeItem(menuItemId) {
        if (confirm('Are you sure you want to remove this item?')) {
            try {
                const response = await fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `menu_item_id=${menuItemId}&quantity=0`
                });

                const result = await response.json();
                if (result.success) {
                    await loadCart();
                } else {
                    alert('Failed to remove item: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Could not connect to server');
            }
        }
    }

    
    document.querySelectorAll('.increment-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const qtyDisplay = document.getElementById(`qty-${id}`);
            let qty = parseInt(qtyDisplay.textContent);
            qty++;
            qtyDisplay.textContent = qty;
            updateAddToCartBtn(id);
        });
    });

    
    document.querySelectorAll('.decrement-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const qtyDisplay = document.getElementById(`qty-${id}`);
            let qty = parseInt(qtyDisplay.textContent);
            if (qty > 0) {
                qty--;
                qtyDisplay.textContent = qty;
                updateAddToCartBtn(id);
            }
        });
    });

    
    cartBtn.addEventListener('click', async () => {
        if(cartModal.style.display === 'block'){
            cartModal.style.display = 'none';
        } else {
            await loadCart();
            cartModal.style.display = 'block';
        }
    });

    closeCartBtn.addEventListener('click', () => {
        cartModal.style.display = 'none';
    });

    
    window.addEventListener('click', e => {
        if (e.target === cartModal) {
            cartModal.style.display = 'none';
        }
    });

    
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        updateAddToCartBtn(button.getAttribute('data-id'));
    });

    
    loadCart();
</script>

</body>
</html>

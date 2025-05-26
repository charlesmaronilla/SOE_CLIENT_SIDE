<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connection.php';

$stall_id = isset($_GET['stall_id']) ? (int)$_GET['stall_id'] : 0;


$stmt = $conn->prepare("SELECT * FROM stalls WHERE id = ?");
$stmt->bind_param("i", $stall_id);
$stmt->execute();
$stall_result = $stmt->get_result();
$stall = $stall_result->fetch_assoc();

if (!$stall) {
    die("Stall not found");
}


$stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_featured = 1 AND available = 1 AND stall_id = ?");
$stmt->bind_param("i", $stall_id);
$stmt->execute();
$featured_result = $stmt->get_result();


$stmt = $conn->prepare("SELECT * FROM menu_items WHERE is_featured = 0 AND available = 1 AND stall_id = ?");
$stmt->bind_param("i", $stall_id);
$stmt->execute();
$regular_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($stall['name']) ?> - Stall Menu</title>
    <style>
        
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
}
.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 8px 16px;
    background-color: #2c3e50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
.stall-header {
    margin-bottom: 20px;
}
.stall-header h2 {
    margin: 0;
}
.stall-header p {
    color: #555;
}

.review-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 16px;
    background-color: #e67e22;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}
.review-btn:hover {
    background-color: #cf711d;
}
.featured-carousel {
    position: relative;
    width: 100%;
    height: 260px;
    overflow: hidden;
    margin-bottom: 40px;
}
.featured-card {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 260px;
    background: #fff;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    text-align: center;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    z-index: 0;
}
.featured-card img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 6px;
}
.featured-card.active {
    opacity: 1;
    z-index: 1;
}
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}
.card {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 10px;
    background-color: #fff;
    text-align: center;
}
.card img {
    width: 100%;
    height: 130px;
    object-fit: cover;
    border-radius: 6px;
}
.card h3 {
    margin: 10px 0 5px;
}
.card p {
    font-size: 14px;
    color: #555;
}
.card strong {
    color: #e67e22;
    display: block;
    margin-top: 8px;
}
.add-to-cart-btn {
    margin-top: 10px;
    padding: 8px 12px;
    background-color: #e67e22;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}
.add-to-cart-btn:hover {
    background-color: #cf711d;
}
.quantity-controls {
    margin-top: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
}
.qty-btn {
    background-color: #ddd;
    border: none;
    padding: 6px 10px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
    user-select: none;
}
.qty-input {
    width: 30px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    pointer-events: none;
}

#cartBtn {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #e67e22;
    color: white;
    border: none;
    padding: 12px 18px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    z-index: 1000;
}
#cartModal {
    display: none;
    position: fixed;
    top: 70px;
    right: 20px;
    width: 320px;
    max-height: 400px;
    background: white;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    overflow-y: auto;
    z-index: 1000;
}
#cartModal header {
    padding: 12px;
    font-weight: bold;
    background-color: #f39c12;
    color: white;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    position: relative;
}
#cartModal header button {
    position: absolute;
    right: 10px;
    top: 8px;
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}
#cartItems {
    padding: 10px 15px;
}
.cart-item {
    margin-bottom: 10px;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}
#cartTotal {
    padding: 10px 15px;
    font-weight: bold;
    border-top: 1px solid #ccc;
    background: #fafafa;
    text-align: right;
}
.toast-notification {
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
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.toast-notification.show {
    opacity: 1;
}

    </style>
</head>
<body>

<a href="client_dashboard.php" class="back-btn">← Back to Dashboard</a>

<div class="stall-header">
    <h2><?= htmlspecialchars($stall['name']) ?></h2>
    <p><?= htmlspecialchars($stall['description']) ?></p>
    <a href="ratings.php?stall_id=<?= $stall_id ?>" class="review-btn">Review & Ratings</a>
</div>

<button id="cartBtn" onclick="window.location.href='view_cart.php'">View Cart (0)</button>

<div id="cartModal" class="cart-modal" style="display:none;">
    <h3>Shopping Cart <span class="close-cart" id="closeCart">&times;</span></h3>
    <div id="cartItems"></div>
    <div style="margin-top: 15px; text-align: right;">
        <strong>Total: ₱<span id="cartTotal">0.00</span></strong>
        <button onclick="window.location.href='view_cart.php'" style="display: block; width: 100%; margin-top: 10px; padding: 10px; background-color: #e67e22; color: white; border: none; border-radius: 6px; cursor: pointer;">View Full Cart</button>
    </div>
</div>

<div id="toast" class="toast-notification"></div>

<h3>Featured Items</h3>
<div class="featured-carousel">
    <?php $first = true; ?>
    <?php while($item = $featured_result->fetch_assoc()): ?>
        <div class="featured-card <?= $first ? 'active' : '' ?>">
            <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            <h3><?= htmlspecialchars($item['name']) ?></h3>
            <p><?= htmlspecialchars($item['description']) ?></p>
            <strong>₱<?= number_format($item['price'], 2) ?></strong>
            <button class="add-to-cart-btn" data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" data-price="<?= $item['price'] ?>" data-default-qty="1">Add to Cart</button>
        </div>
    <?php $first = false; endwhile; ?>
</div>

<h3>All Menu Items</h3>
<div class="menu-grid">
    <?php while($item = $regular_result->fetch_assoc()): ?>
        <div class="card">
            <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            <h3><?= htmlspecialchars($item['name']) ?></h3>
            <p><?= htmlspecialchars($item['description']) ?></p>
            <strong>₱<?= number_format($item['price'], 2) ?></strong>
            <div class="quantity-controls">
                <button class="qty-btn" data-action="decrease">-</button>
                <input type="text" class="qty-input" value="1" size="1" readonly>
                <button class="qty-btn" data-action="increase">+</button>
            </div>
            <button class="add-to-cart-btn" data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" data-price="<?= $item['price'] ?>">Add to Cart</button>
        </div>
    <?php endwhile; ?>
</div>

<script>
const cartBtn = document.getElementById('cartBtn');
const cartModal = document.getElementById('cartModal');
const cartItemsDiv = document.getElementById('cartItems');
const cartTotalSpan = document.getElementById('cartTotal');
const closeCartBtn = document.getElementById('closeCart');

let cart = [];


function loadCartFromDatabase() {
    fetch('get_cart.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cart = data.items;
                updateCartCount();
                // No need to render cart in modal on page load here
            }
        })
        .catch(err => {
            console.error('Error loading cart:', err);
        });
}

function saveCart() {
    // This function is no longer needed as cart is managed server-side
}

function updateCartCount() {
    const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
    cartBtn.textContent = `🛒 View Cart (${totalQty})`;
}

function renderCart() {
    cartItemsDiv.innerHTML = '';
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p>Your cart is empty.</p>';
        cartTotalSpan.textContent = '0.00';
        return;
    }
    let total = 0;
    cart.forEach(item => {
        total += item.price * item.qty;
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>${item.name}</strong><br>
                    ₱${item.price.toFixed(2)} x ${item.qty}
                </div>
                <button onclick="removeFromCart(${item.menu_item_id}, '${item.name.replace(/'/g, "\'")}')" style="background: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Remove</button>
            </div>
        `;
        cartItemsDiv.appendChild(div);
    });
    cartTotalSpan.textContent = total.toFixed(2);
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function addToCart(id, name, qty = 1) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `menu_item_id=${id}&quantity=${qty}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`${qty} x ${name} added to cart!`);
            loadCartFromDatabase(); // Reload cart data after adding
        } else {
            alert(data.message || 'Failed to add to cart.');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Could not connect to server.');
    });
}

function removeFromCart(menuItemId, itemName) {
    if (confirm(`Are you sure you want to remove ${itemName} from your cart?`)) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `menu_item_id=${menuItemId}&quantity=0`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(`${itemName} removed from cart`);
                loadCartFromDatabase(); // Reload cart data after removing
                if (cart.length === 1 && cart[0].menu_item_id === menuItemId) {
                     // If removing the last item shown in modal, hide modal after reload
                     cartModal.style.display = 'none';
                }
            } else {
                alert(data.message || 'Failed to remove item from cart.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Could not connect to server.');
        });
    }
}

document.addEventListener('DOMContentLoaded', loadCartFromDatabase);


document.querySelectorAll('.add-to-cart-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = parseInt(button.getAttribute('data-id'));
        const name = button.getAttribute('data-name');
        let qty = 1; // Default quantity
        const card = button.closest('.card, .featured-card');
        if (card) {
            const qtyInput = card.querySelector('.qty-input');
            if (qtyInput) {
                qty = parseInt(qtyInput.value) || 1;
            } else {
                // For featured items without explicit quantity control, use data-default-qty or default to 1
                qty = parseInt(button.getAttribute('data-default-qty')) || 1;
            }
        }
        if (qty > 0) {
             addToCart(id, name, qty);
        } else {
             showToast('Quantity must be at least 1.');
        }
    });
});


document.querySelectorAll('.card').forEach(card => {
    const qtyInput = card.querySelector('.qty-input');
    if (qtyInput) {
        card.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                let currentQty = parseInt(qtyInput.value);
                if (btn.getAttribute('data-action') === 'increase') {
                    qtyInput.value = currentQty + 1;
                } else {
                    if (currentQty > 1) {
                        qtyInput.value = currentQty - 1;
                    }
                }
            });
        });
    }
});

cartBtn.addEventListener('click', async (e) => {
    e.preventDefault(); // Prevent default link behavior
    if(cartModal.style.display === 'block'){
        cartModal.style.display = 'none';
    } else {
        await loadCartFromDatabase(); // Ensure cart is loaded before showing
        renderCart();
        cartModal.style.display = 'block';
    }
});

closeCartBtn.addEventListener('click', () => {
    cartModal.style.display = 'none';
});

window.addEventListener('click', (e) => {
    if (e.target === cartModal) {
        cartModal.style.display = 'none';
    }
});

// Initial load of cart count
loadCartFromDatabase();

// Carousel functionality (keep existing)
const featuredCards = document.querySelectorAll('.featured-carousel .featured-card');
let currentFeatured = 0;

function showFeaturedCard(index) {
    featuredCards.forEach(card => card.classList.remove('active'));
    if (featuredCards[index]) {
        featuredCards[index].classList.add('active');
    }
}

if (featuredCards.length > 0) {
    showFeaturedCard(currentFeatured);
    setInterval(() => {
        currentFeatured = (currentFeatured + 1) % featuredCards.length;
        showFeaturedCard(currentFeatured);
    }, 3000);
}

</script>

</body>
</html>
</html>
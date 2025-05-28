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
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/view_cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
.featured-section {
    margin-top: -20px;
    position: relative;
    background: linear-gradient(135deg,rgb(113, 120, 133) 0%,rgb(78, 105, 151) 100%);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 50px;
    overflow: hidden;
}
.featured-carousel {
    position: relative;
    height: 400px;
    margin: 0 auto;

}

.featured-section h2 {
    color: white;
    font-size: 28px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
}

.featured-section h2 i {
    font-size: 24px;
}

.featured-card {
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%) scale(0.8);
    height: 400px;
    width: 100%;
    max-width: 1200px;
    color: white;
    border-radius: 20px;
    padding: 0;
    opacity: 0;
    transition: all 0.5s ease;
    pointer-events: none;
}
.featured-card img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 6px;
}
.featured-card.active {
    opacity: 1;
    transform: translateX(-50%) scale(1);
    pointer-events: all;
    z-index: 2;
}
.menu-section {
    margin-top: 60px;
}
.menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.menu-title {
    color: #1e3c72;
    font-size: 35px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-container {
    position: relative;
    width: 300px;
}
.search-box input {
    width: 100%;
    padding: 12px 20px;
    padding-left: 40px;
    border: 2px solid #e8f0fe;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
}
.search-box input:focus {
    border-color: #2a5298;
    outline: none;
    box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
}
.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #1e3c72;
}
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 20px;
}

.card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    transform-origin: center bottom;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 10px;
}

.menu-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.menu-name {
    color: #333;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

.menu-description {
    color: #666;
    font-size: 14px;
    margin: 0;
    line-height: 1.4;
}

.menu-price {
    color: #1e3c72;
    font-size: 20px;
    font-weight: 600;
    margin: 5px 0;
}

.quantity-controls {
    margin-top: auto;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
}

.quantity-controls button {
    background-color: #1b4d4d;
    color: white;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-controls button:hover {
    background-color: #2c7a7a;
}

.quantity-display {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    min-width: 30px;
    text-align: center;
}

.add-to-cart-btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.add-to-cart-btn:hover {
    transform: translateY(-2px);
}

.add-to-cart-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.cart-button {
    position: fixed;
    top: 15px;
    right: 30px;
    padding: 12px 25px;
    background: white;
    color: #1e3c72;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
    z-index: 1001;
}

.cart-button:hover {
    transform: translateY(-2px);
}


#cartBtn {
    position: fixed;
    top: 10px;
    right: 30px;
    padding: 12px 25px;
    background: white;
    color: #1e3c72;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
    z-index: 1001;
}

#cartBtn:hover {
    transform: translateY(-2px);
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

#no-results-message {
    text-align: center;
    padding: 40px;
    color: #666;
    font-size: 18px;
    background: white;
    border-radius: 15px;
    margin-top: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.sidebar {
    width: 240px;
    background-color: rgba(0, 43, 92, 0.9);
    border-radius: 10px;
    margin-top: 20px;
    color: white;
    height: 100vh;
    padding: 20px;
    position: fixed;
    top: 60px;
    left: 0;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 15px;
    color: #fff;
    text-decoration: none;
    padding: 12px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-bottom: 8px;
}

.sidebar a i {
    font-size: 16px;
    min-width: 20px;
    text-align: center;
}

.sidebar a:hover {
    background: rgba(255, 255, 255, 0.1);
}

.header {
    margin-top: -50px;
    margin-bottom: 30px;
}

.stall-info-container {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    margin-bottom: 50px;
}

.stall-info {
    flex: 1;
}

.stall-info h1 {
    font-size: 32px;
    color: #1e3c72;
    margin: 0;
    margin-bottom: 10px;
}

.stall-description {
    color: #666;
    font-size: 16px;
    margin-bottom: 0;
}

.review-btn {
    display: flex;
    align-items: center;       
    justify-content: center;    
    width: 150px;
    height: 40px;
    background: rgb(31, 99, 163);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    white-space: nowrap;
    margin-left: 1.25rem;
    font-size: 1rem;
}


.review-btn:hover {
    background:rgb(39, 102, 184);
    transform: translateY(-2px);
}

    </style>
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="header-brand">
            <img src="Picture/logo1.png" alt="EZ-Order" class="header-logo">   
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logoo-wrapper">
            <img src="Picture/logo2.png" alt="EZ-Order Logo" class="sidebar-logo">
            <div class="logoo">EZ-ORDER</div>
            <div class="divider"></div> 
            <div class="tagline">"easy orders, zero hassle"</div>
        </div>
        <h2>🍽 Stalls</h2>
        <?php 
        $stall_query = "SELECT * FROM stalls";
        $stall_result = $conn->query($stall_query);
        while($stall_item = $stall_result->fetch_assoc()): ?>
            <a href="stall_dashboard.php?stall_id=<?= $stall_item['id'] ?>">
                <i class="fas fa-store"></i> <?= htmlspecialchars($stall_item['name']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="header">
            <a href="client_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Stall Info Container -->
        <div class="stall-info-container">
            <div class="stall-info">
                <h1><?= htmlspecialchars($stall['name']) ?></h1>
                <p class="stall-description"><?= htmlspecialchars($stall['description'] ?? 'Specialty foods and beverages.') ?></p>
            </div>
            <a href="ratings.php?stall_id=<?= $stall_id ?>" class="review-btn">Review & Ratings</a>
        </div>

        <div class="featured-section">
    
            <div class="featured-carousel">
                <?php while($item = $featured_result->fetch_assoc()): ?>
                    <div class="featured-card" data-id="<?= $item['id'] ?>">
                        <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="featured-content">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p><?= htmlspecialchars($item['description']) ?></p>
                            <strong>₱<?= number_format($item['price'], 2) ?></strong>
                            <button class="add-to-cart-btn" data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" data-price="<?= $item['price'] ?>">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="menu-section">
            <div class="menu-header">
                <h2 class="menu-title"><i class="fas fa-utensils"></i> Menu</h2>
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search menu items...">
                    </div>
                </div>
            </div>

            <div class="menu-grid">
                <?php while($item = $regular_result->fetch_assoc()): ?>
                    <div class="card">
                        <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="menu-info">
                            <h3 class="menu-name"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="menu-description"><?= htmlspecialchars($item['description']) ?></p>
                            <div class="menu-price">₱<?= number_format($item['price'], 2) ?></div>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" data-action="decrease">−</button>
                            <input type="text" class="qty-input" value="1" size="1" readonly>
                            <button class="qty-btn" data-action="increase">+</button>
                        </div>
                        <button class="add-to-cart-btn" data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" data-price="<?= $item['price'] ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                <?php endwhile; ?>
            </div>

            <div id="no-results-message" style="display: none;">
                <i class="fas fa-search" style="font-size: 48px; color: #1e3c72; margin-bottom: 20px; display: block;"></i>
                No menu items found matching your search.
            </div>
        </div>
</div>

    <button class="cart-button" id="cartBtn" onclick="window.location.href='view_cart.php'">🛒 View Cart (0)</button>

<div id="cartModal" class="cart-modal" style="display:none;">
    <h3>Shopping Cart <span class="close-cart" id="closeCart">&times;</span></h3>
    <div id="cartItems"></div>
    <div style="margin-top: 15px; text-align: right;">
        <strong>Total: ₱<span id="cartTotal">0.00</span></strong>
        <button onclick="window.location.href='view_cart.php'" style="display: block; width: 100%; margin-top: 10px; padding: 10px; background-color: #e67e22; color: white; border: none; border-radius: 6px; cursor: pointer;">View Full Cart</button>
    </div>
</div>

<div id="toast" class="toast-notification"></div>

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

    // Add search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const menuItems = document.querySelectorAll('.card');
        const noResultsMessage = document.getElementById('no-results-message');
        let hasVisibleItems = false;
        
        menuItems.forEach(item => {
            const name = item.querySelector('.menu-name').textContent.toLowerCase();
            const description = item.querySelector('.menu-description').textContent.toLowerCase();
            
            if (name.includes(searchTerm) || description.includes(searchTerm)) {
                item.style.display = '';
                hasVisibleItems = true;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide no results message
        noResultsMessage.style.display = hasVisibleItems ? 'none' : 'block';
    });

</script>

</body>
</html>
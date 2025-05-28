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
    <link rel="stylesheet" href="css/view_cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
      * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f5ff;
    display: flex;
}

.admin-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 70px;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    display: flex;
    align-items: center;
    padding: 0 20px;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.header-brand {
    display: flex;
    align-items: center;
}

.header-logo {
    height: 50px;
    margin-right: 15px;
}

.sidebar {
    width: 240px;
    background-color: rgba(0, 43, 92, 0.9);
    border-radius: 10px;
    margin-top: 30px;
    color: white;
    height: 100vh;
    padding: 20px;
    position: fixed;
    top: 60px;
    left: 0;
}

.sidebar h2 {
    font-size: 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
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

.logoo-wrapper {
    text-align: center;
    margin-bottom: 30px;
}

.logoo-wrapper img {
    width: 120px;
    height: auto;
    margin-bottom: 15px;
}

.logoo {
    font-size: 24px;
    font-weight: bold;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.divider {
    height: 2px;
    background: rgba(255,255,255,0.1);
    margin: 15px 0;
}

.tagline {
    font-style: italic;
    color: rgba(255,255,255,0.8);
    font-size: 14px;
}

.content {
    flex: 1;
    margin-left: 280px;
    padding: 90px 30px 30px;
}

.featured-section {
    margin-top: -60px;
    position: relative;
    background: linear-gradient(135deg,rgb(113, 120, 133) 0%,rgb(78, 105, 151) 100%);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 50px;
    overflow: hidden;
}

.featured-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
    opacity: 0.1;
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

.featured-carousel {
    position: relative;
    height: 400px;
    margin: 0 auto;
}

.featured-card {
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%) scale(0.8);
    height: 400px;
    width: 100%;
    max-width: 1200px;
    background-color: white;
    border-radius: 20px;
    padding: 0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    opacity: 0;
    transition: all 0.5s ease;
    pointer-events: none;
}

.featured-card.active {
    opacity: 1;
    transform: translateX(-50%) scale(1);
    pointer-events: all;
    z-index: 2;
}

.featured-card.prev,
.featured-card.next {
    opacity: 0.5;
    z-index: 1;
}

.featured-card.prev {
    transform: translateX(-150%) scale(0.8);
}

.featured-card.next {
    transform: translateX(50%) scale(0.8);
}

.featured-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 20px 20px 0 0;
}

.featured-content {
    padding: 20px;
    text-align: center;
}

.featured-content h3 {
    color:rgb(43, 46, 53);
    font-size: 22px;
    margin-bottom: 10px;
}

.featured-content p {
    color: #666;
    font-size: 15px;
    line-height: 1.5;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.featured-content strong {
    color:rgb(83, 109, 153);
    font-size: 24px;
    display: block;
    margin-bottom: 20px;
}

.carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    z-index: 3;
    display: flex;
    justify-content: space-between;
    padding: 0 20px;
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
    font-size: 24px;
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
}

.card:hover {
    transform: translateY(-5px) scale(1.02);
}

.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.card-content {
    padding: 20px;
}

.card h3 {
    color:rgb(43, 46, 53);
    margin-bottom: 8px;
    font-size: 18px;
}

.card p {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
    line-height: 1.4;
}

.card strong {
    color:rgb(83, 109, 153);
    font-size: 20px;
    display: block;
    margin-bottom: 15px;
}

.quantity-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin: 20px 0;
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

.cart-modal {
    position: fixed;
    top: 80px;
    right: 30px;
    width: 350px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.15);
    z-index: 1001;
    padding: 20px;
}

.cart-modal h3 {
    color: #1e3c72;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-item {
    padding: 12px 0;
    border-bottom: 1px solid #e8f0fe;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-item:last-child {
    border-bottom: none;
}

.close-cart {
    color: #1e3c72;
    cursor: pointer;
    font-size: 24px;
}

.cart-total {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #e8f0fe;
    text-align: right;
    color: #1e3c72;
    font-weight: bold;
    font-size: 18px;
}

.view-cart-btn {
    display: block;
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
}

.view-cart-btn:hover {
    opacity: 0.9;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.5s ease backwards;
}

.card:nth-child(n) {
    animation-delay: calc(0.1s * var(--i, 0));
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
    <div class="content" style="margin-top: 70px;">
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
                <h2 class="menu-title"><i class="fas fa-utensils"></i> All Menu Items</h2>
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search menu items...">
                    </div>
                </div>
            </div>
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

                <button class="add-to-cart-btn" data-id="<?= $item['id'] ?>" disabled>
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
        <?php endwhile; ?>
    </div>
    <div id="no-results-message" style="display: none; text-align: center; padding: 40px; color: #666; font-size: 18px;">
        <i class="fas fa-search" style="font-size: 48px; color: #1e3c72; margin-bottom: 20px; display: block;"></i>
        No menu items found matching your search.
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
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const menuItems = document.querySelectorAll('.card');
        const noResultsMessage = document.getElementById('no-results-message');
        let hasVisibleItems = false;
        
        menuItems.forEach(item => {
            const name = item.querySelector('h3').textContent.toLowerCase();
            const description = item.querySelector('p').textContent.toLowerCase();
            
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
                    cartBtn.textContent = `🛒 Cart (${count})`;
            } else {
                console.error('Error fetching cart data:', data.message || 'Unknown error');
                    cartBtn.textContent = `🛒 Cart (Error)`;
            }
        } catch (error) {
            console.error('Error loading cart:', error);
                cartBtn.textContent = `🛒 Cart (Error)`;
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

        // Enhanced carousel functionality
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('.featured-carousel');
            const cards = Array.from(document.querySelectorAll('.featured-card'));
            const dots = document.querySelector('.carousel-dots');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            
            let currentIndex = 0;
            let interval;

            // Create dots
            cards.forEach((_, i) => {
                const dot = document.createElement('div');
                dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
                dot.addEventListener('click', () => goToSlide(i));
                dots.appendChild(dot);
            });

            function updateCards() {
                cards.forEach((card, i) => {
                    card.className = 'featured-card';
                    if (i === currentIndex) {
                        card.classList.add('active');
                    } else if (i === (currentIndex - 1 + cards.length) % cards.length) {
                        card.classList.add('prev');
                    } else if (i === (currentIndex + 1) % cards.length) {
                        card.classList.add('next');
                    }
                });

                // Update dots
                Array.from(dots.children).forEach((dot, i) => {
                    dot.classList.toggle('active', i === currentIndex);
                });
            }

            function nextSlide() {
                currentIndex = (currentIndex + 1) % cards.length;
                updateCards();
            }

            function prevSlide() {
                currentIndex = (currentIndex - 1 + cards.length) % cards.length;
                updateCards();
            }

            function goToSlide(index) {
                currentIndex = index;
                updateCards();
                resetInterval();
            }

            function resetInterval() {
                clearInterval(interval);
                interval = setInterval(nextSlide, 5000);
            }

            // Event listeners
            prevBtn.addEventListener('click', () => {
                prevSlide();
                resetInterval();
            });

            nextBtn.addEventListener('click', () => {
                nextSlide();
                resetInterval();
            });

            // Initialize
            updateCards();
            resetInterval();

            // Search functionality
            const searchInput = document.getElementById('menuSearch');
            const menuItems = document.querySelectorAll('.card');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                menuItems.forEach(item => {
                    const name = item.querySelector('h3').textContent.toLowerCase();
                    const description = item.querySelector('p').textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || description.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
</script>

</body>
</html>
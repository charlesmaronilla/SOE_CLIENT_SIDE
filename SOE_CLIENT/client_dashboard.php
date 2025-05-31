<?php
session_start();
require_once 'db_connection.php';

$stall_result = $conn->query("SELECT * FROM stalls");
$featured_result = $conn->query("SELECT * FROM menu_items WHERE is_featured = 1 AND available = 1 ORDER BY category");
$regular_result = $conn->query("SELECT * FROM menu_items WHERE is_featured = 0 AND available = 1 ORDER BY category, name");

// Add debug output
if (!$featured_result) {
    die("Error in featured items query: " . $conn->error);
}

if ($featured_result->num_rows === 0) {
    echo "<!-- No featured items found -->";
}
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
    height: 60px;
    background-color: #0f4c75;
    color: white;
    display: flex;
    align-items: center;
    padding: 0 20px;
    z-index: 1000;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.header-brand {
    display: flex;
    align-items: center;
}

.header-logo {
    margin-left: -10px;
    margin-top: 5px;
    height: 60px;
    object-fit: contain;
}

.sidebar {
    width: 240px;
    background-color: rgba(0, 43, 92, 0.9);
    border-radius: 10px;
    margin-top: 10px;
    color: white;
    height: 100vh;
    padding: 20px;
    position: fixed;
    top: 60px;
    left: 0;
}

.sidebar .divider {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    margin: 1rem auto;
    width: 90%;
}

.logoo-wrapper {
    text-align: center;
    padding: 20px 0;
}

.logoo-wrapper img {
    position: relative;
    display: inline-block;
    margin-left: -10px;
    height: 150px;
    width: 190px;
}

.logoo {
    color: white;
    font-size: 24px;
    font-weight: bold;
    margin: 10px 0;
}

.tagline {
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    font-style: italic;
    margin-bottom: 20px;
}

.sidebar h2 {
    font-size: 20px;
    margin: 20px 0 15px 0;
    color: white;
}


.content {
    flex: 1;
    margin-left: 280px;
    padding: 90px 30px 30px;
}


.featured-carousel {
    position: relative;
    height: 400px;
    margin: 0 auto;
    z-index: 2;
}

.featured-card {
    position: absolute;
    top: -1px;
    left: 50%;
    transform: translateX(-50%) scale(0.8);
    width: 100%;
    max-width: 800px;
    height: 100%;
    max-height: 480px;
    border-radius: 20px;
    padding: 20px;
    opacity: 0;
    transition: all 0.5s ease;
    pointer-events: none;
    background: linear-gradient(135deg, rgb(133, 114, 113) 0%, rgb(78, 105, 151) 100%);

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

.featured-section {
    margin-top: -110px;
    position: relative;
    background: transparent;
    padding: 30px;
    margin-bottom: 50px;
    overflow: hidden;
    min-height: 500px;
    display: flex;
    align-items: stretch;
}

.featured-carousel {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    width: 100%;
}

.featured-card {
    flex: 1 1 calc(33.333% - 20px); /* Three cards per row */
    display: flex;
    flex-direction: column;
    border-radius: 10px;
    overflow: hidden;
    width: 100%;
    max-width: 1400px;
    height: 500px;
    margin-top: -20px;
    
}

.featured-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    flex-grow: 1;
}

.featured-content {
    padding: 15px;
    background: transparent;
    position: relative;
    z-index: 2;
}

.featured-content h3 {
    margin-left: -10px;
    font-size: 30px;
    color:rgb(43, 46, 53);
}

.featured-content p {
    margin-left: 5px;
    font-size: 16px;
    color:rgb(59, 67, 82);
    margin-bottom: 4px;
    line-height: 1.5;
}
.category {
    display: inline-block;
    padding: 4px 12px;
    background:rgba(238, 255, 84, 0.99);
    color: #1e3c72;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 5px;
}

.featured-content strong {
    margin-left: 7px;
    font-size: 28px;
    color:rgb(51, 83, 141);
}

.quantity-contr {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 12px;
}

.quantity-contr button {
    margin-left: -505px;
    margin-right: 505px;
    width: 35px;
    height: 35px;
    background-color:rgb(9, 64, 100);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.quantity-contr button:hover {
    background-color: #2980b9;
}

.quantity-display {
    margin-left: -505px;
    margin-right: 505px;
    width: 40px;
    height: 35px;
    background-color: #f2f2f2;
    border: 1px solid #ccc;
    border-radius: 6px;
    text-align: center;
    line-height: 35px;
    font-size: 16px;
    font-weight: 600;
    user-select: none;
}

.featured-btn button {
    width: 18%;
    margin-top: 12px;
    padding: 10px 15px;
    background-color:rgb(34, 64, 121);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s ease, transform 0.1s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.featured-btn button :hover {
    background-color:rgb(45, 104, 158);
}

.featured-btn button i {
    font-size: 18px;
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
    width: 340px;
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

.category-filter {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.category-btn {
    padding: 8px 16px;
    border: 2px solid #1e3c72;
    background: white;
    color: #1e3c72;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.category-btn:hover {
    background: rgba(30, 60, 114, 0.1);
}

.category-btn.active {
    background: #1e3c72;
    color: white;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
    width: 340px;
    margin: 0 auto;
}

.search-box i {
    position: absolute;
    left: 15px;
    color: #888;
}

.search-box input {
    width: 100%;
    padding: 10px 10px 10px 35px;
    border: 1px solidrgb(32, 83, 179);
    border-radius: 20px;
}

.visually-hidden {
    position: absolute;
    left: -9999px;
    top: auto;
    width: 1px;
    height: 1px;
    overflow: hidden;
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
    box-shadow: 0 4px 15px rgba(233, 214, 42, 0.05);
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

.quantity-control {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin: 20px 0;
}

.quantity-control button {
    background-color:rgb(34, 64, 121);
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

.quantity-control button:hover {
    background-color:rgb(45, 104, 158);
}

.qty-display {
    width: 40px;
    height: 35px;
    background-color: #f2f2f2;
    border: 1px solid #ccc;
    border-radius: 6px;
    text-align: center;
    line-height: 35px;
    font-size: 16px;
    font-weight: 600;
    user-select: none;
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
    transition: all 0.3s ease;
    z-index: 1001;
}

.cart-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.cart-button.bounce {
    animation: cartBounce 0.5s ease;
}

.cart-button.shake {
    animation: cartShake 0.5s ease;
}

/* Cart icon styles */
.cart-icon {
    font-size: 1.2em;
    transition: transform 0.3s ease;
}

.cart-button:hover .cart-icon {
    transform: scale(1.1);
}

/* Cart count badge */
.cart-count {
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    position: absolute;
    top: -5px;
    right: -5px;
    transition: all 0.3s ease;
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
    margin-top: -8px;
    margin-bottom: 10px;
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
    font-weight: 900;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
}

.view-cart-btn:hover {
    opacity: 0.9;
}

.card {
    animation: fadeIn 3.5s ease backwards;
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

.category-tag {
    display: inline-block;
    padding: 4px 12px;
    background:rgba(238, 255, 84, 0.99);
    color: #1e3c72;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 5px;
}

#no-results-message {
    margin-top: 80px;
    margin-bottom: 50px;
    display: none;
    text-align: center;
    color: #4b5563; /* Gray-700 */
    font-size: 18px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    transition: all 0.3s ease-in-out;
}

#no-results-message i {
    font-size: 56px;
    color: #3b82f6; /* Tailwind Blue-500 */
    margin-bottom: 20px;
    display: block;
    animation: bounce 1.5s infinite;
}

/* Optional: Simple bounce animation */
@keyframes bounce {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-8px);
    }
}


.confirm-modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    text-align: center;
    max-width: 400px;
    width: 90%;
    z-index: 1003;
}

.confirm-modal h3 {
    color:rgb(69, 74, 83);
    font-size: 24px;
    margin-bottom: 15px;
}

.confirm-modal p {
    color: #666;
    font-size: 16px;
    margin-bottom: 25px;
    line-height: 1.5;
}

.modal-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.modal-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s;
}

.modal-btn:hover {
    transform: translateY(-2px);
}

.cancel-modal-btn {
    background: #f8f9fa;
    color:rgb(67, 74, 87);
}

.confirm-modal-btn {
    background: #ffd700;
    color: #333;
}

.no-featured-items {
    text-align: center;
            color: white;
    padding: 40px;
    font-size: 18px;
}

.no-featured-items i {
    font-size: 48px;
    margin-bottom: 20px;
    display: block;
}

.featured-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 20px;
}

/* Cart Button Animation Styles */
@keyframes cartBounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

@keyframes cartShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-3px); }
    75% { transform: translateX(3px); }
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
        <?php while($stall_item = $stall_result->fetch_assoc()): ?>
            <a href="stall_dashboard.php?stall_id=<?= $stall_item['id'] ?>">
                <i class="fas fa-store"></i> <?= htmlspecialchars($stall_item['name']) ?>
        </a>
    <?php endwhile; ?>
</div>

    <!-- Main Content -->
<div class="content">
        <!-- Featured Section -->
        <div class="featured-section">
    <div class="featured-carousel">
                <?php 
                $featured_count = 0;
                while($item = $featured_result->fetch_assoc()): 
                    $featured_count++;
                ?>
                    <div class="featured-card" data-category="<?= htmlspecialchars($item['category']) ?>" data-id="<?= $item['id'] ?>">
                <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="featured-content">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p data-id="<?= htmlspecialchars($item['id']) ?>">
                    <?= htmlspecialchars($item['description']) ?> - <span class="category"><?= htmlspecialchars($item['category']) ?></span></p>
                <strong>₱<?= number_format($item['price'], 2) ?></strong>
                
                 <div class="featured-qty-btn">
                <div class="quantity-contr ">
                    <button class="decrement-btn" data-id="<?= $item['id'] ?>">−</button>
                    <div class="quantity-display" id="qty-<?= $item['id'] ?>">0</div>
                    <button class="increment-btn" data-id="<?= $item['id'] ?>">+</button>
                </div>
               </div>

                <div class="featured-btn">
                    <div class="add-btn ">
                        <button class="add-to-cart-btn" data-id="<?= $item['id'] ?>">
                         <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button></div>
                  </div>          
                 </div>
            </div>
        <?php endwhile; ?>
                <?php if ($featured_count === 0): ?>
                    <div class="no-featured-items">
                        <i class="fas fa-info-circle"></i>
                        <p>No featured items available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
    </div>

        <!-- Menu Section -->
        <div class="menu-section">
            <div class="menu-header">
                <h2 class="menu-title"><i class="fas fa-utensils"></i> All Menu Items</h2>
                <div class="search-container">
                    <div class="category-filter">
                        <button class="category-btn" data-category="all">All</button>
                        <button class="category-btn" data-category="Meal">Meals</button>
                        <button class="category-btn" data-category="Beverage">Beverage</button>
                        <button class="category-btn" data-category="Dessert">Desserts</button>
                    </div>

                <div class="search-box">
                    <label for="searchInput" class="visually-hidden">Search Menu Items</label>
                    <i class="fas fa-search"></i>
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Search menu items..." 
                        aria-label="Search menu items"
                    >
                </div>
                
                </div>
            </div>

    <div class="menu-grid">
        <?php while($item = $regular_result->fetch_assoc()): ?>
                    <div class="card" data-id="<?= $item['id'] ?>" data-category="<?= htmlspecialchars($item['category']) ?>">
                <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="card-content">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p><?= htmlspecialchars($item['description']) ?></p>
                <strong>₱<?= number_format($item['price'], 2) ?></strong>
                            <div class="category-tag"><?= htmlspecialchars($item['category']) ?></div>

                <div class="quantity-control">
                    <button class="decrement-btn" data-id="<?= $item['id'] ?>">−</button>
                    <div class="qty-display" id="qty-<?= $item['id'] ?>">0</div>
                    <button class="increment-btn" data-id="<?= $item['id'] ?>">+</button>
                </div>

                            <button class="add-to-cart-btn" data-id="<?= $item['id'] ?>" disabled>
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
            </div>
        <?php endwhile; ?>
            </div>

            <div id="no-results-message">
                <i class="fas fa-search"></i>
                No menu items found matching your search.
            </div>
    </div>
</div>

    <!-- Quick summary of cart -->
    <button class="cart-button" id="cartBtn">
        <span class="cart-icon">🛒</span>
        <span>View Cart</span>
        <span class="cart-count">0</span>
    </button>

<div class="cart-modal" id="cartModal" style="display:none;">
        <h3>EZ Tray<span class="close-cart" id="closeCart" style="cursor:pointer;color:#34495e;float:right;">&times;</span></h3>
    <div id="cartItems"></div>
    <div style="margin-top: 15px; text-align: right;">
        <strong>Total: ₱<span id="cartTotal">0.00</span></strong>
            <button onclick="window.location.href='view_cart.php'" style="display: block; width: 100%; margin-top: 10px; padding: 10px; background-color:rgb(47, 66, 150); color: white; border: none; border-radius: 6px; cursor: pointer;">Go to Cart to Checkout</button>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal-overlay" id="confirmModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1002;">
        <div class="confirm-modal">
            <h3>Remove Item</h3>
            <p>Are you sure you want to remove <span id="itemName"></span> from your cart?</p>
            <div class="modal-buttons">
                <button class="modal-btn cancel-modal-btn" onclick="closeConfirmModal()">Cancel</button>
                <button class="modal-btn confirm-modal-btn" onclick="confirmRemove()">Remove</button>
            </div>
    </div>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded'); // Debug log
            
            // Get all necessary elements
            const categoryBtns = document.querySelectorAll('.category-btn');
            const searchInput = document.getElementById('searchInput');
            const noResultsMessage = document.getElementById('no-results-message');
            
            console.log('Category buttons found:', categoryBtns.length); // Debug log
            
            
            function filterItems() {
        const selectedCategory = document.querySelector('.category-btn.active')?.getAttribute('data-category') || 'all';
        const searchTerm = searchInput.value.toLowerCase().trim();

        // Handle only .card (regular menu items)
        const menuItems = document.querySelectorAll('.card');
        let visibleCount = 0;

        menuItems.forEach(item => {
            const itemCategory = item.getAttribute('data-category');
            const name = item.querySelector('h3')?.textContent.toLowerCase() || '';
            const description = item.querySelector('p')?.textContent.toLowerCase() || '';

            const matchesCategory = selectedCategory === 'all' || itemCategory === selectedCategory;
            const matchesSearch = searchTerm === '' || name.includes(searchTerm) || description.includes(searchTerm);

            if (matchesCategory && matchesSearch) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Always show featured items regardless of filter
        const featuredItems = document.querySelectorAll('.featured-card');
        featuredItems.forEach(item => {
            item.style.display = '';
        });

        // Show/hide 'No results' message
        noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
    }

            
            // Add click event listeners to category buttons
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    console.log('Category button clicked:', btn.getAttribute('data-category')); // Debug log
                    
                    // Remove active class from all buttons
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    
                    // Add active class to clicked button
                    btn.classList.add('active');
                    
                    // Filter items
                    filterItems();
                });
            });
            
            // Add input event listener to search box
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    console.log('Search input changed:', searchInput.value); // Debug log
                    filterItems();
                });
            }
            
            // Set initial active category
            const defaultCategory = document.querySelector('.category-btn[data-category="all"]');
            if (defaultCategory) {
                defaultCategory.classList.add('active');
            }
            
            // Initial filtering
            filterItems();
        });
    
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

        let itemToRemove = null;
        let itemToRemoveName = '';

        function showConfirmModal(menuItemId, itemName) {
            itemToRemove = menuItemId;
            itemToRemoveName = itemName;
            document.getElementById('itemName').textContent = itemName;
            document.getElementById('confirmModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
            document.body.style.overflow = ''; // Restore scrolling
            itemToRemove = null;
            itemToRemoveName = '';
        }

        function confirmRemove() {
            if (itemToRemove) {
                removeItemFromCart(itemToRemove);
                closeConfirmModal();
            }
        }

        async function removeItemFromCart(menuItemId) {
            try {
                const response = await fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `menu_item_id=${menuItemId}&quantity=0`
                });

                const result = await response.json();
                if (result.success) {
                    await loadCart();
                    // Show success message
                    const toast = document.createElement('div');
                    toast.style.cssText = `
                        position: fixed;
                        top: 20px;
                        left: 50%;
                        transform: translateX(-50%);
                        background-color:rgb(224, 228, 31);
                        color: #1e3c72;
                        padding: 12px 24px;
                        border-radius: 4px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                        z-index: 1000;
                    `;
                    toast.textContent = `${itemToRemoveName} removed from cart`;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                } else {
                    alert('Failed to remove item: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Could not connect to server');
            }
        }
    
    async function loadCart() {
        try {
            const response = await fetch('get_cart.php');
            const data = await response.json();

            if (data.success) {
                cartItemsDiv.innerHTML = '';
                let total = 0, count = 0;
                
                data.items.forEach(item => {
                    item.price = parseFloat(item.price);
                    total += item.price * item.qty;
                        count += parseInt(item.qty);

                    const div = document.createElement('div');
                    div.classList.add('cart-item');
                    div.innerHTML = `
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 0 10px;">
                                <div style="margin-left: 5px;">
                                <strong>${item.name}</strong><br>
                                ₱${item.price.toFixed(2)} x ${item.qty}
                            </div>
                                <button onclick="showConfirmModal(${item.menu_item_id}, '${item.name.replace(/'/g, "\\'")}')" class="remove-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                        </div>
                    `;
                    cartItemsDiv.appendChild(div);
                });

                cartTotalSpan.textContent = total.toFixed(2);
                    cartBtn.innerHTML = `🛒 View Cart (${count})`;
                    
                    if (count > 0) {
                        cartBtn.classList.add('has-items');
                    } else {
                        cartBtn.classList.remove('has-items');
                    }
            } else {
                console.error('Error fetching cart data:', data.message || 'Unknown error');
                    cartBtn.innerHTML = `🛒 View Cart (0)`;
            }
        } catch (error) {
            console.error('Error loading cart:', error);
                cartBtn.innerHTML = `🛒 View Cart (0)`;
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
                        // First, get the current cart to check for existing items
                        const cartResponse = await fetch('get_cart.php');
                        const cartData = await cartResponse.json();
                        
                        // Check if item already exists in cart
                        let existingQty = 0;
                        if (cartData.success && cartData.items) {
                            const existingItem = cartData.items.find(item => item.menu_item_id === id);
                            if (existingItem) {
                                existingQty = parseInt(existingItem.qty);
                                console.log(`Item already in cart with quantity: ${existingQty}`);
                            }
                        }

                        // Add new quantity to existing quantity
                        const totalQty = existingQty + quantity;
                        console.log(`Adding to cart: ${id} with total quantity ${totalQty}`);

                    const response = await fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: `menu_item_id=${id}&quantity=${totalQty}`
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
                            // Show success message with combined quantity
                        const toast = document.createElement('div');
                        toast.style.cssText = `
                            position: fixed;
                            top: 20px;
                            left: 50%;
                            transform: translateX(-50%);
                            background-color:rgb(192, 202, 49);
                            color: #1e3c72;
                            padding: 12px 24px;
                            border-radius: 4px;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                            z-index: 1000;
                        `;
                            toast.textContent = existingQty > 0 
                                ? `Updated ${name} quantity to ${totalQty} in cart!`
                                : `Added ${quantity} x ${name} to cart!`;
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
                    alert('Could not connect to server or process response');
                }
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

        // Initialize featured carousel
        const featuredCards = document.querySelectorAll('.featured-carousel .featured-card');
        let currentFeatured = 0;

        function showFeaturedCard(index) {
            console.log('Showing featured card:', index); // Debug log
            featuredCards.forEach((card, i) => {
                card.classList.remove('active');
                if (i === index) {
                    card.classList.add('active');
                }
            });
        }

        if (featuredCards.length > 0) {
            console.log('Found featured cards:', featuredCards.length); // Debug log
            showFeaturedCard(currentFeatured);
            
            // Auto-rotate featured cards
            setInterval(() => {
                currentFeatured = (currentFeatured + 1) % featuredCards.length;
                showFeaturedCard(currentFeatured);
            }, 5000); // Change slide every 5 seconds
        } else {
            console.log('No featured cards found'); // Debug log
        }

        // Add event listeners for increment and decrement buttons
    document.querySelectorAll('.increment-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const qtyDisplay = document.getElementById(`qty-${id}`);
                const currentQty = parseInt(qtyDisplay.textContent);
                qtyDisplay.textContent = currentQty + 1;
            updateAddToCartBtn(id);
        });
    });
    
    document.querySelectorAll('.decrement-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const qtyDisplay = document.getElementById(`qty-${id}`);
                const currentQty = parseInt(qtyDisplay.textContent);
                if (currentQty > 0) {
                    qtyDisplay.textContent = currentQty - 1;
                updateAddToCartBtn(id);
            }
        });
    });

        const style = document.createElement('style');
        style.textContent = `
            .cart-button.has-items {
                background: #1e3c72;
                color: white;
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                }
                100% {
                    transform: scale(1);
                }
            }
        `;
        document.head.appendChild(style);

        // Update the remove button style
        const removeButtonStyle = document.createElement('style');
        removeButtonStyle.textContent = `
            .remove-btn {
                background: transparent;
                color:rgb(201, 174, 21);
                border: none;
                padding: 8px;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .remove-btn:hover {
                transform: scale(1.1);
                color:rgb(218, 204, 81);
            }
            
            .remove-btn i {
                font-size: 16px;
            }

            .cart-item {
                padding: 8px 0;
                border-bottom: 1px solid #e8f0fe;
            }

            .cart-item:last-child {
                border-bottom: none;
            }
        `;
        document.head.appendChild(removeButtonStyle);

        // Close modal when clicking outside
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirmModal();
            }
        });
</script>
</body>
</html>
<?php
session_start();
require_once 'db_connection.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    // Redirect to dashboard if no valid order ID is provided
    header('Location: client_dashboard.php');
    exit;
}

// Fetch order details including stall name and contact number from the first item's stall
$order_query = "SELECT o.id_number, o.customer_name, o.message, o.total_amount, o.created_at, s.name as stall_name, s.contact_number as stall_contact 
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN menu_items mi ON oi.menu_item_id = mi.id
                JOIN stalls s ON mi.stall_id = s.id
                WHERE o.id = ? LIMIT 1"; // Use LIMIT 1 as we only need one row for order details

$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    // Redirect if order not found
    header('Location: client_dashboard.php');
    exit;
}

// Fetch order items (for 'View Details' or detailed section)
$order_items_query = "SELECT menu_item_id, quantity, price_at_order, item_name_at_order FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($order_items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items_result = $stmt->get_result();

$order_items = [];
while ($row = $order_items_result->fetch_assoc()) {
    $order_items[] = $row;
}

$conn->close();

// Format date and time
$created_at = new DateTime($order['created_at']);
$order_date = $created_at->format('F d, Y');
$order_time = $created_at->format('h:i A');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #d3d3d3; /* Grey background */
            display: flex;
            flex-direction: column; /* Stack header, sidebar+content */
            min-height: 100vh;
            color: #333; /* Default text color */
        }
         .header {
            background-color: #007bff; /* Blue header */
            color: white;
            padding: 15px 20px;
            font-size: 24px;
            font-weight: bold;
        }
         .main-layout {
            display: flex;
            flex: 1; /* Take remaining height */
         }
        .sidebar {
            width: 200px; /* Adjust sidebar width as needed */
            background-color: #f4f4f4; /* Light grey sidebar */
            padding: 20px;
            border-right: 1px solid #ccc;
        }
        .sidebar a {
            display: block;
            margin-bottom: 10px;
            color: #333;
            text-decoration: none;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .checkmark-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #007bff; /* Example blue for circle */
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .checkmark-circle svg {
            width: 50px;
            height: 50px;
            fill: white; /* White checkmark */
        }
        h1 {
            color: #333; /* Dark text */
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 30px;
            font-size: 18px;
            color: #555; /* Slightly darker grey */
        }
        .order-card {
            background: #c0c0c0; /* Slightly darker grey for the card */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 40px;
            color: #333;
            text-align: left;
            position: relative;
            width: 80%; /* Adjust card width */
            max-width: 400px;
        }
        .order-card h2 {
            margin: 0 0 10px 0;
            font-size: 36px;
            color: #333;
        }
        .order-card .view-details-link {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 14px;
            color: #007bff;
            text-decoration: none;
        }
        .order-card .view-details-link:hover {
            text-decoration: underline;
        }
        .order-card p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
         .order-card strong {
            color: #333;
         }
        .order-card .datetime {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }
        .tracking-section {
            width: 100%; /* Make tracking section take full content width */
            max-width: 500px; /* Limit max width */
        }
        .tracking-section h3 {
            margin-bottom: 20px;
            font-size: 20px;
             color: #333; /* Dark text */
        }
        .tracking-progress {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            padding: 0 20px;
        }
        .tracking-progress::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 10%;
            right: 10%;
            height: 4px;
            background-color: #aaa; /* Grey line */
            z-index: 0;
            transform: translateY(-2px);
        }
        .tracking-point {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff; /* Active point color */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
            font-size: 24px;
            color: white;
        }
        .tracking-point.inactive {
            background-color: #ccc; /* Inactive point color */
            color: #888; /* Darker grey for inactive icon */
        }
        .tracking-point svg {
             width: 24px;
             height: 24px;
             fill: white;
        }
         .tracking-point.inactive svg {
             fill: #888;
        }

        .detailed-order-summary {
            margin-top: 40px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            color: #333;
            text-align: left;
             width: 80%;
             max-width: 500px;
        }
         .detailed-order-summary h2 {
             margin-top: 0;
         }
         .detailed-order-summary .order-item {
             border-bottom: 1px solid #eee;
             padding: 10px 0;
         }
         .detailed-order-summary .total-amount {
             font-weight: bold;
             margin-top: 10px;
             text-align: right;
         }
    </style>
</head>
<body>

    <div class="header">Order Number #<?= htmlspecialchars($order_id) ?></div>

    <div class="main-layout">
        <div class="sidebar">
            <a href="client_dashboard.php">Home</a>
            <a href="view_cart.php">Cart</a>
            <!-- Add other sidebar links here if needed -->
        </div>

        <div class="content">
            <div class="checkmark-circle">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                  <path d="M18.286 43.323L0 25.037l4.95-4.95 13.336 13.337 28.427-28.427 4.95 4.95L18.286 43.323z"/>
                </svg>
            </div>

            <h1>Your order has been taken and is being attend to</h1>

            <div class="order-card">
                <a href="#detailed-summary" class="view-details-link">View Details</a>
                <h2>#<?= htmlspecialchars($order_id) ?></h2>
                <p><?= htmlspecialchars($order['stall_name']) ?></p>
                <div class="datetime">
                    <p><?= htmlspecialchars($order_date) ?></p>
                    <p>|</p>
                    <p><?= htmlspecialchars($order_time) ?></p>
                </div>
                 <?php if (!empty($order['stall_contact'])): ?>
                    <p style="margin-top: 15px;"><strong>Seller Contact:</strong> <?= htmlspecialchars($order['stall_contact']) ?></p>
                <?php endif; ?>
            </div>

            <div class="tracking-section">
                <h3>Track Your Order</h3>
                <div class="tracking-progress">
                    <div class="tracking-point">
                       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" fill="currentColor"></path></svg>
                    </div>
                    <div class="tracking-point inactive">
                       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M22 5.5H9c-2.21 0-4 1.79-4 4v9c0 1.1.9 2 2 2h13c1.1 0 2-.9 2-2v-9c0-2.21-1.79-4-4-4zm-2 2.5c0 .55.45 1 1 1s1-.45 1-1-.45-1-1-1-1 .45-1 1zm-2 0c0 .55.45 1 1 1s1-.45 1-1-.45-1-1-1-1 .45-1 1zm-4 0c0 .55.45 1 1 1s1-.45 1-1-.45-1-1-1-1 .45-1 1zm-3.5 2c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm11.5 7H7v-3c0-.55.45-1 1-1h8c.55 0 1 .45 1 1v3z" fill="currentColor"></path></svg>
                    </div>
                    <div class="tracking-point inactive">
                       <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 4h-3V3c0-.55-.45-1-1-1H8c-.55 0-1 .45-1 1v1H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM8.86 4h6.28V3.5c0-.28-.22-.5-.5-.5H9.36c-.28 0-.5.22-.5.5V4zm10.58 16H4V6h16v14zM7 12.51S7.5 12 8 12c.5 0 1 .51 1 1s-.5 1-1 1h-.5v1h.5c.83 0 1.5-.67 1.5-1.5 0-.6-.25-1.1-.64-1.46L11 10.54V9h1V8h-2V7H9v3.5c0 .83.67 1.5 1.5 1.5h.5v-.5h-.5c-.5 0-1-.5-1-1s.5-1 1-1h.5V9h1v1.5c-.83 0-1.5.67-1.5 1.5zM15 14h-2v-1h2v-2h-2v-1h2V8h-3v8h3v-1zM15.71 12l.79-.79.79.79L17 12.71l-.71-.71-.71.71z" fill="currentColor"></path></svg>
                    </div>
                </div>
            </div>

            <div id="detailed-summary" class="detailed-order-summary">
                <h2>Order Details</h2>
                <p><strong>Order ID:</strong> #<?= htmlspecialchars($order_id) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($order_date) ?></p>
                <p><strong>Time:</strong> <?= htmlspecialchars($order_time) ?></p>
                <p><strong>Stall:</strong> <?= htmlspecialchars($order['stall_name']) ?></p>
                 <?php if (!empty($order['stall_contact'])): ?>
                    <p><strong>Seller Contact:</strong> <?= htmlspecialchars($order['stall_contact']) ?></p>
                <?php endif; ?>
                <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                <p><strong>ID Number:</strong> <?= htmlspecialchars($order['id_number']) ?></p>
                <?php if (!empty($order['message'])): ?>
                    <p><strong>Message:</strong> <?= htmlspecialchars($order['message']) ?></p>
                <?php endif; ?>
                
                <h3>Items Ordered</h3>
                <?php foreach ($order_items as $item): ?>
                    <div class="order-item">
                        <?= htmlspecialchars($item['quantity']) ?> x <?= htmlspecialchars($item['item_name_at_order']) ?> (₱<?= number_format($item['price_at_order'], 2) ?> each)
                    </div>
                <?php endforeach; ?>

                <div class="total-amount">Total Amount: ₱<?= number_format($order['total_amount'], 2) ?></div>
            </div>
        </div>
    </div>

</body>
</html> 
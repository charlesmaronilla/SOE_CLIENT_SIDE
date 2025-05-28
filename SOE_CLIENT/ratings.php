<?php
require_once 'db_connection.php';

$stall_id = isset($_GET['stall_id']) ? (int)$_GET['stall_id'] : 0;
if ($stall_id <= 0) {
    die("Invalid stall ID.");
}

$stmt = $conn->prepare("SELECT name FROM stalls WHERE id = ?");
$stmt->bind_param("i", $stall_id);
$stmt->execute();
$stmt->bind_result($stall_name);
if (!$stmt->fetch()) {
    die("Stall not found.");
}
$stmt->close();

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_item_id = isset($_POST['menu_item_id']) ? (int)$_POST['menu_item_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review = isset($_POST['review']) ? trim($_POST['review']) : "";

    $stmt = $conn->prepare("SELECT COUNT(*) FROM menu_items WHERE id = ? AND stall_id = ?");
    $stmt->bind_param("ii", $menu_item_id, $stall_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($menu_item_id <= 0 || $rating < 1 || $rating > 5 || $count == 0) {
        $message = "Please select a valid menu item from this stall and provide a rating 1-5.";
    } else {
        $stmt = $conn->prepare("INSERT INTO ratings (menu_item_id, rating, review) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $menu_item_id, $rating, $review);
        if ($stmt->execute()) {
            $message = "Thank you for your review!";
        } else {
            $message = "Error saving review: " . $conn->error;
        }
        $stmt->close();
    }
}

$menu_items = [];
$stmt = $conn->prepare("SELECT id, name FROM menu_items WHERE stall_id = ? ORDER BY name");
$stmt->bind_param("i", $stall_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $menu_items[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Review Menu Items for <?= htmlspecialchars($stall_name) ?></title>
    <link rel="stylesheet" href="css/view_cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .content {
            padding: 90px 30px 30px;
            margin-left: 280px;
            background: #f8fafc;
        }

        .review-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }

        .review-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #1e3c72, #2a5298);
        }

        h2 { 
            color: #1e3c72;
            font-size: 32px;
            margin-bottom: 40px;
            text-align: center;
            font-weight: 700;
        }

        form { 
            display: flex; 
            flex-direction: column; 
            gap: 25px; 
        }

        .form-group {
            position: relative;
        }

        label { 
            font-weight: 600;
            color: #333;
            font-size: 15px;
            margin-bottom: 8px;
            display: block;
        }

        select { 
            width: 100%;
            padding: 14px;
            border: 2px solid #e8f0fe;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            appearance: none;
            background: #fff url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%231e3c72' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>") no-repeat;
            background-position: right 15px center;
            cursor: pointer;
        }

        textarea { 
            width: 100%;
            padding: 14px;
            border: 2px solid #e8f0fe;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            resize: vertical; 
            min-height: 120px;
            line-height: 1.6;
        }

        select:focus, textarea:focus {
            border-color: #1e3c72;
            outline: none;
            box-shadow: 0 0 0 4px rgba(42, 82, 152, 0.1);
        }

        .rating-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .star-label {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .star-label:hover,
        .star-label:hover ~ .star-label {
            color: #ffd700;
            transform: scale(1.1);
        }

        input[type="radio"]:checked + .star-label {
            color: #ffd700;
        }

        input[type="radio"] {
            display: none;
        }

        .submit-btn { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 82, 152, 0.2);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .message { 
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 500;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .success { 
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .error { 
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .back-button {
            text-align: right;
            margin-bottom: 30px;
        }

        .back-button a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #1e3c72;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: rgba(30, 60, 114, 0.05);
        }

        .back-button a:hover {
            transform: translateX(-5px);
            background: rgba(30, 60, 114, 0.1);
        }

        .back-button i {
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 48px;
            color: #94a3b8;
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .review-container {
                padding: 30px 20px;
            }

            h2 {
                font-size: 24px;
            }
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
        <div class="review-container">
            <div class="back-button">
                <a href="stall_dashboard.php?stall_id=<?= $stall_id ?>">
                    <i class="fas fa-arrow-left"></i> Back to Stall
                </a>
            </div>

            <h2>Rate Your Experience at <?= htmlspecialchars($stall_name) ?></h2>

            <?php if ($message): ?>
                <div class="message <?= strpos($message, 'Thank you') === 0 ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (count($menu_items) > 0): ?>
                <form method="post" action="ratings.php?stall_id=<?= $stall_id ?>">
                    <div class="form-group">
                        <label for="menu_item_id">Select Menu Item</label>
                        <select id="menu_item_id" name="menu_item_id" required>
                            <option value="">Choose an item to review</option>
                            <?php foreach ($menu_items as $item): ?>
                                <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Your Rating</label>
                        <div class="rating-group">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" required>
                                <label for="star<?= $i ?>" class="star-label">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="review">Your Review</label>
                        <textarea id="review" name="review" placeholder="Share your experience with this item... (optional)"></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Submit Review</button>
                </form>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-utensils"></i>
                    <p>No menu items available for review at this time.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Interactive star rating
        document.querySelectorAll('.star-label').forEach(star => {
            star.addEventListener('mouseover', function() {
                const rating = this.previousElementSibling.value;
                document.querySelectorAll('.star-label').forEach(s => {
                    if (s.previousElementSibling.value <= rating) {
                        s.style.color = '#ffd700';
                    }
                });
            });

            star.addEventListener('mouseout', function() {
                document.querySelectorAll('.star-label').forEach(s => {
                    if (!s.previousElementSibling.checked) {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
    </script>
</body>
</html>

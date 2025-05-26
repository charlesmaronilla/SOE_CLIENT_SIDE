<?php
$conn = new mysqli("localhost", "root", "password", "soe_clientside");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; background: #f9f9f9; border-radius: 8px; box-shadow: 0 0 12px rgba(0,0,0,0.1);}
        h2 { text-align: center; margin-bottom: 20px; color: #e67e22;}
        form { display: flex; flex-direction: column; gap: 16px; }
        label { font-weight: bold; }
        select, textarea, input[type=submit] { padding: 10px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px; }
        textarea { resize: vertical; min-height: 100px; }
        input[type=submit] { background-color: #e67e22; color: white; border: none; cursor: pointer; font-weight: bold; transition: background-color 0.3s ease; }
        input[type=submit]:hover { background-color: #cf711d; }
        .message { padding: 12px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-button {
            text-align: center;
            margin-top: 20px;
        }
        .back-button a {
            text-decoration: none;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .back-button a:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<h2>Review Menu Items for "<?= htmlspecialchars($stall_name) ?>"</h2>

<?php if ($message): ?>
    <div class="message <?= strpos($message, 'Thank you') === 0 ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if (count($menu_items) > 0): ?>
    <form method="post" action="ratings.php?stall_id=<?= $stall_id ?>">
        <label for="menu_item_id">Choose Menu Item:</label>
        <select id="menu_item_id" name="menu_item_id" required>
            <option value="">-- Select an item --</option>
            <?php foreach ($menu_items as $item): ?>
                <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="rating">Rate (1 to 5):</label>
        <select id="rating" name="rating" required>
            <option value="">-- Select rating --</option>
            <?php for ($i=1; $i<=5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label for="review">Write your review:</label>
        <textarea id="review" name="review" placeholder="Write your thoughts here... (optional)"></textarea>

        <input type="submit" value="Submit Review" />
    </form>

    <div class="back-button">
        <a href="stall_dashboard.php?stall_id=<?= $stall_id ?>">← Back to Stall</a>
    </div>

<?php else: ?>
    <p>No menu items found for this stall.</p>
<?php endif; ?>

</body>
</html>

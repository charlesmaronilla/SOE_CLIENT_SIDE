<?php
session_start();
require_once 'db_connection.php';

$menu_item_id = isset($_POST['menu_item_id']) ? intval($_POST['menu_item_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

if ($menu_item_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    exit;
}

$session_id = session_id();


$stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE session_id = ? AND menu_item_id = ?");
$stmt->bind_param("si", $session_id, $menu_item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    
    $row = $result->fetch_assoc();
    
    if ($quantity <= 0) {
       
        $delete_stmt = $conn->prepare("DELETE FROM cart_items WHERE session_id = ? AND menu_item_id = ?");
        $delete_stmt->bind_param("si", $session_id, $menu_item_id);
        $success = $delete_stmt->execute();
        echo json_encode(['success' => $success, 'action' => 'removed']);
    } else {
       
        $update_stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE session_id = ? AND menu_item_id = ?");
        $update_stmt->bind_param("isi", $quantity, $session_id, $menu_item_id);
        $success = $update_stmt->execute();
        echo json_encode(['success' => $success, 'action' => 'updated']);
    }
} else {
    if ($quantity > 0) {
        
        $insert_stmt = $conn->prepare("INSERT INTO cart_items (session_id, menu_item_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("sii", $session_id, $menu_item_id, $quantity);
        $success = $insert_stmt->execute();
        echo json_encode(['success' => $success, 'action' => 'added']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Quantity must be greater than 0 to add new item']);
    }
}
?>

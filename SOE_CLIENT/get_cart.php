<?php
session_start();
require_once 'db_connection.php';

$session_id = session_id();

$query = "SELECT ci.id, ci.menu_item_id, ci.quantity as qty, mi.name, mi.price 
          FROM cart_items ci 
          JOIN menu_items mi ON ci.menu_item_id = mi.id 
          WHERE ci.session_id = ?";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $session_id);
                $stmt->execute();
                $result = $stmt->get_result();

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

                echo json_encode([
                    'success' => true,
                    'items' => $items
                    ]);
                    ?> 
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ingredient_id = $_POST['ingredient_id'];
    $due_date = $_POST['due_date'];
    $supplier_id = $_POST['supplier_id'];
    $quantity = $_POST['quantity'];
    $cost = $_POST['cost'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO purchase_orders 
            (ingredient_id, due_date, supplier_id, quantity, cost) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$ingredient_id, $due_date, $supplier_id, $quantity, $cost]);
        
        // Update supplier purchase count
        $stmt = $pdo->prepare("UPDATE suppliers SET purchase_count = purchase_count + 1 WHERE id = ?");
        $stmt->execute([$supplier_id]);
        
        $_SESSION['success'] = "Purchase order created successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error creating order: " . $e->getMessage();
    }
    
    header('Location: orders.php');
    exit();
}
?>
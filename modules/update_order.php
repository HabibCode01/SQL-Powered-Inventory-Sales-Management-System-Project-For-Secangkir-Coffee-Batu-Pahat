<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $ingredient_id = $_POST['ingredient_id'];
    $due_date = $_POST['due_date'];
    $supplier_id = $_POST['supplier_id'];
    $quantity = $_POST['quantity'];
    $cost = $_POST['cost'];
    $status = $_POST['status'];
    
    try {
        // Get current supplier to update counts if changed
        $stmt = $pdo->prepare("SELECT supplier_id FROM purchase_orders WHERE id = ?");
        $stmt->execute([$id]);
        $currentSupplier = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("
            UPDATE purchase_orders 
            SET ingredient_id = ?, due_date = ?, supplier_id = ?, 
                quantity = ?, cost = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([$ingredient_id, $due_date, $supplier_id, $quantity, $cost, $status, $id]);
        
        // Update supplier purchase counts if supplier changed
        if ($currentSupplier != $supplier_id) {
            // Decrement old supplier
            $stmt = $pdo->prepare("UPDATE suppliers SET purchase_count = purchase_count - 1 WHERE id = ?");
            $stmt->execute([$currentSupplier]);
            
            // Increment new supplier
            $stmt = $pdo->prepare("UPDATE suppliers SET purchase_count = purchase_count + 1 WHERE id = ?");
            $stmt->execute([$supplier_id]);
        }
        
        $_SESSION['success'] = "Purchase order updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating order: " . $e->getMessage();
    }
    
    header('Location: orders.php');
    exit();
}
?>
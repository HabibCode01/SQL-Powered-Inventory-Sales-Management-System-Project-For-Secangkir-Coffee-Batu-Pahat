<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Get supplier to update count
        $stmt = $pdo->prepare("SELECT supplier_id FROM purchase_orders WHERE id = ?");
        $stmt->execute([$id]);
        $supplier_id = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("DELETE FROM purchase_orders WHERE id = ?");
        $stmt->execute([$id]);
        
        // Update supplier purchase count
        $stmt = $pdo->prepare("UPDATE suppliers SET purchase_count = purchase_count - 1 WHERE id = ?");
        $stmt->execute([$supplier_id]);
        
        $_SESSION['success'] = "Purchase order deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting order: " . $e->getMessage();
    }
    
    header('Location: orders.php');
    exit();
}
?>
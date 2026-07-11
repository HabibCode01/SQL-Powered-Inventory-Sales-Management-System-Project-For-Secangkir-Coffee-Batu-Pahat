<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $current_stock = $_POST['current_stock'];
    $purchased = $_POST['purchased'];
    $price = $_POST['price'];
    
    try {
        $stmt = $pdo->prepare("
            UPDATE ingredients 
            SET name = ?, current_stock = ?, purchased = ?, price = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $current_stock, $purchased, $price, $id]);
        
        $_SESSION['success'] = "Ingredient updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating ingredient: " . $e->getMessage();
    }
    
    header('Location: stock.php');
    exit();
}
?>
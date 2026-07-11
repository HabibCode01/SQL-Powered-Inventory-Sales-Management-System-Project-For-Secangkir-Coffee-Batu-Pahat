<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $current_stock = $_POST['current_stock'];
    $purchased = $_POST['purchased'];
    $price = $_POST['price'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO ingredients 
            (name, current_stock, purchased, price) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$name, $current_stock, $purchased, $price]);
        
        $_SESSION['success'] = "Ingredient added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding ingredient: " . $e->getMessage();
    }
    
    header('Location: stock.php');
    exit();
}
?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO products 
            (name, category, price) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$name, $category, $price]);
        
        $_SESSION['success'] = "Product added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding product: " . $e->getMessage();
    }
    
    header('Location: products.php');
    exit();
}
?>
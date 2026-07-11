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
    $category = $_POST['category'];
    $price = $_POST['price'];
    $sold_count = $_POST['sold_count'];
    
    try {
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = ?, category = ?, price = ?, sold_count = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $category, $price, $sold_count, $id]);
        
        $_SESSION['success'] = "Product updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating product: " . $e->getMessage();
    }
    
    header('Location: products.php');
    exit();
}
?>
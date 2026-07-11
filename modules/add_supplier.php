<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact_name = $_POST['contact_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO suppliers 
            (name, contact_name, email, phone) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$name, $contact_name, $email, $phone]);
        
        $_SESSION['success'] = "Supplier added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding supplier: " . $e->getMessage();
    }
    
    header('Location: suppliers.php');
    exit();
}
?>
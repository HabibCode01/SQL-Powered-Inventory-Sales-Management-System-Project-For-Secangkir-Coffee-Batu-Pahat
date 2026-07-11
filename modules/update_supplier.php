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
    $contact_name = $_POST['contact_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $purchase_count = $_POST['purchase_count'];
    
    try {
        $stmt = $pdo->prepare("
            UPDATE suppliers 
            SET name = ?, contact_name = ?, email = ?, phone = ?, purchase_count = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $contact_name, $email, $phone, $purchase_count, $id]);
        
        $_SESSION['success'] = "Supplier updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating supplier: " . $e->getMessage();
    }
    
    header('Location: suppliers.php');
    exit();
}
?>
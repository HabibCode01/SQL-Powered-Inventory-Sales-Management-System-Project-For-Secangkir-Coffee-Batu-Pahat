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
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    try {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = ?, email = ?, role = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $email, $role, $id]);
        
        $_SESSION['success'] = "Staff member updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating staff: " . $e->getMessage();
    }
    
    header('Location: staff.php');
    exit();
}
?>
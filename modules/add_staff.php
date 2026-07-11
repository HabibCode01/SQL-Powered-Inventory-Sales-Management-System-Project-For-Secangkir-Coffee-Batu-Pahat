<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO users 
            (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$name, $email, $password, $role]);
        
        $_SESSION['success'] = "Staff member added successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding staff: " . $e->getMessage();
    }
    
    header('Location: staff.php');
    exit();
}
?>
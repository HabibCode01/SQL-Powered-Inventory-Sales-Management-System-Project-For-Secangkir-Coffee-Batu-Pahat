<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with absolute URL
header("Location: /inventory_system/auth/login.php");
exit();
?>
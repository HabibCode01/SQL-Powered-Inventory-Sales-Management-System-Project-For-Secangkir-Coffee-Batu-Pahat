<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: modules/dashboard.php');
    exit();
} else {
    header('Location: auth/login.php');
    exit();
}
?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = strtolower(trim($_POST['email']));
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $storedPassword = $user['password'];
            $userId = $user['id'];

            $isBcrypt = password_verify($password, $storedPassword);
            $isSha256 = hash('sha256', $password) === $storedPassword;

            if ($isBcrypt || $isSha256) {
                // ✅ If SHA256, rehash and update with bcrypt
                if ($isSha256) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $updateStmt->execute([$newHash, $userId]);
                }

                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: ../modules/dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "Invalid email or password!";
        }

    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Coffee Secangkir Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-bg">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg login-card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <img src="https://secangkir.my/wp-content/uploads/2022/02/Secangkir_Logo-e1644550792498.png" alt="Logo" class="mb-3">
                    <h2>Welcome back!</h2>
                    <p class="text-muted">Log in to continue</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

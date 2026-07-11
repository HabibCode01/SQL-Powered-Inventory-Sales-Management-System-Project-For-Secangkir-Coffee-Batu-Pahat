<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Handle staff deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: staff.php');
    exit();
}

// Get all staff members
$stmt = $pdo->query("SELECT * FROM users");
$staff = $stmt->fetchAll();
?>

<h1 class="mb-4">Manage Staff</h1>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <div class="w-50">
                <input type="text" class="form-control" placeholder="Search staff...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="fas fa-plus"></i> Add Staff
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $member): ?>
                        <tr>
                            <td><?= $member['name'] ?></td>
                            <td><?= $member['email'] ?></td>
                            <td>
                                <span class="badge bg-<?= $member['role'] === 'admin' ? 'danger' : ($member['role'] === 'manager' ? 'warning' : 'primary') ?>">
                                    <?= ucfirst($member['role']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editStaffModal<?= $member['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="staff.php?delete=<?= $member['id'] ?>" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Edit Staff Modal -->
                        <div class="modal fade" id="editStaffModal<?= $member['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Staff Member</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="update_staff.php">
                                        <input type="hidden" name="id" value="<?= $member['id'] ?>">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" class="form-control" name="name" value="<?= $member['name'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" class="form-control" name="email" value="<?= $member['email'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Role</label>
                                                <select class="form-select" name="role" required>
                                                    <option value="admin" <?= $member['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                    <option value="manager" <?= $member['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                                                    <option value="staff" <?= $member['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update Staff</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="add_staff.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="staff" selected>Staff</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
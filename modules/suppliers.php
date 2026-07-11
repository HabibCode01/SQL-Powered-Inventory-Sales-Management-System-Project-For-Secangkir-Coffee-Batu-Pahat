<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Handle supplier deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: suppliers.php');
    exit();
}

// Get all suppliers
$stmt = $pdo->query("SELECT * FROM suppliers");
$suppliers = $stmt->fetchAll();
?>

<h1 class="mb-4">Suppliers</h1>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <div class="w-50">
                <input type="text" class="form-control" placeholder="Search suppliers...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="fas fa-plus"></i> Add Supplier
            </button>
        </div>
        
        <div class="row">
            <?php foreach ($suppliers as $supplier): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title"><?= $supplier['name'] ?></h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editSupplierModal<?= $supplier['id'] ?>"><i class="fas fa-edit"></i> Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="suppliers.php?delete=<?= $supplier['id'] ?>" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user me-2"></i>
                                    <span><?= $supplier['contact_name'] ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-envelope me-2"></i>
                                    <span><?= $supplier['email'] ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-phone me-2"></i>
                                    <span><?= $supplier['phone'] ?></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    <span>Purchase Orders: <?= $supplier['purchase_count'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Supplier Modal -->
                <div class="modal fade" id="editSupplierModal<?= $supplier['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Supplier</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="update_supplier.php">
                                <input type="hidden" name="id" value="<?= $supplier['id'] ?>">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Supplier Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= $supplier['name'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" name="contact_name" value="<?= $supplier['contact_name'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?= $supplier['email'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control" name="phone" value="<?= $supplier['phone'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Purchase Orders</label>
                                        <input type="number" class="form-control" name="purchase_count" value="<?= $supplier['purchase_count'] ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Supplier</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="add_supplier.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Name</label>
                        <input type="text" class="form-control" name="contact_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
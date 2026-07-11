<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // Get supplier to update count
        $stmt = $pdo->prepare("SELECT supplier_id FROM purchase_orders WHERE id = ?");
        $stmt->execute([$id]);
        $supplier_id = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("DELETE FROM purchase_orders WHERE id = ?");
        $stmt->execute([$id]);
        
        // Update supplier purchase count
        $stmt = $pdo->prepare("UPDATE suppliers SET purchase_count = purchase_count - 1 WHERE id = ?");
        $stmt->execute([$supplier_id]);
        
        $_SESSION['success'] = "Purchase order deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting order: " . $e->getMessage();
    }
    header('Location: orders.php');
    exit();
}

// Get all purchase orders
$stmt = $pdo->prepare("
    SELECT po.*, i.name AS ingredient_name, s.name AS supplier_name 
    FROM purchase_orders po
    JOIN ingredients i ON po.ingredient_id = i.id
    JOIN suppliers s ON po.supplier_id = s.id
    ORDER BY po.due_date ASC
");
$stmt->execute();
$orders = $stmt->fetchAll();

// Get all ingredients for dropdown
$stmt = $pdo->query("SELECT * FROM ingredients");
$ingredients = $stmt->fetchAll();

// Get all suppliers for dropdown
$stmt = $pdo->query("SELECT * FROM suppliers");
$suppliers = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Purchase Orders</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
        <i class="fas fa-plus"></i> Create Order
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <div class="w-50">
                <input type="text" class="form-control" placeholder="Search orders..." id="searchOrders">
            </div>
            <div>
                <button class="btn btn-outline-secondary"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Menu Ingredient</th>
                        <th>Due Date</th>
                        <th>Supplier</th>
                        <th>Items Ordered</th>
                        <th>Order Cost</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['ingredient_name']) ?></td>
                            <td><?= date('m/d/Y', strtotime($order['due_date'])) ?></td>
                            <td><?= htmlspecialchars($order['supplier_name']) ?></td>
                            <td><?= $order['quantity'] ?></td>
                            <td>RM <?= number_format($order['cost'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $order['status'] === 'completed' ? 'success' : 
                                    ($order['status'] === 'pending' ? 'warning' : 'danger') 
                                ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editOrderModal<?= $order['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="orders.php?delete=<?= $order['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this order?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Edit Order Modal -->
                        <div class="modal fade" id="editOrderModal<?= $order['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Purchase Order</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="update_order.php">
                                        <input type="hidden" name="id" value="<?= $order['id'] ?>">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Menu Ingredient</label>
                                                <select class="form-select" name="ingredient_id" required>
                                                    <?php foreach ($ingredients as $ingredient): ?>
                                                        <option value="<?= $ingredient['id'] ?>" <?= $ingredient['id'] == $order['ingredient_id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($ingredient['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Due Date</label>
                                                <input type="date" class="form-control" name="due_date" value="<?= $order['due_date'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Supplier</label>
                                                <select class="form-select" name="supplier_id" required>
                                                    <?php foreach ($suppliers as $supplier): ?>
                                                        <option value="<?= $supplier['id'] ?>" <?= $supplier['id'] == $order['supplier_id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($supplier['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Quantity</label>
                                                    <input type="number" class="form-control" name="quantity" value="<?= $order['quantity'] ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Cost (RM)</label>
                                                    <input type="number" step="0.01" class="form-control" name="cost" value="<?= $order['cost'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status" required>
                                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update Order</button>
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

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="add_order.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Menu Ingredient</label>
                        <select class="form-select" name="ingredient_id" required>
                            <option value="">Select Ingredient</option>
                            <?php foreach ($ingredients as $ingredient): ?>
                                <option value="<?= $ingredient['id'] ?>"><?= htmlspecialchars($ingredient['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" class="form-control" name="due_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select class="form-select" name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cost (RM)</label>
                            <input type="number" step="0.01" class="form-control" name="cost" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchOrders').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: products.php');
    exit();
}

// Get all products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>

<h1 class="mb-4">Products</h1>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <div class="w-50">
                <input type="text" class="form-control" placeholder="Search products...">
            </div>
            <div class="d-flex">
                <select class="form-select me-2">
                    <option>All Categories</option>
                    <option>Food</option>
                    <option>Beverages</option>
                </select>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
        </div>
        
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title"><?= $product['name'] ?></h5>
                                <span class="badge bg-info"><?= $product['category'] ?></span>
                            </div>
                            
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Price:</span>
                                    <span class="fw-bold">RM <?= number_format($product['price'], 2) ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Sold:</span>
                                    <span class="fw-bold"><?= $product['sold_count'] ?></span>
                                </div>
                            </div>
                            
                            <div class="mt-3 d-flex justify-content-between">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $product['id'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="products.php?delete=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Product Modal -->
                <div class="modal fade" id="editProductModal<?= $product['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="update_product.php">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= $product['name'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category" required>
                                            <option value="Food" <?= $product['category'] === 'Food' ? 'selected' : '' ?>>Food</option>
                                            <option value="Beverages" <?= $product['category'] === 'Beverages' ? 'selected' : '' ?>>Beverages</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price (RM)</label>
                                        <input type="number" step="0.01" class="form-control" name="price" value="<?= $product['price'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Sold Count</label>
                                        <input type="number" class="form-control" name="sold_count" value="<?= $product['sold_count'] ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Product</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="add_product.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <option value="Food">Food</option>
                            <option value="Beverages">Beverages</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (RM)</label>
                        <input type="number" step="0.01" class="form-control" name="price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
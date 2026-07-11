<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Handle ingredient deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM ingredients WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: stock.php');
    exit();
}

// Get all ingredients
$stmt = $pdo->query("SELECT * FROM ingredients");
$ingredients = $stmt->fetchAll();
?>

<h1 class="mb-4">Stock Count</h1>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <div class="w-50">
                <input type="text" class="form-control" placeholder="Search ingredients...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIngredientModal">
                <i class="fas fa-plus"></i> Add Ingredient
            </button>
        </div>
        
        <div class="row">
            <?php foreach ($ingredients as $ingredient): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title"><?= $ingredient['name'] ?></h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editIngredientModal<?= $ingredient['id'] ?>"><i class="fas fa-edit"></i> Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="stock.php?delete=<?= $ingredient['id'] ?>" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Available:</span>
                                    <span class="fw-bold"><?= $ingredient['current_stock'] ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Purchased:</span>
                                    <span class="fw-bold"><?= $ingredient['purchased'] ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Price:</span>
                                    <span class="fw-bold">RM <?= number_format($ingredient['price'], 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Ingredient Modal -->
                <div class="modal fade" id="editIngredientModal<?= $ingredient['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Ingredient</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="update_ingredient.php">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $ingredient['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Ingredient Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= $ingredient['name'] ?>" required>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Available</label>
                                            <input type="number" class="form-control" name="current_stock" value="<?= $ingredient['current_stock'] ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Purchased</label>
                                            <input type="number" class="form-control" name="purchased" value="<?= $ingredient['purchased'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price (RM)</label>
                                        <input type="number" step="0.01" class="form-control" name="price" value="<?= $ingredient['price'] ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add Ingredient Modal -->
<div class="modal fade" id="addIngredientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Ingredient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="add_ingredient.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ingredient Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Available</label>
                            <input type="number" class="form-control" name="current_stock" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Purchased</label>
                            <input type="number" class="form-control" name="purchased" value="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (RM)</label>
                        <input type="number" step="0.01" class="form-control" name="price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Ingredient</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
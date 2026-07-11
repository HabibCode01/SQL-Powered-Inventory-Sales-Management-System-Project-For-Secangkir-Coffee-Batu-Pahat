<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Get recent purchase orders
$stmt = $pdo->prepare("
    SELECT po.*, i.name AS ingredient_name, s.name AS supplier_name 
    FROM purchase_orders po
    JOIN ingredients i ON po.ingredient_id = i.id
    JOIN suppliers s ON po.supplier_id = s.id
    ORDER BY po.due_date ASC
    LIMIT 5
");
$stmt->execute();
$orders = $stmt->fetchAll();

// Get top selling products
$stmt = $pdo->query("SELECT * FROM products ORDER BY sold_count DESC LIMIT 5");
$products = $stmt->fetchAll();

// Get stats
$stmt = $pdo->query("SELECT COUNT(*) AS total_products FROM products");
$total_products = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM purchase_orders");
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT SUM(p.price * p.sold_count) AS total_revenue FROM products p");
$total_revenue = $stmt->fetchColumn();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Revenue</h5>
                <p class="card-text display-6">RM <?= number_format($total_revenue, 2) ?></p>
                <small>Last updated at <?= date('h:i a') ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Ingredients Purchased</h5>
                <p class="card-text display-6"><?= $total_orders ?></p>
                <small>Last updated at <?= date('h:i a') ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Products Sold</h5>
                <p class="card-text display-6"><?= $total_products ?></p>
                <small>Last updated at <?= date('h:i a') ?></small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Purchase Orders Due</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div class="w-75">
                        <input type="text" class="form-control" placeholder="Search..." id="orderSearch">
                    </div>
                    <a href="orders.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Order
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Menu Ingredient</th>
                                <th>Due Date</th>
                                <th>Supplier</th>
                                <th>Items</th>
                                <th>Cost</th>
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
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Top Selling Products</h5>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <input type="text" class="form-control me-2" placeholder="Search..." id="productSearch">
                    <select class="form-select" id="productCategory">
                        <option value="">All Categories</option>
                        <option value="Food">Food</option>
                        <option value="Beverages">Beverages</option>
                    </select>
                </div>
                
                <div class="list-group" id="productList">
                    <?php foreach ($products as $product): ?>
                        <div class="list-group-item product-item" data-category="<?= htmlspecialchars($product['category']) ?>">
                            <div class="d-flex justify-content-between">
                                <h6><?= htmlspecialchars($product['name']) ?></h6>
                                <span class="badge bg-primary"><?= htmlspecialchars($product['category']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span>Price: RM <?= number_format($product['price'], 2) ?></span>
                                <span>Sold: <?= $product['sold_count'] ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality for orders
document.getElementById('orderSearch').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Search and filter functionality for products
document.getElementById('productSearch').addEventListener('input', filterProducts);
document.getElementById('productCategory').addEventListener('change', filterProducts);

function filterProducts() {
    const searchValue = document.getElementById('productSearch').value.toLowerCase();
    const categoryValue = document.getElementById('productCategory').value;
    const items = document.querySelectorAll('.product-item');
    
    items.forEach(item => {
        const matchesSearch = item.textContent.toLowerCase().includes(searchValue);
        const matchesCategory = categoryValue === '' || item.dataset.category === categoryValue;
        
        item.style.display = matchesSearch && matchesCategory ? '' : 'none';
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
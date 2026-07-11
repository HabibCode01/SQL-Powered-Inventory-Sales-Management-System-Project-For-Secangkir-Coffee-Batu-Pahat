<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Get revenue data for chart
$revenueData = [];
$stmt = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, 
           SUM(price * sold_count) AS revenue 
    FROM products 
    GROUP BY month 
    ORDER BY month DESC 
    LIMIT 6
");
while ($row = $stmt->fetch()) {
    $revenueData[] = $row;
}

// Get top products
$topProducts = [];
$stmt = $pdo->query("SELECT name, sold_count FROM products ORDER BY sold_count DESC LIMIT 5");
while ($row = $stmt->fetch()) {
    $topProducts[] = $row;
}

// Get low stock ingredients
$lowStock = [];
$stmt = $pdo->query("SELECT name, current_stock FROM ingredients WHERE current_stock < 10 ORDER BY current_stock ASC");
while ($row = $stmt->fetch()) {
    $lowStock[] = $row;
}
?>

<h1 class="mb-4">Reporting Dashboard</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Revenue Trends (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Top Selling Products</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($topProducts as $product): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= $product['name'] ?>
                            <span class="badge bg-primary rounded-pill"><?= $product['sold_count'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Low Stock Ingredients</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($lowStock as $ingredient): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= $ingredient['name'] ?>
                            <span class="badge bg-danger rounded-pill"><?= $ingredient['current_stock'] ?></span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($lowStock)): ?>
                        <li class="list-group-item text-center text-muted">All ingredients are well stocked</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?php foreach ($revenueData as $data): ?>'<?= date('M Y', strtotime($data['month'])) ?>',<?php endforeach; ?>],
            datasets: [{
                label: 'Monthly Revenue (RM)',
                data: [<?php foreach ($revenueData as $data): ?><?= $data['revenue'] ?>,<?php endforeach; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'RM ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
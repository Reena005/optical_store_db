<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$totalCustomers = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM customers"), 0, 0);
$totalProducts = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM products"), 0, 0);
$totalSuppliers = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM suppliers"), 0, 0);
$totalOrders = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM orders"), 0, 0);

$totalRevenue = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COALESCE(SUM(total_amount),0)
         FROM orders
         WHERE payment_status='Paid'"
    ),
    0,
    0
);

$lowStock = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COUNT(*)
         FROM products
         WHERE stock < 5"
    ),
    0,
    0
);

$pendingOrders = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COUNT(*)
         FROM orders
         WHERE payment_status='Pending'"
    ),
    0,
    0
);

$deliveredOrders = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COUNT(*)
         FROM deliveries
         WHERE delivery_status='Delivered'"
    ),
    0,
    0
);

$totalAppointments = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COUNT(*)
         FROM appointments"
    ),
    0,
    0
);

$monthlyLabels = [];
$monthlyRevenue = [];

$monthlyQuery = pg_query(
    $conn,
    "SELECT 
        TO_CHAR(DATE_TRUNC('month', order_date), 'Mon YYYY') AS month_name,
        COALESCE(SUM(total_amount),0) AS revenue
     FROM orders
     WHERE payment_status='Paid'
     GROUP BY DATE_TRUNC('month', order_date)
     ORDER BY DATE_TRUNC('month', order_date)"
);

while ($m = pg_fetch_assoc($monthlyQuery)) {
    $monthlyLabels[] = $m['month_name'];
    $monthlyRevenue[] = (float)$m['revenue'];
}

$topProductLabels = [];
$topProductQty = [];

$topProductQuery = pg_query(
    $conn,
    "SELECT 
        p.product_name,
        COALESCE(SUM(oi.quantity),0) AS total_sold
     FROM order_items oi
     LEFT JOIN products p ON oi.product_id = p.product_id
     GROUP BY p.product_name
     ORDER BY total_sold DESC
     LIMIT 5"
);

while ($p = pg_fetch_assoc($topProductQuery)) {
    $topProductLabels[] = $p['product_name'] ?? 'Deleted Product';
    $topProductQty[] = (int)$p['total_sold'];
}

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="bi bi-bar-chart-fill"></i>
            Analytics & Reports Dashboard
        </h2>
        <p class="text-muted mb-0">
            Visual summary of store performance, sales, stock and operations.
        </p>
    </div>
</div>

    

<div class="row mb-4">

    <div class="col-md-8 mb-4">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                Monthly Revenue Analytics
            </div>
            <div class="card-body">
                <canvas id="monthlyRevenueChart" height="110"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card shadow border-0">
            <div class="card-header bg-success text-white">
                Operational Summary
            </div>
            <div class="card-body">
                <canvas id="summaryChart" height="240"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="row mb-4">

    <div class="col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white">
                Top Selling Products
            </div>
            <div class="card-body">
                <canvas id="topProductsChart" height="160"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-header bg-warning text-dark">
                Report Modules
            </div>

            <div class="list-group list-group-flush">
                <a href="sales_report.php" class="list-group-item list-group-item-action">
                    📈 Sales Report
                </a>

                <a href="customer_report.php" class="list-group-item list-group-item-action">
                    👥 Customer Report
                </a>

                <a href="stock_report.php" class="list-group-item list-group-item-action">
                    📦 Stock Report
                </a>

                <a href="top_products.php" class="list-group-item list-group-item-action">
                    🏆 Top Selling Products
                </a>
            </div>
        </div>
    </div>

</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const monthlyLabels = <?= json_encode($monthlyLabels); ?>;
const monthlyRevenue = <?= json_encode($monthlyRevenue); ?>;

new Chart(document.getElementById('monthlyRevenueChart'), {
    type: 'bar',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Monthly Revenue ₹',
            data: monthlyRevenue,
            backgroundColor: '#2563eb',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

new Chart(document.getElementById('summaryChart'), {
    type: 'bar',
    data: {
        labels: ['Customers', 'Products', 'Orders', 'Suppliers', 'Low Stock', 'Appointments'],
        datasets: [{
            label: 'Count',
            data: [
                <?= $totalCustomers; ?>,
                <?= $totalProducts; ?>,
                <?= $totalOrders; ?>,
                <?= $totalSuppliers; ?>,
                <?= $lowStock; ?>,
                <?= $totalAppointments; ?>
            ],
            backgroundColor: [
                '#2563eb',
                '#16a34a',
                '#111827',
                '#9333ea',
                '#dc2626',
                '#f59e0b'
            ],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

const topProductLabels = <?= json_encode($topProductLabels); ?>;
const topProductQty = <?= json_encode($topProductQty); ?>;

new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: topProductLabels,
        datasets: [{
            label: 'Quantity Sold',
            data: topProductQty,
            backgroundColor: '#0f172a',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include("../includes/footer.php"); ?>
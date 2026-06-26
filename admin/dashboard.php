<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$today = date("Y-m-d");

$pendingBills = pg_query($conn,"
SELECT o.order_id, o.total_amount, c.customer_name
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.customer_id
WHERE o.payment_status='Pending'
ORDER BY o.order_id DESC
LIMIT 5
");

$outOfStock = pg_query($conn,"
SELECT product_name, brand, stock
FROM products
WHERE stock = 0
ORDER BY product_name
LIMIT 5
");

$lowStock = pg_query($conn,"
SELECT product_name, brand, stock
FROM products
WHERE stock > 0 AND stock < 5
ORDER BY stock ASC
LIMIT 5
");

$newCustomers = pg_query_params($conn,"
SELECT customer_name, phone, created_at
FROM customers
WHERE DATE(created_at)=$1
ORDER BY customer_id DESC
LIMIT 5
", [$today]);

$recentOrders = pg_query($conn,"
SELECT o.order_id, o.order_date, o.payment_status, c.customer_name
FROM orders o
LEFT JOIN customers c ON o.customer_id=c.customer_id
ORDER BY o.order_id DESC
LIMIT 5
");
?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<div class="dashboard-hero mb-4">
    <div>
        <h2>Welcome back, <?= htmlspecialchars($_SESSION['name']); ?> 👋</h2>
        <p>Here is what needs your attention in the optical store today.</p>
    </div>

    
</div>

<div class="row mb-4">

<div class="col-md-3">
    <div class="action-card border-start border-warning border-5">
        <h6>Pending Bills</h6>
        <p>Follow up unpaid orders</p>
        <a href="../orders/orders.php?search=Pending">View Pending</a>
    </div>
</div>

<div class="col-md-3">
    <div class="action-card border-start border-danger border-5">
        <h6>Stock Attention</h6>
        <p>Products need restocking</p>
        <a href="../reports/stock_report.php?filter=low">Check Stock</a>
    </div>
</div>

<div class="col-md-3">
    <div class="action-card border-start border-success border-5">
        <h6>New Walk-in</h6>
        <p>Register new customer</p>
        <a href="../customers/add_customer.php">Add Now</a>
    </div>
</div>

<div class="col-md-3">
    <div class="action-card border-start border-primary border-5">
        <h6>Billing Desk</h6>
        <p>Create invoice quickly</p>
        <a href="../orders/create_order.php">Start Billing</a>
    </div>
</div>

</div>

<div class="row">

<div class="col-md-6">

<div class="card shadow mb-4">
<div class="card-header bg-warning text-dark">
    <i class="bi bi-cash-coin"></i> Pending Bill Follow-ups
</div>
<div class="card-body">

<?php if($pendingBills && pg_num_rows($pendingBills)>0){ ?>
<?php while($b=pg_fetch_assoc($pendingBills)){ ?>
<div class="work-item">
    <div>
        <strong>#<?= htmlspecialchars($b['order_id']); ?> -
        <?= htmlspecialchars($b['customer_name'] ?? 'Deleted Customer'); ?></strong>
        <br>
        <small>Amount: ₹<?= number_format($b['total_amount'],2); ?></small>
    </div>
    <a href="../orders/view_order.php?id=<?= $b['order_id']; ?>" class="btn btn-sm btn-outline-dark">
        Open
    </a>
</div>
<?php } ?>
<?php } else { ?>
<p class="text-muted mb-0">No pending bills today.</p>
<?php } ?>

</div>
</div>

<div class="card shadow">
<div class="card-header bg-primary text-white">
    <i class="bi bi-clock-history"></i> Recent Store Activity
</div>
<div class="card-body">

<?php if($recentOrders && pg_num_rows($recentOrders)>0){ ?>
<?php while($r=pg_fetch_assoc($recentOrders)){ ?>
<div class="timeline-item">
    <span></span>
    <div>
        Order #<?= htmlspecialchars($r['order_id']); ?>
        created for
        <strong><?= htmlspecialchars($r['customer_name'] ?? 'Deleted Customer'); ?></strong>
        <br>
        <small><?= date("d M Y, h:i A", strtotime($r['order_date'])); ?></small>
    </div>
</div>
<?php } ?>
<?php } else { ?>
<p class="text-muted mb-0">No recent activity.</p>
<?php } ?>

</div>
</div>

</div>

<div class="col-md-6">

<div class="card shadow mb-4">
<div class="card-header bg-danger text-white">
    <i class="bi bi-exclamation-triangle"></i> Inventory Attention
</div>
<div class="card-body">

<h6>Out of Stock</h6>
<?php if($outOfStock && pg_num_rows($outOfStock)>0){ ?>
<?php while($p=pg_fetch_assoc($outOfStock)){ ?>
<div class="work-item">
    <div>
        <strong><?= htmlspecialchars($p['product_name']); ?></strong><br>
        <small><?= htmlspecialchars($p['brand']); ?></small>
    </div>
    <span class="badge bg-danger">0 left</span>
</div>
<?php } ?>
<?php } else { ?>
<p class="text-muted">No out-of-stock products.</p>
<?php } ?>

<hr>

<h6>Very Low Stock</h6>
<?php if($lowStock && pg_num_rows($lowStock)>0){ ?>
<?php while($p=pg_fetch_assoc($lowStock)){ ?>
<div class="work-item">
    <div>
        <strong><?= htmlspecialchars($p['product_name']); ?></strong><br>
        <small><?= htmlspecialchars($p['brand']); ?></small>
    </div>
    <span class="badge bg-warning text-dark"><?= htmlspecialchars($p['stock']); ?> left</span>
</div>
<?php } ?>
<?php } else { ?>
<p class="text-muted">No critical low stock.</p>
<?php } ?>

</div>
</div>

<div class="card shadow">
<div class="card-header bg-success text-white">
    <i class="bi bi-person-check"></i> Today’s New Customers
</div>
<div class="card-body">

<?php if($newCustomers && pg_num_rows($newCustomers)>0){ ?>
<?php while($c=pg_fetch_assoc($newCustomers)){ ?>
<div class="work-item">
    <div>
        <strong><?= htmlspecialchars($c['customer_name']); ?></strong><br>
        <small><?= htmlspecialchars($c['phone']); ?></small>
    </div>
    <span class="badge bg-success">New</span>
</div>
<?php } ?>
<?php } else { ?>
<p class="text-muted mb-0">No new customers added today.</p>
<?php } ?>

</div>
</div>

</div>

</div>

</div>
</div>
</div>

<?php include("../includes/footer.php"); ?>
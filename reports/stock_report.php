<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$filter = $_GET['filter'] ?? "all";

if ($filter == "low") {
    $query = "
        SELECT p.product_id, p.product_name, p.brand, c.category_name, p.price, p.stock
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.stock < 5 AND p.stock > 0
        ORDER BY p.stock ASC
    ";
} elseif ($filter == "out") {
    $query = "
        SELECT p.product_id, p.product_name, p.brand, c.category_name, p.price, p.stock
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.stock = 0
        ORDER BY p.product_name ASC
    ";
} else {
    $query = "
        SELECT p.product_id, p.product_name, p.brand, c.category_name, p.price, p.stock
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.product_id DESC
    ";
}

$products = pg_query($conn, $query);

$totalProducts = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM products"), 0, 0);
$lowStock = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM products WHERE stock < 5 AND stock > 0"), 0, 0);
$outStock = pg_fetch_result(pg_query($conn, "SELECT COUNT(*) FROM products WHERE stock = 0"), 0, 0);

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>
        <i class="bi bi-box-seam-fill"></i>
        Stock Report
    </h2>

    <a href="reports.php" class="btn btn-secondary">
        Back
    </a>

</div>

<div class="row mb-4">

    <div class="col-md-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <h5>Total Products</h5>
                <h2><?= $totalProducts; ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-warning text-dark shadow">
            <div class="card-body">
                <h5>Low Stock</h5>
                <h2><?= $lowStock; ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-danger text-white shadow">
            <div class="card-body">
                <h5>Out of Stock</h5>
                <h2><?= $outStock; ?></h2>
            </div>
        </div>
    </div>

</div>

<div class="card shadow mb-4">

<div class="card-header bg-primary text-white">
    Filter Stock
</div>

<div class="card-body">

<a href="stock_report.php?filter=all" class="btn btn-outline-primary">
    All Products
</a>

<a href="stock_report.php?filter=low" class="btn btn-outline-warning">
    Low Stock
</a>

<a href="stock_report.php?filter=out" class="btn btn-outline-danger">
    Out of Stock
</a>

</div>

</div>

<div class="card shadow">

<div class="card-header bg-dark text-white">
    Stock Details
</div>

<div class="card-body">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Product</th>
    <th>Brand</th>
    <th>Category</th>
    <th>Price</th>
    <th>Stock</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

<?php if ($products && pg_num_rows($products) > 0) { ?>

<?php while ($row = pg_fetch_assoc($products)) { ?>

<tr>
    <td><?= htmlspecialchars($row['product_id']); ?></td>
    <td><?= htmlspecialchars($row['product_name']); ?></td>
    <td><?= htmlspecialchars($row['brand']); ?></td>
    <td><?= htmlspecialchars($row['category_name']); ?></td>
    <td>₹<?= htmlspecialchars($row['price']); ?></td>
    <td><?= htmlspecialchars($row['stock']); ?></td>

    <td>
        <?php if ($row['stock'] == 0) { ?>
            <span class="badge bg-danger">Out of Stock</span>
        <?php } elseif ($row['stock'] < 5) { ?>
            <span class="badge bg-warning text-dark">Low Stock</span>
        <?php } else { ?>
            <span class="badge bg-success">Available</span>
        <?php } ?>
    </td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="7" class="text-center text-muted">
        No stock data found
    </td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>
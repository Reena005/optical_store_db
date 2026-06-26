<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$totalCustomers=pg_fetch_result(
pg_query($conn,"SELECT COUNT(*) FROM customers"),
0,
0
);

$totalProducts=pg_fetch_result(
pg_query($conn,"SELECT COUNT(*) FROM products"),
0,
0
);

$totalSuppliers=pg_fetch_result(
pg_query($conn,"SELECT COUNT(*) FROM suppliers"),
0,
0
);

$totalOrders=pg_fetch_result(
pg_query($conn,"SELECT COUNT(*) FROM orders"),
0,
0
);

$totalRevenue=pg_fetch_result(
pg_query(
$conn,
"SELECT COALESCE(SUM(total_amount),0)
FROM orders
WHERE payment_status='Paid'"
),
0,
0
);

$lowStock=pg_fetch_result(
pg_query(
$conn,
"SELECT COUNT(*)
FROM products
WHERE stock<5"
),
0,
0
);

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<h2 class="fw-bold mb-4">

<i class="bi bi-bar-chart-fill"></i>

Reports Dashboard

</h2>

<div class="row">

<div class="col-md-4">

<div class="card bg-primary text-white shadow">

<div class="card-body">

<h5>Total Customers</h5>

<h2>

<?= $totalCustomers; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-success text-white shadow">

<div class="card-body">

<h5>Total Products</h5>

<h2>

<?= $totalProducts; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-dark text-white shadow">

<div class="card-body">

<h5>Total Suppliers</h5>

<h2>

<?= $totalSuppliers; ?>

</h2>

</div>

</div>

</div>

</div>

<br>

<div class="row">

<div class="col-md-4">

<div class="card bg-warning shadow">

<div class="card-body">

<h5>Total Orders</h5>

<h2>

<?= $totalOrders; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-danger text-white shadow">

<div class="card-body">

<h5>Total Revenue</h5>

<h2>

₹<?= number_format($totalRevenue,2); ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-danger text-white shadow border-0">

<div class="card-body">

<h5>
<i class="bi bi-exclamation-triangle-fill"></i>
Critical Low Stock
</h5>

<h2>
<?= $lowStock; ?>
</h2>

<p class="mb-0">
Products with stock less than
<strong>5 units</strong>
</p>

</div>

</div>

</div>

</div>

<br>

<div class="row">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

Reports

</div>

<div class="list-group list-group-flush">

<a
href="sales_report.php"
class="list-group-item list-group-item-action">

📈 Sales Report

</a>

<a
href="customer_report.php"
class="list-group-item list-group-item-action">

👥 Customer Report

</a>

<a
href="stock_report.php"
class="list-group-item list-group-item-action">

📦 Stock Report

</a>

<a
href="top_products.php"
class="list-group-item list-group-item-action">

🏆 Top Selling Products

</a>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-success text-white">

Quick Summary

</div>

<div class="card-body">

<table class="table">

<tr>

<td>

Today's Date

</td>

<td>

<?= date("d-m-Y"); ?>

</td>

</tr>

<tr>

<td>

Store

</td>

<td>

Clarity Optical Store

</td>

</tr>

<tr>

<td>

Status

</td>

<td>

<span class="badge bg-success">

System Online

</span>

</td>

</tr>

<tr>

<td>

Currency

</td>

<td>

Indian Rupee (₹)

</td>

</tr>

</table>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>
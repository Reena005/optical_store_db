<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

/* Today's Revenue */

$todayRevenue = pg_fetch_result(

pg_query(

$conn,

"

SELECT COALESCE(SUM(total_amount),0)

FROM orders

WHERE payment_status='Paid'

AND DATE(order_date)=CURRENT_DATE

"

),

0,

0

);

/* Today's Orders */

$todayOrders = pg_fetch_result(

pg_query(

$conn,

"

SELECT COUNT(*)

FROM orders

WHERE DATE(order_date)=CURRENT_DATE

"

),

0,

0

);

/* Average Order */

$avgOrder = pg_fetch_result(

pg_query(

$conn,

"

SELECT COALESCE(AVG(total_amount),0)

FROM orders

"

),

0,

0

);

/* Pending Payments */

$pendingPayments = pg_fetch_result(

pg_query(

$conn,

"

SELECT COALESCE(SUM(total_amount),0)

FROM orders

WHERE payment_status='Pending'

"

),

0,

0

);

/* Total Revenue */

$totalRevenue = pg_fetch_result(

pg_query(

$conn,

"

SELECT COALESCE(SUM(total_amount),0)

FROM orders

WHERE payment_status='Paid'

"

),

0,

0

);

/* Total Customers */

$totalCustomers = pg_fetch_result(

pg_query(

$conn,

"

SELECT COUNT(*)

FROM customers

"

),

0,

0

);

/* Total Appointments */

$totalAppointments = pg_fetch_result(

pg_query(

$conn,

"

SELECT COUNT(*)

FROM appointments

"

),

0,

0

);

/* Total Deliveries */

$totalDeliveries = pg_fetch_result(

pg_query(

$conn,

"

SELECT COUNT(*)

FROM deliveries

"

),

0,

0

);

?>

<div class="d-flex">

<?php include("../includes/sidebar.php");?>

<div class="content flex-grow-1">

<div class="container-fluid">

<h2 class="fw-bold mb-4">

<i class="bi bi-graph-up-arrow"></i>

Business Analytics Dashboard

</h2>

<div class="row">

<div class="col-md-3 mb-4">

<div class="card border-0 shadow-lg bg-primary text-white">

<div class="card-body">

<h6>Today's Revenue</h6>

<h2>

₹<?= number_format($todayRevenue,2);?>

</h2>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card border-0 shadow-lg bg-success text-white">

<div class="card-body">

<h6>Today's Orders</h6>

<h2>

<?= $todayOrders;?>

</h2>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card border-0 shadow-lg bg-warning">

<div class="card-body">

<h6>Average Order</h6>

<h2>

₹<?= number_format($avgOrder,2);?>

</h2>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card border-0 shadow-lg bg-danger text-white">

<div class="card-body">

<h6>Pending Payments</h6>

<h2>

₹<?= number_format($pendingPayments,2);?>

</h2>

</div>

</div>

</div>

</div>

<div class="row">

<div class="col-md-3 mb-4">

<div class="card shadow">

<div class="card-body text-center">

<i class="bi bi-currency-rupee fs-1 text-success"></i>

<h3>

₹<?= number_format($totalRevenue,2);?>

</h3>

<p>Total Revenue</p>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card shadow">

<div class="card-body text-center">

<i class="bi bi-people-fill fs-1 text-primary"></i>

<h3>

<?= $totalCustomers;?>

</h3>

<p>Total Customers</p>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card shadow">

<div class="card-body text-center">

<i class="bi bi-calendar-check-fill fs-1 text-warning"></i>

<h3>

<?= $totalAppointments;?>

</h3>

<p>Appointments</p>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card shadow">

<div class="card-body text-center">

<i class="bi bi-truck fs-1 text-info"></i>

<h3>

<?= $totalDeliveries;?>

</h3>

<p>Deliveries</p>

</div>

</div>

</div>

</div>

<div class="card shadow">

<div class="card-header bg-dark text-white">

Today's Business Summary

</div>

<div class="card-body">

<table class="table table-striped">

<tr>

<th>Today's Orders</th>

<td><?= $todayOrders;?></td>

</tr>

<tr>

<th>Today's Revenue</th>

<td>₹<?= number_format($todayRevenue,2);?></td>

</tr>

<tr>

<th>Average Order Value</th>

<td>₹<?= number_format($avgOrder,2);?></td>

</tr>

<tr>

<th>Pending Payments</th>

<td>₹<?= number_format($pendingPayments,2);?></td>

</tr>

<tr>

<th>Total Revenue</th>

<td>₹<?= number_format($totalRevenue,2);?></td>

</tr>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php");?>
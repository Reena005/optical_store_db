<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$query = "

SELECT

p.product_name,

p.brand,

SUM(oi.quantity) AS total_quantity,

SUM(oi.subtotal) AS total_sales

FROM order_items oi

JOIN products p

ON oi.product_id=p.product_id

GROUP BY

p.product_id,

p.product_name,

p.brand

ORDER BY total_quantity DESC

LIMIT 10

";

$result=pg_query($conn,$query);

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>

<i class="bi bi-trophy-fill"></i>

Top Selling Products

</h2>

<a
href="reports.php"
class="btn btn-secondary">

Back

</a>

</div>

<div class="row mb-4">

<div class="col-md-4">

<div class="card bg-success text-white shadow">

<div class="card-body">

<h5>

Best Selling Product

</h5>

<?php

pg_result_seek($result,0);

$top=pg_fetch_assoc($result);

?>

<h4>

<?= $top ? htmlspecialchars($top['product_name']) : "No Data"; ?>

</h4>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-primary text-white shadow">

<div class="card-body">

<h5>

Units Sold

</h5>

<h2>

<?= $top ? htmlspecialchars($top['total_quantity']) : 0; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-danger text-white shadow">

<div class="card-body">

<h5>

Revenue Generated

</h5>

<h2>

₹<?= $top ? number_format($top['total_sales'],2) : "0.00"; ?>

</h2>

</div>

</div>

</div>

</div>

<?php

pg_result_seek($result,0);

?>

<div class="card shadow">

<div class="card-header bg-dark text-white">

Top 10 Products

</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Rank</th>

<th>Product</th>

<th>Brand</th>

<th>Quantity Sold</th>

<th>Total Revenue</th>

</tr>

</thead>

<tbody>

<?php

$rank=1;

if($result && pg_num_rows($result)>0)
{

while($row=pg_fetch_assoc($result))
{

?>

<tr>

<td>

🏆 <?= $rank++; ?>

</td>

<td>

<?= htmlspecialchars($row['product_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['brand']); ?>

</td>

<td>

<?= htmlspecialchars($row['total_quantity']); ?>

</td>

<td>

₹<?= number_format($row['total_sales'],2); ?>

</td>

</tr>

<?php

}

}
else
{

?>

<tr>

<td colspan="5" class="text-center">

No Sales Available

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>
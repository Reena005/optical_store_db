<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<nav aria-label="breadcrumb">

<ol class="breadcrumb">

<li class="breadcrumb-item">

<a href="../admin/dashboard.php">

Dashboard

</a>

</li>

<li class="breadcrumb-item">

<a href="products.php">

Products

</a>

</li>

<li class="breadcrumb-item active">

Add Product

</li>

</ol>

</nav>

<h2>

<i class="bi bi-plus-circle-fill"></i>

Add New Product

</h2>

<br>

<div class="card shadow">

<div class="card-header bg-primary text-white">

Product Details

</div>

<div class="card-body">

<form
action="product_process.php"
method="POST"
enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">

<label>

Product Name

</label>

<input
type="text"
name="product_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>

Brand

</label>

<input
type="text"
name="brand"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>

Category

</label>

<select
name="category_id"
class="form-select">

<?php

$cat=pg_query(
$conn,
"SELECT * FROM categories"
);

while($row=pg_fetch_assoc($cat))
{

?>

<option
value="<?= $row['category_id']; ?>">

<?= $row['category_name']; ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-6 mb-3">

<label>

Price

</label>

<input
type="number"
step="0.01"
name="price"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>

Stock

</label>

<input
type="number"
name="stock"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>

Product Image

</label>

<input
type="file"
name="image"
class="form-control">

</div>

<div class="col-md-12 mb-3">

<label>

Description

</label>

<textarea
name="description"
rows="4"
class="form-control">

</textarea>

</div>

<div class="col-md-12">

<button
class="btn btn-success">

<i class="bi bi-check-circle-fill"></i>

Save Product

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>
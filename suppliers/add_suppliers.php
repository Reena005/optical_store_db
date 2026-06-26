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
<a href="../admin/dashboard.php">Dashboard</a>
</li>

<li class="breadcrumb-item">
<a href="suppliers.php">Suppliers</a>
</li>

<li class="breadcrumb-item active">
Add Supplier
</li>

</ol>

</nav>

<h2 class="mb-4">

<i class="bi bi-truck"></i>

Add New Supplier

</h2>

<?php

if(isset($_SESSION['success']))
{

?>

<div class="alert alert-success alert-dismissible fade show">

<?= $_SESSION['success']; ?>

<button
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php

unset($_SESSION['success']);

}

?>

<?php

if(isset($_SESSION['error']))
{

?>

<div class="alert alert-danger alert-dismissible fade show">

<?= $_SESSION['error']; ?>

<button
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php

unset($_SESSION['error']);

}

?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

Supplier Information

</div>

<div class="card-body">

<form
action="supplier_process.php"
method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Supplier Name

</label>

<input
type="text"
name="supplier_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Contact Person

</label>

<input
type="text"
name="contact_person"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Phone

</label>

<input
type="text"
name="phone"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Email

</label>

<input
type="email"
name="email"
class="form-control">

</div>

<div class="col-md-12 mb-3">

<label class="form-label">

Address

</label>

<textarea
name="address"
rows="4"
class="form-control">

</textarea>

</div>

<div class="col-md-12">

<button
class="btn btn-success">

<i class="bi bi-check-circle-fill"></i>

Save Supplier

</button>

<button
type="reset"
class="btn btn-secondary">

Reset

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
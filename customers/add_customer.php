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
            <a href="customers.php">Customers</a>
        </li>
        <li class="breadcrumb-item active">
            Add Customer
        </li>
    </ol>
</nav>

<h2 class="mb-4">
    <i class="bi bi-person-plus-fill"></i>
    Add New Customer
</h2>

<?php
if(isset($_SESSION['success']))
{
?>
<div class="alert alert-success alert-dismissible fade show">
    <?php
    echo $_SESSION['success'];
    unset($_SESSION['success']);
    ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php
}

if(isset($_SESSION['error']))
{
?>
<div class="alert alert-danger alert-dismissible fade show">
    <?php
    echo $_SESSION['error'];
    unset($_SESSION['error']);
    ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php
}
?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>Customer Information</h4>

</div>

<div class="card-body">

<form action="customer_process.php" method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Customer Name</label>

<input
type="text"
name="customer_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Phone</label>

<input
type="text"
name="phone"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Email</label>

<input
type="email"
name="email"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Age</label>

<input
type="number"
name="age"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Gender</label>

<select
name="gender"
class="form-select">

<option value="">Select Gender</option>
<option>Male</option>
<option>Female</option>
<option>Other</option>

</select>

</div>

<div class="col-md-12 mb-3">

<label class="form-label">Address</label>

<textarea
name="address"
class="form-control"
rows="4"></textarea>

</div>

<div class="col-md-12">

<button
type="submit"
class="btn btn-success">

<i class="bi bi-check-circle-fill"></i>

Save Customer

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
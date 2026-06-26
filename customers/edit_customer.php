<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid customer ID.";
    header("Location: customers.php");
    exit();
}

$customer_id = $_GET['id'];

$query = "SELECT * FROM customers WHERE customer_id = $1";
$result = pg_query_params($conn, $query, array($customer_id));

if (!$result || pg_num_rows($result) == 0) {
    $_SESSION['error'] = "Customer not found.";
    header("Location: customers.php");
    exit();
}

$customer = pg_fetch_assoc($result);

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
            Edit Customer
        </li>
    </ol>
</nav>

<h2 class="mb-4">
    <i class="bi bi-pencil-square"></i>
    Edit Customer
</h2>

<?php if(isset($_SESSION['error'])) { ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php 
        echo $_SESSION['error']; 
        unset($_SESSION['error']); 
        ?>
        <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<div class="card shadow">

<div class="card-header bg-warning text-dark">
    <h4>Update Customer Information</h4>
</div>

<div class="card-body">

<form action="update_customer.php" method="POST">

<input
    type="hidden"
    name="customer_id"
    value="<?php echo htmlspecialchars($customer['customer_id']); ?>"
>

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Customer Name</label>
    <input
        type="text"
        name="customer_name"
        class="form-control"
        value="<?php echo htmlspecialchars($customer['customer_name']); ?>"
        required
    >
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Phone</label>
    <input
        type="text"
        name="phone"
        class="form-control"
        value="<?php echo htmlspecialchars($customer['phone']); ?>"
        required
    >
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Email</label>
    <input
        type="email"
        name="email"
        class="form-control"
        value="<?php echo htmlspecialchars($customer['email']); ?>"
    >
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Age</label>
    <input
        type="number"
        name="age"
        class="form-control"
        value="<?php echo htmlspecialchars($customer['age']); ?>"
    >
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Gender</label>

    <select name="gender" class="form-select">
        <option value="">Select Gender</option>

        <option value="Male" <?php if($customer['gender'] == "Male") echo "selected"; ?>>
            Male
        </option>

        <option value="Female" <?php if($customer['gender'] == "Female") echo "selected"; ?>>
            Female
        </option>

        <option value="Other" <?php if($customer['gender'] == "Other") echo "selected"; ?>>
            Other
        </option>
    </select>
</div>

<div class="col-md-12 mb-3">
    <label class="form-label">Address</label>
    <textarea
        name="address"
        class="form-control"
        rows="4"
    ><?php echo htmlspecialchars($customer['address']); ?></textarea>
</div>

<div class="col-md-12">
    <button type="submit" class="btn btn-warning">
        <i class="bi bi-check-circle-fill"></i>
        Update Customer
    </button>

    <a href="customers.php" class="btn btn-secondary">
        Cancel
    </a>
</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>
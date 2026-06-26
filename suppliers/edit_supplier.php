<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid supplier ID.";
    header("Location: suppliers.php");
    exit();
}

$supplier_id = $_GET['id'];

$result = pg_query_params(
    $conn,
    "SELECT * FROM suppliers WHERE supplier_id = $1",
    array($supplier_id)
);

if (!$result || pg_num_rows($result) == 0) {
    $_SESSION['error'] = "Supplier not found.";
    header("Location: suppliers.php");
    exit();
}

$supplier = pg_fetch_assoc($result);
?>

<div class="d-flex">
<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<h2 class="mb-4">
    <i class="bi bi-pencil-square"></i>
    Edit Supplier
</h2>

<div class="card shadow">
<div class="card-header bg-warning text-dark">
    Update Supplier Information
</div>

<div class="card-body">

<form action="update_supplier.php" method="POST">

<input type="hidden" name="supplier_id" value="<?= htmlspecialchars($supplier['supplier_id']); ?>">

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Supplier Name</label>
    <input type="text" name="supplier_name" class="form-control"
           value="<?= htmlspecialchars($supplier['supplier_name']); ?>" required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Contact Person</label>
    <input type="text" name="contact_person" class="form-control"
           value="<?= htmlspecialchars($supplier['contact_person']); ?>">
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" class="form-control"
           value="<?= htmlspecialchars($supplier['phone']); ?>" required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control"
           value="<?= htmlspecialchars($supplier['email']); ?>">
</div>

<div class="col-md-12 mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control" rows="4"><?= htmlspecialchars($supplier['address']); ?></textarea>
</div>

<div class="col-md-12">
    <button type="submit" class="btn btn-warning">
        <i class="bi bi-check-circle-fill"></i>
        Update Supplier
    </button>

    <a href="suppliers.php" class="btn btn-secondary">
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
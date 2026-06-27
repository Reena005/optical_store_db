<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

if (!isset($_GET['customer_id'])) {
    $_SESSION['error'] = "Invalid customer.";
    header("Location: ../customers/customers.php");
    exit();
}

$customer_id = $_GET['customer_id'];

$customerQuery = pg_query_params(
    $conn,
    "SELECT * FROM customers WHERE customer_id = $1",
    array($customer_id)
);

if (!$customerQuery || pg_num_rows($customerQuery) == 0) {
    $_SESSION['error'] = "Customer not found.";
    header("Location: ../customers/customers.php");
    exit();
}

$customer = pg_fetch_assoc($customerQuery);

$prescriptionQuery = pg_query_params(
    $conn,
    "SELECT * FROM prescriptions WHERE customer_id = $1 ORDER BY prescription_id DESC LIMIT 1",
    array($customer_id)
);

$prescription = pg_fetch_assoc($prescriptionQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $right_sph = trim($_POST['right_sph']);
    $right_cyl = trim($_POST['right_cyl']);
    $right_axis = trim($_POST['right_axis']);
    $right_add = trim($_POST['right_add']);

    $left_sph = trim($_POST['left_sph']);
    $left_cyl = trim($_POST['left_cyl']);
    $left_axis = trim($_POST['left_axis']);
    $left_add = trim($_POST['left_add']);

    $doctor_name = trim($_POST['doctor_name']);
    $prescription_date = $_POST['prescription_date'];

    $result = pg_query_params(
        $conn,
        "INSERT INTO prescriptions
        (customer_id, right_sph, right_cyl, right_axis, right_add,
         left_sph, left_cyl, left_axis, left_add, doctor_name, prescription_date)
         VALUES($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11)",
        array(
            $customer_id,
            $right_sph,
            $right_cyl,
            $right_axis,
            $right_add,
            $left_sph,
            $left_cyl,
            $left_axis,
            $left_add,
            $doctor_name,
            $prescription_date
        )
    );

    if ($result) {
        $_SESSION['success'] = "New prescription saved successfully.";
    } else {
        $_SESSION['error'] = "Unable to save prescription: " . pg_last_error($conn);
    }

    header("Location: history.php?customer_id=" . $customer_id);
    exit();
}
 
?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-eyeglasses"></i>
        Customer Prescription
    </h2>
    <div>
   
    <a href="../customers/customers.php" class="btn btn-secondary">
        Back
    </a>
</div>
</div>

<?php if(isset($_SESSION['success'])) { ?>
<div class="alert alert-success">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<?php } ?>

<?php if(isset($_SESSION['error'])) { ?>
<div class="alert alert-danger">
    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
</div>
<?php } ?>

<div class="card shadow mb-4">
<div class="card-header bg-primary text-white">
    Customer Details
</div>
<div class="card-body">
    <h5><?= htmlspecialchars($customer['customer_name']); ?></h5>
    <p class="mb-1">Phone: <?= htmlspecialchars($customer['phone']); ?></p>
    <p class="mb-1">Email: <?= htmlspecialchars($customer['email']); ?></p>
    <p class="mb-0">Address: <?= htmlspecialchars($customer['address']); ?></p>
</div>
</div>

<div class="card shadow">
<div class="card-header bg-dark text-white">
    Optical Prescription Matrix
</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6">
    <h4 class="text-primary">Right Eye - OD</h4>

    <div class="mb-3">
        <label class="form-label">SPH</label>
        <input type="text" name="right_sph" class="form-control"
        value="<?= htmlspecialchars($prescription['right_sph'] ?? ''); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">CYL</label>
        <input type="text" name="right_cyl" class="form-control"
        value="<?= htmlspecialchars($prescription['right_cyl'] ?? ''); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">AXIS</label>
        <input type="text" name="right_axis" class="form-control"
        value="<?= htmlspecialchars($prescription['right_axis'] ?? ''); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">ADD</label>
        <input type="text" name="right_add" class="form-control"
        value="<?= htmlspecialchars($prescription['right_add'] ?? ''); ?>">
    </div>
</div>

<div class="col-md-6">
    <h4 class="text-success">Left Eye - OS</h4>

    <div class="mb-3">
        <label class="form-label">SPH</label>
        <input type="text" name="left_sph" class="form-control"
        value="<?= htmlspecialchars($prescription['left_sph'] ?? ''); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">CYL</label>
        <input type="text" name="left_cyl" class="form-control"
        value="<?= htmlspecialchars($prescription['left_cyl'] ?? ''); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">AXIS</label>
        <input type="text" name="left_axis" class="form-control"
        value="<?= htmlspecialchars($prescription['left_axis'] ?? ''); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">ADD</label>
        <input type="text" name="left_add" class="form-control"
        value="<?= htmlspecialchars($prescription['left_add'] ?? ''); ?>">
    </div>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Doctor / Optometrist Name</label>
    <input type="text" name="doctor_name" class="form-control"
    value="<?= htmlspecialchars($prescription['doctor_name'] ?? ''); ?>">
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Prescription Date</label>
    <input type="date" name="prescription_date" class="form-control"
    value="<?= htmlspecialchars($prescription['prescription_date'] ?? date('Y-m-d')); ?>">
</div>

<div class="col-md-12">
    <button class="btn btn-success">
        <i class="bi bi-check-circle-fill"></i>
        Save Prescription
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
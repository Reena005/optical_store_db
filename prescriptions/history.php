<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

if (!isset($_GET['customer_id'])) {
    header("Location: ../customers/customers.php");
    exit();
}

$customer_id = $_GET['customer_id'];

$customerQuery = pg_query_params(
    $conn,
    "SELECT * FROM customers WHERE customer_id=$1",
    array($customer_id)
);

$customer = pg_fetch_assoc($customerQuery);

$result = pg_query_params(
    $conn,
    "SELECT *
     FROM prescriptions
     WHERE customer_id=$1
     ORDER BY prescription_date DESC, prescription_id DESC",
    array($customer_id)
);
?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-clock-history"></i>
        Prescription History
    </h2>

    <div>
        <a href="prescription.php?customer_id=<?= $customer_id; ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill"></i>
            Add New Prescription
        </a>
  <a href="../prescriptions/prescription_pdf.php" class="btn btn-secondary">
         Download PDF
    </a>
        <a href="../customers/customers.php" class="btn btn-secondary">
            Back
        </a>
    </div>
</div>

<div class="card shadow mb-4">
<div class="card-header bg-primary text-white">
    Customer Details
</div>
<div class="card-body">
    <h5><?= htmlspecialchars($customer['customer_name']); ?></h5>
    <p class="mb-1">Phone: <?= htmlspecialchars($customer['phone']); ?></p>
    <p class="mb-0">Email: <?= htmlspecialchars($customer['email']); ?></p>
</div>
</div>

<div class="card shadow">
<div class="card-header bg-dark text-white">
    Previous Prescriptions
</div>

<div class="card-body">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">
<tr>
    <th>Date</th>
    <th>Doctor</th>
    <th>Right Eye SPH</th>
    <th>Right Eye CYL</th>
    <th>Left Eye SPH</th>
    <th>Left Eye CYL</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php if($result && pg_num_rows($result) > 0) { ?>

<?php while($row = pg_fetch_assoc($result)) { ?>

<tr>
    <td><?= htmlspecialchars($row['prescription_date']); ?></td>
    <td><?= htmlspecialchars($row['doctor_name']); ?></td>
    <td><?= htmlspecialchars($row['right_sph']); ?></td>
    <td><?= htmlspecialchars($row['right_cyl']); ?></td>
    <td><?= htmlspecialchars($row['left_sph']); ?></td>
    <td><?= htmlspecialchars($row['left_cyl']); ?></td>

    <td>
        <a href="prescription_pdf.php?prescription_id=<?= $row['prescription_id']; ?>" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill"></i>
        </a>

        <a href="delete_prescription.php?id=<?= $row['prescription_id']; ?>&customer_id=<?= $customer_id; ?>"
           class="btn btn-dark btn-sm"
           onclick="return confirm('Delete this prescription?');">
            <i class="bi bi-trash-fill"></i>
        </a>
    </td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="7" class="text-center text-muted">
        No prescription history found
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
<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$customers = pg_query(
    $conn,
    "SELECT customer_id, customer_name, phone
     FROM customers
     ORDER BY customer_name"
);

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<h2 class="mb-4">
    <i class="bi bi-calendar-plus-fill"></i>
    Add Eye Test Appointment
</h2>

<div class="card shadow">

<div class="card-header bg-primary text-white">
    Appointment Details
</div>

<div class="card-body">

<form action="appointment_process.php" method="POST">

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Customer</label>

    <select name="customer_id" class="form-select" required>
        <option value="">Select Customer</option>

        <?php while($c = pg_fetch_assoc($customers)) { ?>
            <option value="<?= $c['customer_id']; ?>">
                <?= htmlspecialchars($c['customer_name']); ?>
                - <?= htmlspecialchars($c['phone'] ?? '-'); ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Doctor / Optometrist</label>

    <input
        type="text"
        name="doctor_name"
        class="form-control"
        placeholder="Example: Dr. Kumar / Optometrist Reena"
        required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Appointment Date</label>

    <input
        type="date"
        name="appointment_date"
        class="form-control"
        required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Appointment Time</label>

    <input
        type="time"
        name="appointment_time"
        class="form-control"
        required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Status</label>

    <select name="status" class="form-select">
        <option value="Booked">Booked</option>
        <option value="Checked">Checked</option>
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
    </select>
</div>

<div class="col-md-12 mb-3">
    <label class="form-label">Notes</label>

    <textarea
        name="notes"
        class="form-control"
        rows="4"
        placeholder="Example: Customer requested evening eye test"></textarea>
</div>

<div class="col-md-12">
    <button class="btn btn-success">
        <i class="bi bi-check-circle-fill"></i>
        Save Appointment
    </button>

    <a href="appointments.php" class="btn btn-secondary">
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
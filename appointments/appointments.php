<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$result = pg_query($conn, "
SELECT 
    a.appointment_id,
    a.doctor_name,
    a.appointment_date,
    a.appointment_time,
    a.status,
    a.notes,
    c.customer_name,
    c.phone
FROM appointments a
LEFT JOIN customers c
ON a.customer_id = c.customer_id
ORDER BY a.appointment_date ASC, a.appointment_time ASC
");

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-calendar-check-fill"></i>
        Eye Test Appointments
    </h2>

    <a href="add_appointment.php" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i>
        Add Appointment
    </a>
</div>

<div class="card shadow">
<div class="card-body">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Phone</th>
    <th>Doctor</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    <th>Notes</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php if($result && pg_num_rows($result) > 0) { ?>

<?php while($row = pg_fetch_assoc($result)) { ?>

<tr>
    <td><?= htmlspecialchars($row['appointment_id']); ?></td>

    <td><?= htmlspecialchars($row['customer_name'] ?? 'Deleted Customer'); ?></td>

    <td><?= htmlspecialchars($row['phone'] ?? '-'); ?></td>

    <td><?= htmlspecialchars($row['doctor_name'] ?? '-'); ?></td>

    <td><?= htmlspecialchars($row['appointment_date']); ?></td>

    <td><?= htmlspecialchars($row['appointment_time']); ?></td>

    <td>
        <?php if($row['status'] == "Completed") { ?>
            <span class="badge bg-success">Completed</span>
        <?php } elseif($row['status'] == "Cancelled") { ?>
            <span class="badge bg-danger">Cancelled</span>
        <?php } elseif($row['status'] == "Checked") { ?>
            <span class="badge bg-info text-dark">Checked</span>
        <?php } else { ?>
            <span class="badge bg-warning text-dark">Booked</span>
        <?php } ?>
    </td>

    <td><?= htmlspecialchars($row['notes'] ?? '-'); ?></td>

    <td>
        <a href="update_appointment.php?id=<?= $row['appointment_id']; ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil-fill"></i>
        </a>

        <a href="delete_appointment.php?id=<?= $row['appointment_id']; ?>"
           class="btn btn-dark btn-sm"
           onclick="return confirm('Delete this appointment?');">
            <i class="bi bi-trash-fill"></i>
        </a>
    </td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="9" class="text-center text-muted">
        No appointments found
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
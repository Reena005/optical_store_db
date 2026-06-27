<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$result = pg_query($conn, "
SELECT *
FROM audit_logs
ORDER BY audit_id DESC
");
?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<h2 class="mb-4">
    <i class="bi bi-journal-text"></i>
    Audit Logs
</h2>

<div class="card shadow">
<div class="card-header bg-dark text-white">
    Database Activity History
</div>

<div class="card-body">

<table class="table table-bordered table-hover align-middle">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Table</th>
    <th>Record ID</th>
    <th>Action</th>
    <th>Old Data</th>
    <th>New Data</th>
    <th>Time</th>
</tr>
</thead>

<tbody>

<?php if($result && pg_num_rows($result) > 0) { ?>

<?php while($row = pg_fetch_assoc($result)) { ?>

<tr>
    <td><?= htmlspecialchars($row['audit_id']); ?></td>

    <td><?= htmlspecialchars($row['table_name']); ?></td>

    <td><?= htmlspecialchars($row['record_id']); ?></td>

    <td>
        <?php if($row['action_type'] == "INSERT") { ?>
            <span class="badge bg-success">INSERT</span>
        <?php } elseif($row['action_type'] == "UPDATE") { ?>
            <span class="badge bg-warning text-dark">UPDATE</span>
        <?php } else { ?>
            <span class="badge bg-danger">DELETE</span>
        <?php } ?>
    </td>

    <td>
        <textarea class="form-control" rows="4" readonly><?= htmlspecialchars($row['old_data'] ?? '-'); ?></textarea>
    </td>

    <td>
        <textarea class="form-control" rows="4" readonly><?= htmlspecialchars($row['new_data'] ?? '-'); ?></textarea>
    </td>

    <td><?= htmlspecialchars($row['action_time']); ?></td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="7" class="text-center text-muted">
        No audit logs found
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
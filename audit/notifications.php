<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$result = pg_query($conn, "
SELECT n.*, p.product_name, p.stock
FROM notifications n
LEFT JOIN products p ON n.product_id = p.product_id
ORDER BY n.notification_id DESC
");
?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<h2 class="mb-4">
    <i class="bi bi-bell-fill"></i>
    Stock Notifications
</h2>

<div class="card shadow">
<div class="card-header bg-danger text-white">
    Low Stock Alerts
</div>

<div class="card-body">

<table class="table table-bordered table-hover align-middle">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Product</th>
    <th>Message</th>
    <th>Status</th>
    <th>Time</th>
</tr>
</thead>

<tbody>

<?php if($result && pg_num_rows($result) > 0) { ?>

<?php while($row = pg_fetch_assoc($result)) { ?>

<tr>
    <td><?= htmlspecialchars($row['notification_id']); ?></td>

    <td><?= htmlspecialchars($row['product_name'] ?? 'Deleted Product'); ?></td>

    <td><?= htmlspecialchars($row['message']); ?></td>

    <td>
        <span class="badge bg-warning text-dark">
            <?= htmlspecialchars($row['status']); ?>
        </span>
    </td>

    <td><?= htmlspecialchars($row['created_at']); ?></td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="5" class="text-center text-muted">
        No notifications found
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
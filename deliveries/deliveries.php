<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$result = pg_query($conn, "
SELECT d.*, o.total_amount, c.customer_name
FROM deliveries d
JOIN orders o ON d.order_id = o.order_id
LEFT JOIN customers c ON o.customer_id = c.customer_id
ORDER BY d.delivery_id ASC
");
?>

<div class="d-flex">
<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-truck-front-fill"></i> Deliveries</h2>

    <a href="add_delivery.php" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i> Add Delivery
    </a>
</div>

<div class="card shadow">
<div class="card-body">

<table class="table table-bordered table-hover align-middle">
<thead class="table-dark">
<tr>
    <th>Order</th>
    <th>Customer</th>
    <th>Type</th>
    <th>Courier</th>
    <th>Tracking No</th>
    <th>Status</th>
    <th>Expected</th>
    <th>Delivered</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php if($result && pg_num_rows($result) > 0){ ?>

<?php while($row = pg_fetch_assoc($result)){ ?>

<tr>
    <td>#<?= htmlspecialchars($row['order_id']); ?></td>

    <td><?= htmlspecialchars($row['customer_name'] ?? 'Deleted Customer'); ?></td>

    <td>
        <?php if(($row['delivery_type'] ?? '') == 'Store Pickup'){ ?>
            <span class="badge bg-secondary">
                <i class="bi bi-shop"></i>
                Store Pickup
            </span>
        <?php } else { ?>
            <span class="badge bg-primary">
                <i class="bi bi-house-door-fill"></i>
                Home Delivery
            </span>
        <?php } ?>
    </td>

    <td><?= htmlspecialchars($row['courier_name'] ?? '-'); ?></td>

    <td><?= htmlspecialchars($row['tracking_number'] ?? '-'); ?></td>

    <td>
        <?php
        $status = $row['delivery_status'];

        if($status == "Delivered"){
            $badge = "bg-success";
        } elseif($status == "Out for Delivery"){
            $badge = "bg-warning text-dark";
        } elseif($status == "Shipped"){
            $badge = "bg-info text-dark";
        } elseif($status == "Packed"){
            $badge = "bg-primary";
        } else {
            $badge = "bg-secondary";
        }
        ?>

        <span class="badge <?= $badge; ?>">
            <?= htmlspecialchars($status); ?>
        </span>
    </td>

    <td><?= htmlspecialchars($row['expected_date'] ?? '-'); ?></td>

    <td><?= htmlspecialchars($row['delivered_date'] ?? '-'); ?></td>

    <td>
        <a href="update_delivery.php?id=<?= $row['delivery_id']; ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil-fill"></i>
        </a>
    </td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="9" class="text-center text-muted">
        No deliveries found
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
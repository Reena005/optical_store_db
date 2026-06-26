<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);

    $query = "
        SELECT 
            o.order_id,
            o.order_date,
            o.total_amount,
            o.payment_status,
            o.payment_mode,
            c.customer_name
        FROM orders o
        LEFT JOIN customers c
        ON o.customer_id = c.customer_id
        WHERE c.customer_name ILIKE $1
           OR CAST(o.order_id AS TEXT) ILIKE $1
           OR o.payment_status ILIKE $1
        ORDER BY o.order_id ASC
    ";

    $orders = pg_query_params($conn, $query, array("%" . $search . "%"));

} else {
    $query = "
        SELECT 
            o.order_id,
            o.order_date,
            o.total_amount,
            o.payment_status,
            o.payment_mode,
            c.customer_name
        FROM orders o
        LEFT JOIN customers c
        ON o.customer_id = c.customer_id
        ORDER BY o.order_id ASC
    ";

    $orders = pg_query($conn, $query);
}

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>
        <i class="bi bi-receipt"></i>
        Orders & Billing
    </h2>

    <a href="create_order.php" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i>
        Create Order
    </a>

</div>

<?php if(isset($_SESSION['success'])) { ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php } ?>

<?php if(isset($_SESSION['error'])) { ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php } ?>

<form method="GET" class="mb-4">

    <div class="row">

        <div class="col-md-4">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search by customer, order ID, or status..."
                value="<?= htmlspecialchars($search); ?>"
            >
        </div>

        <div class="col-md-2">
            <button class="btn btn-success">
                <i class="bi bi-search"></i>
                Search
            </button>
        </div>

       

</form>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Date</th>
    <th>Total Amount</th>
    <th>Payment Status</th>
    <th>Payment Mode</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php if ($orders && pg_num_rows($orders) > 0) { ?>

<?php while ($row = pg_fetch_assoc($orders)) { ?>

<tr>

    <td>#<?= htmlspecialchars($row['order_id']); ?></td>

    <td><?= htmlspecialchars($row['customer_name'] ?? 'Deleted Customer'); ?></td>

    <td><?= date("d M Y, h:i A", strtotime($row['order_date'])); ?></td>

    <td>₹<?= htmlspecialchars($row['total_amount']); ?></td>

    <td>
        <?php if($row['payment_status'] == "Paid") { ?>
            <span class="badge bg-success">Paid</span>
        <?php } else { ?>
            <span class="badge bg-warning text-dark">Pending</span>
        <?php } ?>
    </td>
    <td>
    <span class="badge bg-info">
        <?= htmlspecialchars($row['payment_mode']); ?>
    </span>
<td>

<a
href="edit_order.php?id=<?= $row['order_id']; ?>"
class="btn btn-warning btn-sm"
title="Edit">

<i class="bi bi-pencil-fill"></i>

</a>

<a
href="view_order.php?id=<?= $row['order_id']; ?>"
class="btn btn-info btn-sm"
title="View">

<i class="bi bi-eye-fill"></i>

</a>

<a
href="invoice_pdf.php?id=<?= $row['order_id']; ?>"
class="btn btn-danger btn-sm"
title="PDF">

<i class="bi bi-file-earmark-pdf-fill"></i>

</a>

<a
href="delete_order.php?id=<?= $row['order_id']; ?>"
class="btn btn-dark btn-sm"
onclick="return confirm('Delete this order?')"
title="Delete">

<i class="bi bi-trash-fill"></i>

</a>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="6" class="text-center text-muted">
        No Orders Found
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
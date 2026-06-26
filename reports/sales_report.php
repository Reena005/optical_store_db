<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$from_date = $_GET['from_date'] ?? "";
$to_date = $_GET['to_date'] ?? "";

if (!empty($from_date) && !empty($to_date)) {

    $query = "
        SELECT 
            o.order_id,
            o.order_date,
            o.total_amount,
            o.payment_status,
            c.customer_name
        FROM orders o
        LEFT JOIN customers c
        ON o.customer_id = c.customer_id
        WHERE DATE(o.order_date) BETWEEN $1 AND $2
        ORDER BY o.order_date DESC
    ";

    $sales = pg_query_params($conn, $query, array($from_date, $to_date));

    $totalQuery = pg_query_params(
        $conn,
        "SELECT COALESCE(SUM(total_amount),0)
         FROM orders
         WHERE payment_status='Paid'
         AND DATE(order_date) BETWEEN $1 AND $2",
        array($from_date, $to_date)
    );

} else {

    $query = "
        SELECT 
            o.order_id,
            o.order_date,
            o.total_amount,
            o.payment_status,
            c.customer_name
        FROM orders o
        LEFT JOIN customers c
        ON o.customer_id = c.customer_id
        ORDER BY o.order_date DESC
    ";

    $sales = pg_query($conn, $query);

    $totalQuery = pg_query(
        $conn,
        "SELECT COALESCE(SUM(total_amount),0)
         FROM orders
         WHERE payment_status='Paid'"
    );
}

$totalRevenue = pg_fetch_result($totalQuery, 0, 0);

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>
        <i class="bi bi-graph-up-arrow"></i>
        Sales Report
    </h2>

    <a href="reports.php" class="btn btn-secondary">
        Back
    </a>

</div>

<div class="card shadow mb-4">

<div class="card-header bg-primary text-white">
    Filter Sales
</div>

<div class="card-body">

<form method="GET" class="row">

<div class="col-md-4 mb-3">
    <label class="form-label">From Date</label>
    <input
        type="date"
        name="from_date"
        class="form-control"
        value="<?= htmlspecialchars($from_date); ?>"
    >
</div>

<div class="col-md-4 mb-3">
    <label class="form-label">To Date</label>
    <input
        type="date"
        name="to_date"
        class="form-control"
        value="<?= htmlspecialchars($to_date); ?>"
    >
</div>

<div class="col-md-4 mb-3 d-flex align-items-end">

    <button class="btn btn-success me-2">
        <i class="bi bi-funnel-fill"></i>
        Filter
    </button>

    <a href="sales_report.php" class="btn btn-secondary">
        Reset
    </a>

</div>

</form>

</div>

</div>

<div class="row mb-4">

<div class="col-md-4">
    <div class="card bg-success text-white shadow">
        <div class="card-body">
            <h5>Total Paid Revenue</h5>
            <h2>₹<?= number_format($totalRevenue, 2); ?></h2>
        </div>
    </div>
</div>

</div>

<div class="card shadow">

<div class="card-header bg-dark text-white">
    Sales Details
</div>

<div class="card-body">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">
<tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Date</th>
    <th>Total Amount</th>
    <th>Payment Status</th>
</tr>
</thead>

<tbody>

<?php if ($sales && pg_num_rows($sales) > 0) { ?>

<?php while ($row = pg_fetch_assoc($sales)) { ?>

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
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="5" class="text-center text-muted">
        No sales found
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
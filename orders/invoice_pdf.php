<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid order ID.";
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];

$orderQuery = "
SELECT 
    o.order_id,
    o.order_date,
    o.total_amount,
    o.payment_status,
    c.customer_name,
    c.phone,
    c.email,
    c.address
FROM orders o
LEFT JOIN customers c
ON o.customer_id = c.customer_id
WHERE o.order_id = $1
";

$orderResult = pg_query_params($conn, $orderQuery, array($order_id));

if (!$orderResult || pg_num_rows($orderResult) == 0) {
    $_SESSION['error'] = "Order not found.";
    header("Location: orders.php");
    exit();
}

$order = pg_fetch_assoc($orderResult);

$itemQuery = "
SELECT 
    oi.quantity,
    oi.price,
    oi.subtotal,
    p.product_name,
    p.brand
FROM order_items oi
LEFT JOIN products p
ON oi.product_id = p.product_id
WHERE oi.order_id = $1
";

$items = pg_query_params($conn, $itemQuery, array($order_id));

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>
        <i class="bi bi-receipt-cutoff"></i>
        Invoice
    </h2>

    <div>
        <a href="orders.php" class="btn btn-secondary">
            Back
        </a>

        <a href="invoice_pdf.php?id=<?= $order['order_id']; ?>" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            Download PDF
        </a>
    </div>

</div>

<div class="card shadow">

<div class="card-body">

<div class="row mb-4">

    <div class="col-md-6">
        <h4>Optical Store Management System</h4>
        <p>
            Clarity Optical Store<br>
            Chennai, Tamil Nadu<br>
            Phone: 9876543210
        </p>
    </div>

    <div class="col-md-6 text-end">
        <h5>Invoice #<?= htmlspecialchars($order['order_id']); ?></h5>
        <p>
            Date:
            <?= date("d M Y, h:i A", strtotime($order['order_date'])); ?>
        </p>

        <?php if($order['payment_status'] == "Paid") { ?>
            <span class="badge bg-success">Paid</span>
        <?php } else { ?>
            <span class="badge bg-warning text-dark">Pending</span>
        <?php } ?>
    </div>

</div>

<hr>

<div class="row mb-4">

    <div class="col-md-6">
        <h5>Bill To:</h5>

        <p>
            <strong><?= htmlspecialchars($order['customer_name'] ?? 'Deleted Customer'); ?></strong><br>
            Phone: <?= htmlspecialchars($order['phone'] ?? '-'); ?><br>
            Email: <?= htmlspecialchars($order['email'] ?? '-'); ?><br>
            Address: <?= htmlspecialchars($order['address'] ?? '-'); ?>
        </p>
    </div>

</div>

<table class="table table-bordered">

<thead class="table-dark">

<tr>
    <th>Product</th>
    <th>Brand</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Subtotal</th>
</tr>

</thead>

<tbody>

<?php while($item = pg_fetch_assoc($items)) { ?>

<tr>
    <td><?= htmlspecialchars($item['product_name'] ?? 'Deleted Product'); ?></td>
    <td><?= htmlspecialchars($item['brand'] ?? '-'); ?></td>
    <td>₹<?= htmlspecialchars($item['price']); ?></td>
    <td><?= htmlspecialchars($item['quantity']); ?></td>
    <td>₹<?= htmlspecialchars($item['subtotal']); ?></td>
</tr>

<?php } ?>

</tbody>

<tfoot>

<tr>
    <th colspan="4" class="text-end">
        Total
    </th>

    <th>
        ₹<?= htmlspecialchars($order['total_amount']); ?>
    </th>
</tr>

</tfoot>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>
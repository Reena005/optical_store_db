<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];

$orderResult = pg_query_params($conn, "
SELECT o.*, c.customer_name, c.phone, c.email, c.address
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.customer_id
WHERE o.order_id = $1
", array($order_id));

$order = pg_fetch_assoc($orderResult);

$items = pg_query_params($conn, "
SELECT oi.*, p.product_name, p.brand
FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.product_id
WHERE oi.order_id = $1
", array($order_id));
?>

<div class="d-flex">
<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-receipt-cutoff"></i>
        Order Details #<?= htmlspecialchars($order['order_id']); ?>
    </h2>

    <div>
        <a href="edit_order.php?id=<?= $order['order_id']; ?>" class="btn btn-warning">
            <i class="bi bi-pencil-fill"></i> Edit
        </a>

        <a href="invoice_pdf.php?id=<?= $order['order_id']; ?>" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf-fill"></i> PDF
        </a>

        <a href="orders.php" class="btn btn-secondary">
            Back
        </a>
    </div>
</div>

<div class="row mb-4">

    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                Customer Details
            </div>
            <div class="card-body">
                <h5><?= htmlspecialchars($order['customer_name'] ?? 'Deleted Customer'); ?></h5>
                <p class="mb-1">📞 <?= htmlspecialchars($order['phone'] ?? '-'); ?></p>
                <p class="mb-1">📧 <?= htmlspecialchars($order['email'] ?? '-'); ?></p>
                <p class="mb-0">📍 <?= htmlspecialchars($order['address'] ?? '-'); ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                Payment Details
            </div>
            <div class="card-body">
                <p>
                    Status:
                    <?php if($order['payment_status'] == "Paid") { ?>
                        <span class="badge bg-success">Paid</span>
                    <?php } else { ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php } ?>
                </p>

                <p>
                    Mode:
                    <span class="badge bg-info">
                        <?= htmlspecialchars($order['payment_mode'] ?? '-'); ?>
                    </span>
                </p>

                <h4>₹<?= number_format($order['total_amount'], 2); ?></h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                Order Info
            </div>
            <div class="card-body">
                <p>Order ID: #<?= htmlspecialchars($order['order_id']); ?></p>
                <p>Date: <?= date("d M Y, h:i A", strtotime($order['order_date'])); ?></p>
                <p>Store: Clarity Optical Store</p>
            </div>
        </div>
    </div>

</div>

<div class="card shadow">
<div class="card-header bg-dark text-white">
    Ordered Products
</div>

<div class="card-body">
<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">
<tr>
    <th>Product</th>
    <th>Brand</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
</tr>
</thead>

<tbody>
<?php while($item = pg_fetch_assoc($items)) { ?>
<tr>
    <td><?= htmlspecialchars($item['product_name'] ?? 'Deleted Product'); ?></td>
    <td><?= htmlspecialchars($item['brand'] ?? '-'); ?></td>
    <td>₹<?= number_format($item['price'], 2); ?></td>
    <td><?= htmlspecialchars($item['quantity']); ?></td>
    <td>₹<?= number_format($item['subtotal'], 2); ?></td>
</tr>
<?php } ?>
</tbody>

<tfoot>
<tr>
    <th colspan="4" class="text-end">Grand Total</th>
    <th>₹<?= number_format($order['total_amount'], 2); ?></th>
</tr>
</tfoot>

</table>
</div>
</div>

</div>
</div>
</div>

<?php include("../includes/footer.php"); ?>
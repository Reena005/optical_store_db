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

$orderResult = pg_query_params($conn, "
SELECT 
    o.order_id,
    o.order_date,
    o.total_amount,
    o.payment_status,
    o.payment_mode,
    c.customer_name,
    c.phone,
    c.email,
    c.address
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.customer_id
WHERE o.order_id = $1
", array($order_id));

if (!$orderResult || pg_num_rows($orderResult) == 0) {
    $_SESSION['error'] = "Order not found.";
    header("Location: orders.php");
    exit();
}

$order = pg_fetch_assoc($orderResult);

$items = pg_query_params($conn, "
SELECT 
    oi.quantity,
    oi.price,
    oi.subtotal,
    p.product_name,
    p.brand
FROM order_items oi
LEFT JOIN products p ON oi.product_id = p.product_id
WHERE oi.order_id = $1
", array($order_id));

$packageResult = pg_query_params($conn, "
SELECT 
    op.package_price,
    op.remarks,
    fp.product_name AS frame_name,
    fp.brand AS frame_brand,
    lt.lens_name,
    lt.price AS lens_price,
    lc.coating_name,
    lc.price AS coating_price
FROM order_packages op
LEFT JOIN products fp ON op.frame_product_id = fp.product_id
LEFT JOIN lens_types lt ON op.lens_id = lt.lens_id
LEFT JOIN lens_coatings lc ON op.coating_id = lc.coating_id
WHERE op.order_id = $1
", array($order_id));

$package = ($packageResult && pg_num_rows($packageResult) > 0)
    ? pg_fetch_assoc($packageResult)
    : null;
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
        <a href="orders.php" class="btn btn-secondary">Back</a>

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
        <h4>Clarity Optical Store</h4>
        <p>
            Optical Store Management System<br>
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

        <span class="badge bg-info">
            <?= htmlspecialchars($order['payment_mode'] ?? '-'); ?>
        </span>
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

<?php if($package) { ?>

<div class="alert alert-primary">
    <strong>Optical Package Order</strong>
    <br>
    This order includes frame, lens and coating details.
</div>

<table class="table table-bordered align-middle">
<thead class="table-dark">
<tr>
    <th>Component</th>
    <th>Details</th>
    <th>Amount</th>
</tr>
</thead>

<tbody>
<tr>
    <td>Frame</td>
    <td>
        <?= htmlspecialchars($package['frame_name'] ?? 'Deleted Frame'); ?>
        <br>
        <small><?= htmlspecialchars($package['frame_brand'] ?? '-'); ?></small>
    </td>
    <td>
        <?php
        $frameAmount = 0;
        pg_result_seek($items, 0);
        $firstItem = pg_fetch_assoc($items);
        if($firstItem) {
            $frameAmount = $firstItem['subtotal'];
        }
        ?>
        ₹<?= number_format($frameAmount, 2); ?>
    </td>
</tr>

<tr>
    <td>Lens</td>
    <td><?= htmlspecialchars($package['lens_name'] ?? 'No Lens'); ?></td>
    <td>₹<?= number_format($package['lens_price'] ?? 0, 2); ?></td>
</tr>

<tr>
    <td>Coating</td>
    <td><?= htmlspecialchars($package['coating_name'] ?? 'No Coating'); ?></td>
    <td>₹<?= number_format($package['coating_price'] ?? 0, 2); ?></td>
</tr>

<?php if(!empty($package['remarks'])) { ?>
<tr>
    <td>Remarks</td>
    <td colspan="2"><?= htmlspecialchars($package['remarks']); ?></td>
</tr>
<?php } ?>

</tbody>

<tfoot>
<tr>
    <th colspan="2" class="text-end">Grand Total</th>
    <th>₹<?= number_format($order['total_amount'], 2); ?></th>
</tr>
</tfoot>
</table>

<?php } else { ?>

<table class="table table-bordered align-middle">
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
    <td>₹<?= number_format($item['price'], 2); ?></td>
    <td><?= htmlspecialchars($item['quantity']); ?></td>
    <td>₹<?= number_format($item['subtotal'], 2); ?></td>
</tr>
<?php } ?>
</tbody>

<tfoot>
<tr>
    <th colspan="4" class="text-end">Total</th>
    <th>₹<?= number_format($order['total_amount'], 2); ?></th>
</tr>
</tfoot>
</table>

<?php } ?>

</div>
</div>

</div>
</div>
</div>

<?php include("../includes/footer.php"); ?>
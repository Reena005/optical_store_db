<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$orders = pg_query($conn, "
SELECT
    o.order_id,
    o.total_amount,
    c.customer_name,
    c.address
FROM orders o
LEFT JOIN customers c
ON o.customer_id = c.customer_id
WHERE o.order_id NOT IN (
    SELECT order_id FROM deliveries
)
ORDER BY o.order_id ASC
");
?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<h2 class="mb-4">
    <i class="bi bi-truck"></i>
    Add Delivery
</h2>

<div class="card shadow">

<div class="card-header bg-primary text-white">
    Delivery Information
</div>

<div class="card-body">

<form action="delivery_process.php" method="POST">

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Order</label>

    <select name="order_id" id="orderSelect" class="form-select" required>
        <option value="" data-address="">Select Order</option>

        <?php while($o = pg_fetch_assoc($orders)) { ?>
            <option
                value="<?= $o['order_id']; ?>"
                data-address="<?= htmlspecialchars($o['address'] ?? ''); ?>"
            >
                Order #<?= $o['order_id']; ?>
                -
                <?= htmlspecialchars($o['customer_name'] ?? 'Deleted Customer'); ?>
                -
                ₹<?= htmlspecialchars($o['total_amount']); ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Delivery Type</label>

    <select name="delivery_type" id="deliveryType" class="form-select" required>
        <option value="Home Delivery">Home Delivery</option>
        <option value="Store Pickup">Store Pickup</option>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Courier Name</label>

    <select name="courier_name" id="courierName" class="form-select">
        <option value="">Select Courier</option>
        <option>Blue Dart</option>
        <option>DTDC</option>
        <option>Delhivery</option>
        <option>India Post</option>
        <option>XpressBees</option>
        <option>Store Delivery</option>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Tracking Number</label>

    <input
        type="text"
        name="tracking_number"
        id="trackingNumber"
        class="form-control"
        placeholder="Enter tracking number">
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Delivery Status</label>

    <select name="delivery_status" class="form-select">
        <option>Processing</option>
        <option>Packed</option>
        <option>Shipped</option>
        <option>Out for Delivery</option>
        <option>Delivered</option>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Expected Delivery / Pickup Date</label>

    <input
        type="date"
        name="expected_date"
        class="form-control">
</div>

<div class="col-md-12 mb-3">
    <label class="form-label">Delivery / Pickup Address</label>

    <textarea
        name="delivery_address"
        id="deliveryAddress"
        class="form-control"
        rows="4"
        readonly></textarea>
</div>

<div class="col-md-12">
    <button class="btn btn-success">
        <i class="bi bi-check-circle-fill"></i>
        Save Delivery
    </button>

    <a href="deliveries.php" class="btn btn-secondary">
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

<script>
const orderSelect = document.getElementById("orderSelect");
const deliveryType = document.getElementById("deliveryType");
const deliveryAddress = document.getElementById("deliveryAddress");
const courierName = document.getElementById("courierName");
const trackingNumber = document.getElementById("trackingNumber");

const storeAddress = "Clarity Optical Store\n12, Sardar Patel Road,\nGuindy,\nChennai - 600025";

function updateDeliveryDetails() {
    let selectedOrder = orderSelect.options[orderSelect.selectedIndex];
    let customerAddress = selectedOrder.getAttribute("data-address") || "";

    if (deliveryType.value === "Store Pickup") {
        deliveryAddress.value = storeAddress;
        courierName.value = "Store Delivery";
        trackingNumber.value = "Store Pickup";
        courierName.disabled = true;
        trackingNumber.readOnly = true;
    } else {
        deliveryAddress.value = customerAddress;
        courierName.disabled = false;
        trackingNumber.readOnly = false;

        if (courierName.value === "Store Delivery") {
            courierName.value = "";
        }

        if (trackingNumber.value === "Store Pickup") {
            trackingNumber.value = "";
        }
    }
}

orderSelect.addEventListener("change", updateDeliveryDetails);
deliveryType.addEventListener("change", updateDeliveryDetails);
</script>

<?php include("../includes/footer.php"); ?>
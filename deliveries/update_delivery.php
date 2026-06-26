<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid delivery ID.";
    header("Location: deliveries.php");
    exit();
}

$delivery_id = $_GET['id'];

$result = pg_query_params(
    $conn,
    "SELECT d.*, o.order_id, c.customer_name
     FROM deliveries d
     JOIN orders o ON d.order_id = o.order_id
     LEFT JOIN customers c ON o.customer_id = c.customer_id
     WHERE d.delivery_id = $1",
    array($delivery_id)
);

if (!$result || pg_num_rows($result) == 0) {
    $_SESSION['error'] = "Delivery not found.";
    header("Location: deliveries.php");
    exit();
}

$delivery = pg_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $courier_name = $_POST['courier_name'];
    $tracking_number = trim($_POST['tracking_number']);
    $delivery_status = $_POST['delivery_status'];
    $delivery_type = $_POST['delivery_type'];
    $delivery_address = trim($_POST['delivery_address']);

    $expected_date = !empty($_POST['expected_date'])
        ? $_POST['expected_date']
        : null;

    $delivered_date = !empty($_POST['delivered_date'])
        ? $_POST['delivered_date']
        : null;

    if ($delivery_status == "Delivered" && $delivered_date === null) {
        $delivered_date = date("Y-m-d");
    }

    if ($delivery_status != "Delivered") {
        $delivered_date = null;
    }

    $update = pg_query_params(
        $conn,
        "UPDATE deliveries
         SET courier_name = $1,
             tracking_number = $2,
             delivery_status = $3,
             expected_date = $4,
             delivered_date = $5,
             delivery_address = $6,
             delivery_type = $7
         WHERE delivery_id = $8",
        array(
            $courier_name,
            $tracking_number,
            $delivery_status,
            $expected_date,
            $delivered_date,
            $delivery_address,
            $delivery_type,
            $delivery_id
        )
    );

    if ($update) {
        $_SESSION['success'] = "Delivery updated successfully.";
        header("Location: deliveries.php");
        exit();
    } else {
        $error = "Unable to update delivery: " . pg_last_error($conn);
    }
}
?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<h2 class="mb-4">
    <i class="bi bi-truck-front-fill"></i>
    Update Delivery
</h2>

<?php if(isset($error)) { ?>
<div class="alert alert-danger">
    <?= htmlspecialchars($error); ?>
</div>
<?php } ?>

<div class="card shadow">

<div class="card-header bg-warning text-dark">
    Delivery Details
</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Order</label>
    <input
        type="text"
        class="form-control"
        value="Order #<?= htmlspecialchars($delivery['order_id']); ?> - <?= htmlspecialchars($delivery['customer_name'] ?? 'Deleted Customer'); ?>"
        readonly>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Delivery Type</label>
    <select name="delivery_type" id="deliveryType" class="form-select" required>
        <option value="Home Delivery" <?= ($delivery['delivery_type'] == "Home Delivery") ? "selected" : ""; ?>>
            Home Delivery
        </option>

        <option value="Store Pickup" <?= ($delivery['delivery_type'] == "Store Pickup") ? "selected" : ""; ?>>
            Store Pickup
        </option>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Courier Name</label>
    <select name="courier_name" id="courierName" class="form-select" required>
        <?php
        $couriers = ["Blue Dart", "DTDC", "Delhivery", "India Post", "XpressBees", "Store Delivery"];
        foreach ($couriers as $courier) {
        ?>
            <option value="<?= $courier; ?>" <?= ($delivery['courier_name'] == $courier) ? "selected" : ""; ?>>
                <?= $courier; ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Tracking Number</label>
    <input
        type="text"
        name="tracking_number"
        id="trackingNumber"
        class="form-control"
        value="<?= htmlspecialchars($delivery['tracking_number']); ?>">
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Delivery Status</label>
    <select name="delivery_status" class="form-select" required>
        <?php
        $statuses = ["Processing", "Packed", "Shipped", "Out for Delivery", "Delivered"];
        foreach ($statuses as $status) {
        ?>
            <option value="<?= $status; ?>" <?= ($delivery['delivery_status'] == $status) ? "selected" : ""; ?>>
                <?= $status; ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Expected Delivery / Pickup Date</label>
    <input
        type="date"
        name="expected_date"
        class="form-control"
        value="<?= htmlspecialchars($delivery['expected_date']); ?>">
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Delivered Date</label>
    <input
        type="date"
        name="delivered_date"
        class="form-control"
        value="<?= htmlspecialchars($delivery['delivered_date']); ?>">
</div>

<div class="col-md-12 mb-3">
    <label class="form-label">Delivery / Pickup Address</label>
    <textarea
        name="delivery_address"
        id="deliveryAddress"
        class="form-control"
        rows="4"><?= htmlspecialchars($delivery['delivery_address']); ?></textarea>
</div>

<div class="col-md-12">
    <button class="btn btn-warning">
        <i class="bi bi-check-circle-fill"></i>
        Update Delivery
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
const deliveryType = document.getElementById("deliveryType");
const courierName = document.getElementById("courierName");
const trackingNumber = document.getElementById("trackingNumber");

function handleDeliveryType() {
    if (deliveryType.value === "Store Pickup") {
        courierName.value = "Store Delivery";
        trackingNumber.value = "Store Pickup";
        courierName.disabled = false;
        trackingNumber.readOnly = true;
    } else {
        if (courierName.value === "Store Delivery") {
            courierName.value = "Blue Dart";
        }

        if (trackingNumber.value === "Store Pickup") {
            trackingNumber.value = "";
        }

        trackingNumber.readOnly = false;
    }
}

deliveryType.addEventListener("change", handleDeliveryType);
handleDeliveryType();
</script>

<?php include("../includes/footer.php"); ?>
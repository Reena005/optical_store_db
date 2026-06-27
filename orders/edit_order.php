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

$orderResult = pg_query_params(
    $conn,
    "SELECT * FROM orders WHERE order_id = $1",
    array($order_id)
);

if (!$orderResult || pg_num_rows($orderResult) == 0) {
    $_SESSION['error'] = "Order not found.";
    header("Location: orders.php");
    exit();
}

$order = pg_fetch_assoc($orderResult);

$itemResult = pg_query_params(
    $conn,
    "SELECT * FROM order_items WHERE order_id = $1 LIMIT 1",
    array($order_id)
);

$item = pg_fetch_assoc($itemResult);

$packageResult = pg_query_params(
    $conn,
    "SELECT * FROM order_packages WHERE order_id = $1 LIMIT 1",
    array($order_id)
);

$package = ($packageResult && pg_num_rows($packageResult) > 0)
    ? pg_fetch_assoc($packageResult)
    : null;

$customers = pg_query(
    $conn,
    "SELECT customer_id, customer_name FROM customers ORDER BY customer_name"
);

$products = pg_query(
    $conn,
    "SELECT product_id, product_name, price, stock FROM products ORDER BY product_name"
);

$lenses = pg_query(
    $conn,
    "SELECT lens_id, lens_name, price FROM lens_types ORDER BY lens_name"
);

$coatings = pg_query(
    $conn,
    "SELECT coating_id, coating_name, price FROM lens_coatings ORDER BY coating_name"
);

$selected_lens = $package['lens_id'] ?? "";
$selected_coating = $package['coating_id'] ?? "";
$remarks = $package['remarks'] ?? "";

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<h2 class="mb-4">
    <i class="bi bi-pencil-square"></i>
    Edit Order
</h2>

<div class="card shadow">

<div class="card-header bg-warning text-dark">
    Update Order Information
</div>

<div class="card-body">

<form action="update_order.php" method="POST">

<input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']); ?>">
<input type="hidden" name="old_product_id" value="<?= htmlspecialchars($item['product_id']); ?>">
<input type="hidden" name="old_quantity" value="<?= htmlspecialchars($item['quantity']); ?>">

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Customer</label>

    <select name="customer_id" class="form-select" required>
        <option value="">Select Customer</option>

        <?php while($c = pg_fetch_assoc($customers)) { ?>
            <option value="<?= $c['customer_id']; ?>"
                <?= ($c['customer_id'] == $order['customer_id']) ? "selected" : ""; ?>>
                <?= htmlspecialchars($c['customer_name']); ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Payment Status</label>

    <select name="payment_status" class="form-select">
        <option value="Pending" <?= ($order['payment_status'] == "Pending") ? "selected" : ""; ?>>
            Pending
        </option>

        <option value="Paid" <?= ($order['payment_status'] == "Paid") ? "selected" : ""; ?>>
            Paid
        </option>
    </select>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Payment Mode</label>

    <select name="payment_mode" class="form-select">
        <option value="Cash" <?= ($order['payment_mode'] == "Cash") ? "selected" : ""; ?>>
            Cash
        </option>

        <option value="UPI" <?= ($order['payment_mode'] == "UPI") ? "selected" : ""; ?>>
            UPI
        </option>

        <option value="Card" <?= ($order['payment_mode'] == "Card") ? "selected" : ""; ?>>
            Card
        </option>
    </select>
</div>

<hr class="my-4">

<h4 class="mb-3">Frame / Product Details</h4>

<div class="col-md-6 mb-3">
    <label class="form-label">Frame / Product</label>

    <select name="product_id" id="product_id" class="form-select" required>
        <option value="">Select Product</option>

        <?php while($p = pg_fetch_assoc($products)) { ?>
            <option
                value="<?= $p['product_id']; ?>"
                data-price="<?= $p['price']; ?>"
                data-stock="<?= $p['stock']; ?>"
                <?= ($p['product_id'] == $item['product_id']) ? "selected" : ""; ?>
            >
                <?= htmlspecialchars($p['product_name']); ?>
                - ₹<?= $p['price']; ?>
                | Stock: <?= $p['stock']; ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Available Stock</label>
    <input type="text" id="stock" class="form-control" readonly>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Frame Price</label>
    <input
        type="text"
        id="price"
        name="price"
        class="form-control"
        value="<?= htmlspecialchars($item['price']); ?>"
        readonly>
</div>

<hr class="my-4">

<h4 class="mb-3">Lens Package</h4>

<div class="col-md-6 mb-3">
    <label class="form-label">Lens Type</label>

    <select name="lens_id" id="lens_id" class="form-select">
        <option value="" data-price="0">No Lens</option>

        <?php while($l = pg_fetch_assoc($lenses)) { ?>
            <option
                value="<?= $l['lens_id']; ?>"
                data-price="<?= $l['price']; ?>"
                <?= ($selected_lens == $l['lens_id']) ? "selected" : ""; ?>
            >
                <?= htmlspecialchars($l['lens_name']); ?>
                - ₹<?= $l['price']; ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Lens Coating</label>

    <select name="coating_id" id="coating_id" class="form-select">
        <option value="" data-price="0">No Coating</option>

        <?php while($co = pg_fetch_assoc($coatings)) { ?>
            <option
                value="<?= $co['coating_id']; ?>"
                data-price="<?= $co['price']; ?>"
                <?= ($selected_coating == $co['coating_id']) ? "selected" : ""; ?>
            >
                <?= htmlspecialchars($co['coating_name']); ?>
                - ₹<?= $co['price']; ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Quantity</label>
    <input
        type="number"
        name="quantity"
        id="quantity"
        class="form-control"
        value="<?= htmlspecialchars($item['quantity']); ?>"
        min="1"
        required>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Lens Price</label>
    <input type="text" id="lens_price" class="form-control" readonly>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Coating Price</label>
    <input type="text" id="coating_price" class="form-control" readonly>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Grand Total</label>
    <input
        type="text"
        id="total"
        class="form-control fw-bold bg-light"
        value="₹<?= htmlspecialchars($order['total_amount']); ?>"
        readonly>
</div>

<div class="col-md-12 mb-3">
    <label class="form-label">Remarks</label>
    <textarea
        name="remarks"
        class="form-control"
        rows="3"><?= htmlspecialchars($remarks); ?></textarea>
</div>

<div class="col-md-12 mt-4">
    <button type="submit" class="btn btn-warning btn-lg">
        <i class="bi bi-check-circle-fill"></i>
        Update Order
    </button>

    <a href="orders.php" class="btn btn-secondary btn-lg">
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
const product = document.getElementById("product_id");
const lens = document.getElementById("lens_id");
const coating = document.getElementById("coating_id");
const quantity = document.getElementById("quantity");

const priceBox = document.getElementById("price");
const stockBox = document.getElementById("stock");
const lensPriceBox = document.getElementById("lens_price");
const coatingPriceBox = document.getElementById("coating_price");
const totalBox = document.getElementById("total");

function selectedPrice(selectBox) {
    let selected = selectBox.options[selectBox.selectedIndex];
    return parseFloat(selected.getAttribute("data-price")) || 0;
}

function updateProductInfo() {
    let selected = product.options[product.selectedIndex];

    let price = parseFloat(selected.getAttribute("data-price")) || 0;
    let stock = parseInt(selected.getAttribute("data-stock")) || 0;

    priceBox.value = price.toFixed(2);
    stockBox.value = stock;

    calculateTotal();
}

function calculateTotal() {
    let framePrice = parseFloat(priceBox.value) || 0;
    let lensPrice = selectedPrice(lens);
    let coatingPrice = selectedPrice(coating);
    let qty = parseInt(quantity.value) || 1;

    let stock = parseInt(stockBox.value) || 0;

    if (qty > stock && stock > 0) {
        alert("Quantity exceeds available stock.");
        quantity.value = stock;
        qty = stock;
    }

    lensPriceBox.value = lensPrice.toFixed(2);
    coatingPriceBox.value = coatingPrice.toFixed(2);

    let total = (framePrice + lensPrice + coatingPrice) * qty;

    totalBox.value = "₹" + total.toFixed(2);
}

product.addEventListener("change", updateProductInfo);
lens.addEventListener("change", calculateTotal);
coating.addEventListener("change", calculateTotal);
quantity.addEventListener("input", calculateTotal);

updateProductInfo();
calculateTotal();
</script>

<?php include("../includes/footer.php"); ?>
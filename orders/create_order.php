<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$customers = pg_query(
    $conn,
    "SELECT customer_id, customer_name
     FROM customers
     ORDER BY customer_name"
);

$products = pg_query(
    $conn,
    "SELECT product_id, product_name, price, stock
     FROM products
     ORDER BY product_name"
);

$lenses = pg_query(
    $conn,
    "SELECT lens_id, lens_name, price
     FROM lens_types
     ORDER BY lens_name"
);

$coatings = pg_query(
    $conn,
    "SELECT coating_id, coating_name, price
     FROM lens_coatings
     ORDER BY coating_name"
);

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<h2 class="mb-4">
    <i class="bi bi-receipt"></i>
    Create New Order
</h2>

<div class="card shadow">

<div class="card-header bg-primary text-white">
    Order Information
</div>

<div class="card-body">

<form action="order_process.php" method="POST">

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Customer</label>

    <select name="customer_id" class="form-select" required>
        <option value="">Select Customer</option>

        <?php while($c = pg_fetch_assoc($customers)) { ?>
            <option value="<?= $c['customer_id']; ?>">
                <?= htmlspecialchars($c['customer_name']); ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Payment Status</label>

    <select name="payment_status" class="form-select">
        <option value="Pending">Pending</option>
        <option value="Paid">Paid</option>
    </select>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Payment Mode</label>

    <select name="payment_mode" class="form-select">
        <option>Cash</option>
        <option>UPI</option>
        <option>Card</option>
    </select>
</div>

<hr class="my-4">

<h4 class="mb-3">
    Frame / Product
</h4>

<div class="col-md-6 mb-3">
    <label class="form-label">Frame / Product</label>

    <select name="product_id" id="product_id" class="form-select" required>
        <option value="">Select Frame / Product</option>

        <?php while($p = pg_fetch_assoc($products)) { ?>
            <option
                value="<?= $p['product_id']; ?>"
                data-price="<?= $p['price']; ?>"
                data-stock="<?= $p['stock']; ?>"
            >
                <?= htmlspecialchars($p['product_name']); ?>
                (₹<?= $p['price']; ?>)
                | Stock: <?= $p['stock']; ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Available Stock</label>

    <input
        type="text"
        id="stock"
        class="form-control"
        readonly>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Frame Price</label>

    <input
        type="text"
        id="price"
        class="form-control"
        readonly>
</div>

<hr class="my-4">

<h4 class="mb-3">
    Lens Package
</h4>

<div class="col-md-6 mb-3">
    <label class="form-label">Lens Type</label>

    <select name="lens_id" id="lens_id" class="form-select">
        <option value="" data-price="0">No Lens</option>

        <?php while($l = pg_fetch_assoc($lenses)) { ?>
            <option
                value="<?= $l['lens_id']; ?>"
                data-price="<?= $l['price']; ?>"
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
        min="1"
        value="1"
        required>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Lens Price</label>

    <input
        type="text"
        id="lens_price"
        class="form-control"
        readonly>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Coating Price</label>

    <input
        type="text"
        id="coating_price"
        class="form-control"
        readonly>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Grand Total</label>

    <input
        type="text"
        id="total"
        class="form-control fw-bold bg-light"
        readonly>
</div>

<div class="col-md-12 mb-3">
    <label class="form-label">Remarks</label>

    <textarea
        name="remarks"
        class="form-control"
        rows="3"
        placeholder="Example: Use customer prescription, blue cut lens, urgent delivery"></textarea>
</div>

<div class="col-md-12 mt-4">
    <button
        type="submit"
        class="btn btn-success btn-lg">

        <i class="bi bi-check-circle-fill"></i>
        Place Order
    </button>

    <a
        href="orders.php"
        class="btn btn-secondary btn-lg">

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
const qty = document.getElementById("quantity");

const priceBox = document.getElementById("price");
const stockBox = document.getElementById("stock");
const lensPriceBox = document.getElementById("lens_price");
const coatingPriceBox = document.getElementById("coating_price");
const totalBox = document.getElementById("total");

function getSelectedPrice(selectBox) {
    let selected = selectBox.options[selectBox.selectedIndex];
    return parseFloat(selected.getAttribute("data-price")) || 0;
}

function calculateTotal() {
    let framePrice = getSelectedPrice(product);
    let lensPrice = getSelectedPrice(lens);
    let coatingPrice = getSelectedPrice(coating);
    let quantity = parseInt(qty.value) || 1;

    let stock = parseInt(stockBox.value) || 0;

    if (quantity > stock && stock > 0) {
        alert("Quantity exceeds available stock.");
        qty.value = stock;
        quantity = stock;
    }

    priceBox.value = framePrice.toFixed(2);
    lensPriceBox.value = lensPrice.toFixed(2);
    coatingPriceBox.value = coatingPrice.toFixed(2);

    let total = (framePrice + lensPrice + coatingPrice) * quantity;

    totalBox.value = "₹" + total.toFixed(2);
}

product.addEventListener("change", function() {
    let selected = this.options[this.selectedIndex];

    let stock = selected.getAttribute("data-stock") || 0;

    stockBox.value = stock;

    calculateTotal();
});

lens.addEventListener("change", calculateTotal);
coating.addEventListener("change", calculateTotal);
qty.addEventListener("input", calculateTotal);

calculateTotal();
</script>

<?php include("../includes/footer.php"); ?>
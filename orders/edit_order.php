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

$customers = pg_query(
    $conn,
    "SELECT customer_id, customer_name FROM customers ORDER BY customer_name"
);

$products = pg_query(
    $conn,
    "SELECT product_id, product_name, price, stock FROM products ORDER BY product_name"
);

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

<h4>Product Details</h4>

<div class="col-md-6 mb-3">
    <label class="form-label">Product</label>

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
    <label class="form-label">Price</label>
    <input
        type="text"
        id="price"
        name="price"
        class="form-control"
        value="<?= htmlspecialchars($item['price']); ?>"
        readonly
    >
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
        required
    >
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Available Stock</label>
    <input
        type="text"
        id="stock"
        class="form-control"
        readonly
    >
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">Total Amount</label>
    <input
        type="text"
        id="total"
        class="form-control fw-bold"
        value="₹<?= htmlspecialchars($order['total_amount']); ?>"
        readonly
    >
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
const quantity = document.getElementById("quantity");
const priceBox = document.getElementById("price");
const stockBox = document.getElementById("stock");
const totalBox = document.getElementById("total");

function updateProductInfo() {
    let selected = product.options[product.selectedIndex];

    let price = parseFloat(selected.getAttribute("data-price")) || 0;
    let stock = parseInt(selected.getAttribute("data-stock")) || 0;

    priceBox.value = price.toFixed(2);
    stockBox.value = stock;

    calculateTotal();
}

function calculateTotal() {
    let price = parseFloat(priceBox.value) || 0;
    let qty = parseInt(quantity.value) || 0;

    totalBox.value = "₹" + (price * qty).toFixed(2);
}

product.addEventListener("change", updateProductInfo);
quantity.addEventListener("input", calculateTotal);

updateProductInfo();
</script>

<?php include("../includes/footer.php"); ?>
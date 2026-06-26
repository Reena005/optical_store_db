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

<select
name="customer_id"
class="form-select"
required>

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

<select
name="payment_status"
class="form-select">

<option value="Pending">Pending</option>
<option value="Paid">Paid</option>

</select>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">Payment Mode</label>

<select
name="payment_mode"
class="form-select">

<option>Cash</option>
<option>UPI</option>
<option>Card</option>

</select>

</div>

<hr class="my-4">

<h4 class="mb-3">

Products

</h4>

<div class="col-md-6 mb-3">

<label class="form-label">

Product

</label>

<select
name="product_id"
id="product_id"
class="form-select"
required>

<option value="">Select Product</option>

<?php while($p = pg_fetch_assoc($products)) { ?>

<option

value="<?= $p['product_id']; ?>"

data-price="<?= $p['price']; ?>"

data-stock="<?= $p['stock']; ?>">

<?= htmlspecialchars($p['product_name']); ?>

(₹<?= $p['price']; ?>)

| Stock: <?= $p['stock']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Available Stock

</label>

<input
type="text"
id="stock"
class="form-control"
readonly>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Price

</label>

<input
type="text"
id="price"
class="form-control"
readonly>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Quantity

</label>

<input
type="number"
name="quantity"
id="quantity"
class="form-control"
min="1"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Total Amount

</label>

<input
type="text"
id="total"
class="form-control fw-bold bg-light"
readonly>

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

const product=document.getElementById("product_id");
const qty=document.getElementById("quantity");

product.addEventListener("change",function(){

let selected=this.options[this.selectedIndex];

let price=selected.getAttribute("data-price") || 0;
let stock=selected.getAttribute("data-stock") || 0;

document.getElementById("price").value=price;
document.getElementById("stock").value=stock;

qty.value="";
document.getElementById("total").value="";

});

qty.addEventListener("input",function(){

let price=parseFloat(document.getElementById("price").value)||0;

let stock=parseInt(document.getElementById("stock").value)||0;

let quantity=parseInt(this.value)||0;

if(quantity>stock)
{
alert("Quantity exceeds available stock.");

this.value=stock;

quantity=stock;
}

document.getElementById("total").value="₹"+(price*quantity).toFixed(2);

});

</script>

<?php include("../includes/footer.php"); ?>
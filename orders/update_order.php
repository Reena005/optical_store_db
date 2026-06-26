<?php

require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit();
}

$order_id = $_POST['order_id'];
$customer_id = $_POST['customer_id'];
$new_product_id = $_POST['product_id'];
$new_qty = (int)$_POST['quantity'];
$old_product_id = $_POST['old_product_id'];
$old_qty = (int)$_POST['old_quantity'];
$status = $_POST['payment_status'];
$payment_mode = $_POST['payment_mode'];

if (empty($order_id) || empty($customer_id) || empty($new_product_id) || $new_qty <= 0) {
    $_SESSION['error'] = "Invalid order details.";
    header("Location: edit_order.php?id=" . urlencode($order_id));
    exit();
}

/* Restore old product stock */
pg_query_params(
    $conn,
    "UPDATE products SET stock = stock + $1 WHERE product_id = $2",
    array($old_qty, $old_product_id)
);

/* Get new product price and stock */
$productResult = pg_query_params(
    $conn,
    "SELECT price, stock FROM products WHERE product_id = $1",
    array($new_product_id)
);

if (!$productResult || pg_num_rows($productResult) == 0) {
    $_SESSION['error'] = "Selected product not found.";
    header("Location: edit_order.php?id=" . urlencode($order_id));
    exit();
}

$product = pg_fetch_assoc($productResult);
$price = $product['price'];
$available_stock = (int)$product['stock'];

if ($new_qty > $available_stock) {
    /* Rollback old stock restore manually */
    pg_query_params(
        $conn,
        "UPDATE products SET stock = stock - $1 WHERE product_id = $2",
        array($old_qty, $old_product_id)
    );

    $_SESSION['error'] = "Insufficient stock. Available stock: " . $available_stock;
    header("Location: edit_order.php?id=" . urlencode($order_id));
    exit();
}

$total = $price * $new_qty;

/* Update order */
pg_query_params(
    $conn,
    "UPDATE orders
     SET customer_id = $1,
         total_amount = $2,
         payment_status = $3,
         payment_mode = $4
     WHERE order_id = $5",
    array($customer_id, $total, $status, $payment_mode, $order_id)
);

/* Update order item */
pg_query_params(
    $conn,
    "UPDATE order_items
     SET product_id = $1,
         quantity = $2,
         price = $3,
         subtotal = $4
     WHERE order_id = $5",
    array($new_product_id, $new_qty, $price, $total, $order_id)
);

/* Deduct new stock */
pg_query_params(
    $conn,
    "UPDATE products SET stock = stock - $1 WHERE product_id = $2",
    array($new_qty, $new_product_id)
);

/* Update payment */
pg_query_params(
    $conn,
    "DELETE FROM payments WHERE order_id = $1",
    array($order_id)
);

if ($status == "Paid") {
    pg_query_params(
        $conn,
        "INSERT INTO payments(order_id, payment_mode, amount)
         VALUES($1, $2, $3)",
        array($order_id, $payment_mode, $total)
    );
}

$_SESSION['success'] = "Order updated successfully.";

header("Location: orders.php");
exit();

?>
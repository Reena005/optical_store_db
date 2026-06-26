<?php

require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$customer = $_POST['customer_id'];
$product = $_POST['product_id'];
$qty = $_POST['quantity'];
$status = $_POST['payment_status'];
$payment_mode = $_POST['payment_mode'];

$productQuery = pg_query_params(
    $conn,
    "SELECT price, stock FROM products WHERE product_id = $1",
    array($product)
);

$productData = pg_fetch_assoc($productQuery);

$price = $productData['price'];
$stock = $productData['stock'];

if ($qty > $stock) {
    $_SESSION['error'] = "Insufficient Stock";
    header("Location: create_order.php");
    exit();
}

$total = $price * $qty;

$orderInsert = pg_query_params(
    $conn,
    "INSERT INTO orders (
        customer_id,
        total_amount,
        payment_status,
        payment_mode
    )
    VALUES ($1, $2, $3, $4)
    RETURNING order_id",
    array(
        $customer,
        $total,
        $status,
        $payment_mode
    )
);

$order = pg_fetch_assoc($orderInsert);
$order_id = $order['order_id'];

pg_query_params(
    $conn,
    "INSERT INTO order_items (
        order_id,
        product_id,
        quantity,
        price,
        subtotal
    )
    VALUES ($1, $2, $3, $4, $5)",
    array(
        $order_id,
        $product,
        $qty,
        $price,
        $total
    )
);

$newStock = $stock - $qty;

pg_query_params(
    $conn,
    "UPDATE products
     SET stock = $1
     WHERE product_id = $2",
    array(
        $newStock,
        $product
    )
);

if ($status == "Paid") {
    pg_query_params(
        $conn,
        "INSERT INTO payments (
            order_id,
            payment_mode,
            amount
        )
        VALUES ($1, $2, $3)",
        array(
            $order_id,
            $payment_mode,
            $total
        )
    );
}

$_SESSION['success'] = "Order Created Successfully";

header("Location: orders.php");
exit();

?>
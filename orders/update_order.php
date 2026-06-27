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

$lens_id = !empty($_POST['lens_id']) ? $_POST['lens_id'] : null;
$coating_id = !empty($_POST['coating_id']) ? $_POST['coating_id'] : null;
$remarks = trim($_POST['remarks'] ?? '');

if (empty($order_id) || empty($customer_id) || empty($new_product_id) || $new_qty <= 0) {
    $_SESSION['error'] = "Invalid order details.";
    header("Location: edit_order.php?id=" . urlencode($order_id));
    exit();
}

/* Restore old product stock */
pg_query_params(
    $conn,
    "UPDATE products
     SET stock = stock + $1
     WHERE product_id = $2",
    array($old_qty, $old_product_id)
);

/* Get frame/product price and stock */
$productResult = pg_query_params(
    $conn,
    "SELECT price, stock
     FROM products
     WHERE product_id = $1",
    array($new_product_id)
);

if (!$productResult || pg_num_rows($productResult) == 0) {
    $_SESSION['error'] = "Selected product not found.";
    header("Location: edit_order.php?id=" . urlencode($order_id));
    exit();
}

$product = pg_fetch_assoc($productResult);

$frame_price = (float)$product['price'];
$available_stock = (int)$product['stock'];

if ($new_qty > $available_stock) {

    /* Rollback restored stock */
    pg_query_params(
        $conn,
        "UPDATE products
         SET stock = stock - $1
         WHERE product_id = $2",
        array($old_qty, $old_product_id)
    );

    $_SESSION['error'] = "Insufficient stock. Available stock: " . $available_stock;
    header("Location: edit_order.php?id=" . urlencode($order_id));
    exit();
}

/* Lens price */
$lens_price = 0;

if ($lens_id !== null) {
    $lensResult = pg_query_params(
        $conn,
        "SELECT price FROM lens_types WHERE lens_id = $1",
        array($lens_id)
    );

    if ($lensResult && pg_num_rows($lensResult) > 0) {
        $lens = pg_fetch_assoc($lensResult);
        $lens_price = (float)$lens['price'];
    }
}

/* Coating price */
$coating_price = 0;

if ($coating_id !== null) {
    $coatingResult = pg_query_params(
        $conn,
        "SELECT price FROM lens_coatings WHERE coating_id = $1",
        array($coating_id)
    );

    if ($coatingResult && pg_num_rows($coatingResult) > 0) {
        $coating = pg_fetch_assoc($coatingResult);
        $coating_price = (float)$coating['price'];
    }
}

/* Grand total */
$frame_subtotal = $frame_price * $new_qty;

$total = ($frame_price + $lens_price + $coating_price) * $new_qty;

/* Update orders */
$orderUpdate = pg_query_params(
    $conn,
    "UPDATE orders
     SET customer_id = $1,
         total_amount = $2,
         payment_status = $3,
         payment_mode = $4
     WHERE order_id = $5",
    array(
        $customer_id,
        $total,
        $status,
        $payment_mode,
        $order_id
    )
);

if (!$orderUpdate) {
    $_SESSION['error'] = "Unable to update order: " . pg_last_error($conn);
    header("Location: edit_order.php?id=" . urlencode($order_id));
    exit();
}

/* Update order_items - frame only */
$itemUpdate = pg_query_params(
    $conn,
    "UPDATE order_items
     SET product_id = $1,
         quantity = $2,
         price = $3,
         subtotal = $4
     WHERE order_id = $5",
    array(
        $new_product_id,
        $new_qty,
        $frame_price,
        $frame_subtotal,
        $order_id
    )
);

if (!$itemUpdate) {
    $_SESSION['error'] = "Unable to update order item: " . pg_last_error($conn);
    header("Location: edit_order.php?id=" . urlencode($order_id));
    exit();
}

/* Deduct new stock */
pg_query_params(
    $conn,
    "UPDATE products
     SET stock = stock - $1
     WHERE product_id = $2",
    array($new_qty, $new_product_id)
);

/* Update or insert order package */
$packageCheck = pg_query_params(
    $conn,
    "SELECT package_id
     FROM order_packages
     WHERE order_id = $1",
    array($order_id)
);

if ($packageCheck && pg_num_rows($packageCheck) > 0) {

    pg_query_params(
        $conn,
        "UPDATE order_packages
         SET frame_product_id = $1,
             lens_id = $2,
             coating_id = $3,
             package_price = $4,
             remarks = $5
         WHERE order_id = $6",
        array(
            $new_product_id,
            $lens_id,
            $coating_id,
            $total,
            $remarks,
            $order_id
        )
    );

} else {

    pg_query_params(
        $conn,
        "INSERT INTO order_packages
         (order_id, frame_product_id, lens_id, coating_id, package_price, remarks)
         VALUES($1,$2,$3,$4,$5,$6)",
        array(
            $order_id,
            $new_product_id,
            $lens_id,
            $coating_id,
            $total,
            $remarks
        )
    );
}

/* Update payment */
pg_query_params(
    $conn,
    "DELETE FROM payments
     WHERE order_id = $1",
    array($order_id)
);

if ($status == "Paid") {
    pg_query_params(
        $conn,
        "INSERT INTO payments(order_id, payment_mode, amount)
         VALUES($1, $2, $3)",
        array(
            $order_id,
            $payment_mode,
            $total
        )
    );
}

$_SESSION['success'] = "Order updated successfully.";

header("Location: orders.php");
exit();

?>
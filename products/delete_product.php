<?php
require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid product ID.";
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];

$getImage = pg_query_params(
    $conn,
    "SELECT image FROM products WHERE product_id = $1",
    array($product_id)
);

$product = pg_fetch_assoc($getImage);
$image = $product['image'] ?? "";

$result = pg_query_params(
    $conn,
    "DELETE FROM products WHERE product_id = $1",
    array($product_id)
);

if ($result) {
    if (!empty($image) && file_exists("../uploads/products/" . $image)) {
        unlink("../uploads/products/" . $image);
    }

    $_SESSION['success'] = "Product deleted successfully.";
} else {
    $_SESSION['error'] = "Unable to delete product.";
}

header("Location: products.php");
exit();
?>
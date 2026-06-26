<?php
require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: products.php");
    exit();
}

$product_id = $_POST['product_id'];
$name = trim($_POST['product_name']);
$brand = trim($_POST['brand']);
$category_id = $_POST['category_id'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$description = trim($_POST['description']);
$old_image = $_POST['old_image'];

if (empty($product_id) || empty($name) || $price === "" || $stock === "") {
    $_SESSION['error'] = "Product name, price, and stock are required.";
    header("Location: edit_product.php?id=" . urlencode($product_id));
    exit();
}

$image = $old_image;

if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $file_name = $_FILES['image']['name'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        $_SESSION['error'] = "Only JPG, JPEG, PNG, and WEBP images are allowed.";
        header("Location: edit_product.php?id=" . urlencode($product_id));
        exit();
    }

    $image = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $file_name);
    $upload_path = "../uploads/products/" . $image;

    if (move_uploaded_file($file_tmp, $upload_path)) {
        if (!empty($old_image) && file_exists("../uploads/products/" . $old_image)) {
            unlink("../uploads/products/" . $old_image);
        }
    } else {
        $_SESSION['error'] = "Image upload failed.";
        header("Location: edit_product.php?id=" . urlencode($product_id));
        exit();
    }
}

$query = "UPDATE products
          SET product_name = $1,
              brand = $2,
              category_id = $3,
              price = $4,
              stock = $5,
              image = $6,
              description = $7
          WHERE product_id = $8";

$result = pg_query_params(
    $conn,
    $query,
    array($name, $brand, $category_id, $price, $stock, $image, $description, $product_id)
);

if ($result) {
    $_SESSION['success'] = "Product updated successfully.";
} else {
    $_SESSION['error'] = "Unable to update product.";
}

header("Location: products.php");
exit();
?>
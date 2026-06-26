<?php
require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid product ID.";
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];

$result = pg_query_params(
    $conn,
    "SELECT * FROM products WHERE product_id = $1",
    array($product_id)
);

if (!$result || pg_num_rows($result) == 0) {
    $_SESSION['error'] = "Product not found.";
    header("Location: products.php");
    exit();
}

$product = pg_fetch_assoc($result);
$categories = pg_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");
?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">
<div class="container-fluid">

<h2 class="mb-4">
    <i class="bi bi-pencil-square"></i>
    Edit Product
</h2>

<div class="card shadow">
<div class="card-header bg-warning">
    <strong>Update Product Details</strong>
</div>

<div class="card-body">

<form action="update_product.php" method="POST" enctype="multipart/form-data">

<input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
<input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']); ?>">

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Product Name</label>
    <input type="text" name="product_name" class="form-control"
           value="<?= htmlspecialchars($product['product_name']); ?>" required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Brand</label>
    <input type="text" name="brand" class="form-control"
           value="<?= htmlspecialchars($product['brand']); ?>">
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Category</label>
    <select name="category_id" class="form-select">
        <?php while ($cat = pg_fetch_assoc($categories)) { ?>
            <option value="<?= $cat['category_id']; ?>"
                <?= ($cat['category_id'] == $product['category_id']) ? "selected" : ""; ?>>
                <?= htmlspecialchars($cat['category_name']); ?>
            </option>
        <?php } ?>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Price</label>
    <input type="number" step="0.01" name="price" class="form-control"
           value="<?= htmlspecialchars($product['price']); ?>" required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Stock</label>
    <input type="number" name="stock" class="form-control"
           value="<?= htmlspecialchars($product['stock']); ?>" required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Change Product Image</label>
    <input type="file" name="image" class="form-control">
</div>

<?php if (!empty($product['image'])) { ?>
<div class="col-md-12 mb-3">
    <label class="form-label">Current Image</label><br>
    <img src="../uploads/products/<?= htmlspecialchars($product['image']); ?>" width="120">
</div>
<?php } ?>

<div class="col-md-12 mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']); ?></textarea>
</div>

<div class="col-md-12">
    <button class="btn btn-warning" type="submit">
        <i class="bi bi-check-circle-fill"></i>
        Update Product
    </button>

    <a href="products.php" class="btn btn-secondary">Cancel</a>
</div>

</div>

</form>

</div>
</div>

</div>
</div>
</div>

<?php include("../includes/footer.php"); ?>
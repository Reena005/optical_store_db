<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$search="";

if(isset($_GET['search']))
{
    $search=trim($_GET['search']);

    $query="
    SELECT p.*,c.category_name
    FROM products p
    LEFT JOIN categories c
    ON p.category_id=c.category_id
    WHERE
    p.product_name ILIKE $1
    OR p.brand ILIKE $1
    ORDER BY product_id DESC";

    $result=pg_query_params($conn,$query,array("%".$search."%"));
}
else
{
    $query="
    SELECT p.*,c.category_name
    FROM products p
    LEFT JOIN categories c
    ON p.category_id=c.category_id
    ORDER BY product_id DESC";

    $result=pg_query($conn,$query);
}

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>

<i class="bi bi-eyeglasses"></i>

Products

</h2>
<div>
<a
href="add_product.php"
class="btn btn-primary">

Add Product
</a>

<a href="export_products_pdf.php" class="btn btn-danger">
    <i class="bi bi-file-earmark-pdf-fill"></i>
    Export PDF
</a>

<a href="export_products_excel.php" class="btn btn-success">
    <i class="bi bi-file-earmark-excel-fill"></i>
    Export Excel
</a>

</div>
</div>

<form method="GET">

<div class="row mb-4">

<div class="col-md-4">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Product..."
value="<?= htmlspecialchars($search) ?>">

</div>

<div class="col-md-2">

<button class="btn btn-success">

Search

</button>

</div>

</div>

</form>

<div class="card shadow">

<div class="card-body">
    <?php if(isset($_SESSION['success'])) { ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php } ?>

<?php if(isset($_SESSION['error'])) { ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php } ?>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Image</th>

<th>Name</th>

<th>Brand</th>

<th>Category</th>

<th>Price</th>

<th>Stock</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

while($row=pg_fetch_assoc($result))
{

?>

<tr>

<td>

<?php

if($row['image']!="")
{

?>

<img
src="../uploads/products/<?= $row['image']; ?>"
width="70">

<?php

}
else

{

echo "No Image";

}

?>

</td>

<td>

<?= htmlspecialchars($row['product_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['brand']); ?>

</td>

<td>

<?= htmlspecialchars($row['category_name']); ?>

</td>

<td>

₹<?= htmlspecialchars($row['price']); ?>

</td>

<td>

<?php

if($row['stock']<10)

{

?>

<span class="badge bg-danger">

Low Stock

</span>

<?php

}

else

{

?>

<span class="badge bg-success">

<?= $row['stock']; ?>

</span>

<?php

}

?>

</td>

<td>

<a
href="edit_product.php?id=<?= $row['product_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-fill"></i>

</a>

<a
href="delete_product.php?id=<?= $row['product_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete Product?')">

<i class="bi bi-trash-fill"></i>

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>
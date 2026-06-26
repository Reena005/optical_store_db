<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$search = "";

if(isset($_GET['search']))
{
    $search = trim($_GET['search']);

    $result = pg_query_params(
        $conn,
        "SELECT * FROM customers
         WHERE customer_name ILIKE $1
         ORDER BY customer_id ASC",
        array("%".$search."%")
    );
}
else
{
    $result = pg_query(
        $conn,
        "SELECT * FROM customers
         ORDER BY customer_id ASC"
    );
}

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>
    <i class="bi bi-people-fill"></i>
    Customers
</h2>
<div>
<a href="add_customer.php" class="btn btn-primary">
    <i class="bi bi-person-plus-fill"></i>
    Add Customer
</a>
<a href="export_customers_pdf.php" class="btn btn-danger">
    <i class="bi bi-file-earmark-pdf-fill"></i>
    Export PDF
</a>

<a href="export_customers_excel.php" class="btn btn-success">
    <i class="bi bi-file-earmark-excel-fill"></i>
    Export Excel
</a>
</div>
</div>
<form method="GET" class="row mb-4">

<div class="col-md-4">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Customer..."
value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<button class="btn btn-success">

Search

</button>

</div>

</form>

<div class="card shadow">

<div class="card-body">
<?php if(isset($_SESSION['success'])) { ?>

<div class="alert alert-success alert-dismissible fade show">
    <?php 
    echo $_SESSION['success']; 
    unset($_SESSION['success']); 
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php } ?>

<?php if(isset($_SESSION['error'])) { ?>

<div class="alert alert-danger alert-dismissible fade show">
    <?php 
    echo $_SESSION['error']; 
    unset($_SESSION['error']); 
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php } ?>
<table class="table table-hover table-bordered">

<thead class="table-primary">

<tr>

<th>ID</th>

<th>Name</th>

<th>Phone</th>

<th>Email</th>

<th>Gender</th>

<th>Age</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(pg_num_rows($result)>0)
{

while($row=pg_fetch_assoc($result))
{

?>

<tr>

<td><?= $row['customer_id']; ?></td>

<td><?= htmlspecialchars($row['customer_name']); ?></td>

<td><?= htmlspecialchars($row['phone']); ?></td>

<td><?= htmlspecialchars($row['email']); ?></td>

<td><?= htmlspecialchars($row['gender']); ?></td>

<td><?= htmlspecialchars($row['age']); ?></td>

<td>

<a
href="edit_customer.php?id=<?= $row['customer_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-fill"></i>

</a>

<a
href="delete_customer.php?id=<?= $row['customer_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this customer?')">


<i class="bi bi-trash-fill"></i>

</a>

</td>

</tr>

<?php

}

}

else

{

?>

<tr>

<td colspan="7" class="text-center">

No Customers Found

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
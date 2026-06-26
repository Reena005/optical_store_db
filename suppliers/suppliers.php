<?php

require("../includes/auth_check.php");
require("../config/database.php");

include("../includes/header.php");
include("../includes/navbar.php");

$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);

    $query = "
        SELECT *
        FROM suppliers
        WHERE supplier_name ILIKE $1
           OR contact_person ILIKE $1
           OR phone ILIKE $1
           OR email ILIKE $1
        ORDER BY supplier_id DESC
    ";

    $result = pg_query_params($conn, $query, array("%" . $search . "%"));
} else {
    $result = pg_query(
        $conn,
        "SELECT * FROM suppliers ORDER BY supplier_id ASC"
    );
}

?>

<div class="d-flex">

<?php include("../includes/sidebar.php"); ?>

<div class="content flex-grow-1">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>
        <i class="bi bi-truck"></i>
        Suppliers
    </h2>

    <div>
        <a href="add_suppliers.php" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill"></i>
            Add Supplier
        </a>

        <a href="export_supplier_pdf.php" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            Export PDF
        </a>

        <a href="export_supplier_excel.php" class="btn btn-success">
            <i class="bi bi-file-earmark-excel-fill"></i>
            Export Excel
        </a>
    </div>

</div>

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

<form method="GET" class="mb-4">
    <div class="row">
        <div class="col-md-4">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search supplier..."
                value="<?= htmlspecialchars($search); ?>"
            >
        </div>

        <div class="col-md-2">
            <button class="btn btn-success">
                <i class="bi bi-search"></i>
                Search
            </button>
        </div>
    </div>
</form>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>
    <th>ID</th>
    <th>Supplier Name</th>
    <th>Contact Person</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Address</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php if ($result && pg_num_rows($result) > 0) { ?>

<?php while ($row = pg_fetch_assoc($result)) { ?>

<tr>
    <td><?= htmlspecialchars($row['supplier_id']); ?></td>

    <td><?= htmlspecialchars($row['supplier_name']); ?></td>

    <td><?= htmlspecialchars($row['contact_person']); ?></td>

    <td><?= htmlspecialchars($row['phone']); ?></td>

    <td><?= htmlspecialchars($row['email']); ?></td>

    <td><?= htmlspecialchars($row['address']); ?></td>

    <td>
        <a
            href="edit_supplier.php?id=<?= $row['supplier_id']; ?>"
            class="btn btn-warning btn-sm"
        >
            <i class="bi bi-pencil-fill"></i>
        </a>

        <a
            href="delete_supplier.php?id=<?= $row['supplier_id']; ?>"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Are you sure you want to delete this supplier?');"
        >
            <i class="bi bi-trash-fill"></i>
        </a>
    </td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="7" class="text-center text-muted">
        No Suppliers Found
    </td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php"); ?>
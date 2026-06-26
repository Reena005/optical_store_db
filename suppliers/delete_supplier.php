<?php

require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid supplier ID.";
    header("Location: suppliers.php");
    exit();
}

$supplier_id = $_GET['id'];

$result = pg_query_params(
    $conn,
    "DELETE FROM suppliers WHERE supplier_id = $1",
    array($supplier_id)
);

if ($result) {
    $_SESSION['success'] = "Supplier deleted successfully.";
} else {
    $_SESSION['error'] = "Unable to delete supplier.";
}

header("Location: suppliers.php");
exit();

?>
<?php

require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid customer ID.";
    header("Location: customers.php");
    exit();
}

$customer_id = $_GET['id'];

$query = "DELETE FROM customers WHERE customer_id = $1";

$result = pg_query_params($conn, $query, array($customer_id));

if ($result) {
    $_SESSION['success'] = "Customer deleted successfully.";
} else {
    $_SESSION['error'] = "Unable to delete customer.";
}

header("Location: customers.php");
exit();

?>
<?php
require("../includes/auth_check.php");
require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'];
$customer_id = $_GET['customer_id'];

$result = pg_query_params(
    $conn,
    "DELETE FROM prescriptions WHERE prescription_id=$1",
    array($id)
);

if($result) {
    $_SESSION['success'] = "Prescription deleted successfully.";
} else {
    $_SESSION['error'] = "Unable to delete prescription.";
}

header("Location: history.php?customer_id=" . $customer_id);
exit();
?>
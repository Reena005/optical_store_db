<?php

require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: suppliers.php");
    exit();
}

$supplier_id = $_POST['supplier_id'];
$name = trim($_POST['supplier_name']);
$contact = trim($_POST['contact_person']);
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);
$address = trim($_POST['address']);

if (empty($supplier_id) || empty($name) || empty($phone)) {
    $_SESSION['error'] = "Supplier name and phone are required.";
    header("Location: edit_supplier.php?id=" . urlencode($supplier_id));
    exit();
}

$check = pg_query_params(
    $conn,
    "SELECT supplier_id FROM suppliers
     WHERE phone = $1 AND supplier_id != $2",
    array($phone, $supplier_id)
);

if ($check && pg_num_rows($check) > 0) {
    $_SESSION['error'] = "Phone number already belongs to another supplier.";
    header("Location: edit_supplier.php?id=" . urlencode($supplier_id));
    exit();
}

$query = "UPDATE suppliers
          SET supplier_name = $1,
              contact_person = $2,
              phone = $3,
              email = $4,
              address = $5
          WHERE supplier_id = $6";

$result = pg_query_params(
    $conn,
    $query,
    array($name, $contact, $phone, $email, $address, $supplier_id)
);

if ($result) {
    $_SESSION['success'] = "Supplier updated successfully.";
} else {
    $_SESSION['error'] = "Unable to update supplier.";
}

header("Location: suppliers.php");
exit();

?>
<?php

require("../config/database.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $customer_id = $_POST['customer_id'];
    $name = trim($_POST['customer_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);

    if (empty($customer_id) || empty($name) || empty($phone)) {
        $_SESSION['error'] = "Customer name and phone are required.";
        header("Location: customers.php");
        exit();
    }

    $check = pg_query_params(
        $conn,
        "SELECT customer_id FROM customers 
         WHERE phone = $1 AND customer_id != $2",
        array($phone, $customer_id)
    );

    if ($check && pg_num_rows($check) > 0) {
        $_SESSION['error'] = "Phone number already belongs to another customer.";
        header("Location: edit_customer.php?id=" . urlencode($customer_id));
        exit();
    }

    $query = "UPDATE customers
              SET customer_name = $1,
                  phone = $2,
                  email = $3,
                  age = $4,
                  gender = $5,
                  address = $6
              WHERE customer_id = $7";

    $result = pg_query_params(
        $conn,
        $query,
        array(
            $name,
            $phone,
            $email,
            $age,
            $gender,
            $address,
            $customer_id
        )
    );

    if ($result) {
        $_SESSION['success'] = "Customer updated successfully.";
    } else {
        $_SESSION['error'] = "Unable to update customer.";
    }

    header("Location: customers.php");
    exit();
}

header("Location: customers.php");
exit();

?>
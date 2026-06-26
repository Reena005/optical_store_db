<?php
require("../config/database.php");

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$order_id = $_POST['order_id'];
$courier_name = $_POST['courier_name'];
$tracking_number = trim($_POST['tracking_number']);
$delivery_status = $_POST['delivery_status'];
$expected_date = $_POST['expected_date'];
$delivery_address = trim($_POST['delivery_address']);

$result = pg_query_params(
    $conn,
    "INSERT INTO deliveries
    (order_id, courier_name, tracking_number, delivery_status, expected_date, delivery_address)
    VALUES($1,$2,$3,$4,$5,$6)",
    array($order_id, $courier_name, $tracking_number, $delivery_status, $expected_date, $delivery_address)
);

if($result){
    $_SESSION['success'] = "Delivery added successfully.";
}else{
    $_SESSION['error'] = "Unable to add delivery: " . pg_last_error($conn);
}

header("Location: deliveries.php");
exit();
?>
<?php

require("../config/database.php");

session_start();

if($_SERVER["REQUEST_METHOD"]=="POST")
{

$name=trim($_POST['customer_name']);
$phone=trim($_POST['phone']);
$email=trim($_POST['email']);
$age=$_POST['age'];
$gender=$_POST['gender'];
$address=trim($_POST['address']);

if(empty($name)||empty($phone))
{
$_SESSION['error']="Name and Phone are required.";

header("Location:add_customer.php");
exit();
}

$check=pg_query_params(
$conn,
"SELECT customer_id FROM customers WHERE phone=$1",
array($phone)
);

if(pg_num_rows($check)>0)
{
$_SESSION['error']="Phone number already exists.";

header("Location:add_customer.php");
exit();
}

$query="INSERT INTO customers
(customer_name,phone,email,age,gender,address)
VALUES($1,$2,$3,$4,$5,$6)";

$result=pg_query_params(
$conn,
$query,
array(
$name,
$phone,
$email,
$age,
$gender,
$address
)
);

if($result)
{
$_SESSION['success']="Customer Added Successfully.";
}
else
{
$_SESSION['error']="Unable to Add Customer.";
}

header("Location:customers.php");
exit();

}
?>
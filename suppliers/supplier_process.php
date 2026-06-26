<?php

require("../config/database.php");

if(session_status()==PHP_SESSION_NONE)
{
    session_start();
}

if($_SERVER["REQUEST_METHOD"]=="POST")
{

$name=trim($_POST['supplier_name']);

$contact=trim($_POST['contact_person']);

$phone=trim($_POST['phone']);

$email=trim($_POST['email']);

$address=trim($_POST['address']);

if(empty($name)||empty($phone))
{

$_SESSION['error']="Supplier Name and Phone are required.";

header("Location:add_supplier.php");

exit();

}

$check=pg_query_params(

$conn,

"SELECT supplier_id FROM suppliers WHERE phone=$1",

array($phone)

);

if(pg_num_rows($check)>0)
{

$_SESSION['error']="Phone Number already exists.";

header("Location:add_supplier.php");

exit();

}

$query="

INSERT INTO suppliers(

supplier_name,

contact_person,

phone,

email,

address

)

VALUES(

$1,$2,$3,$4,$5

)

";

$result=pg_query_params(

$conn,

$query,

array(

$name,

$contact,

$phone,

$email,

$address

)

);

if($result)
{

$_SESSION['success']="Supplier Added Successfully.";

header("Location:suppliers.php");

}
else
{

$_SESSION['error']="Unable to Add Supplier.";

header("Location:add_supplier.php");

}

exit();

}

?>
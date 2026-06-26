<?php

require("../config/database.php");

session_start();

if(!isset($_GET['id']))
{

$_SESSION['error']="Invalid Order";

header("Location:orders.php");

exit();

}

$order_id=$_GET['id'];

$stockQuery="

SELECT

product_id,

quantity

FROM order_items

WHERE order_id=$1

";

$items=pg_query_params(

$conn,

$stockQuery,

array($order_id)

);

while($row=pg_fetch_assoc($items))
{

$product=$row['product_id'];

$qty=$row['quantity'];

pg_query_params(

$conn,

"

UPDATE products

SET stock=stock+$1

WHERE product_id=$2

",

array(

$qty,

$product

)

);

}

pg_query_params(

$conn,

"DELETE FROM payments

WHERE order_id=$1",

array($order_id)

);

pg_query_params(

$conn,

"DELETE FROM order_items

WHERE order_id=$1",

array($order_id)

);

$result=pg_query_params(

$conn,

"DELETE FROM orders

WHERE order_id=$1",

array($order_id)

);

if($result)
{

$_SESSION['success']="Order Deleted Successfully";

}
else
{

$_SESSION['error']="Unable to Delete Order";

}

header("Location:orders.php");

exit();

?>
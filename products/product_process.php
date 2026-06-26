<?php

require("../config/database.php");

session_start();

$name=$_POST['product_name'];

$brand=$_POST['brand'];

$category=$_POST['category_id'];

$price=$_POST['price'];

$stock=$_POST['stock'];

$description=$_POST['description'];

$image="";

if($_FILES['image']['name']!="")
{

$image=time()."_".$_FILES['image']['name'];

move_uploaded_file(

$_FILES['image']['tmp_name'],

"../uploads/products/".$image

);

}

$query="INSERT INTO products(

product_name,

brand,

category_id,

price,

stock,

image,

description

)

VALUES(

$1,$2,$3,$4,$5,$6,$7

)";

$result=pg_query_params(

$conn,

$query,

array(

$name,

$brand,

$category,

$price,

$stock,

$image,

$description

)

);

if($result)
{

$_SESSION['success']="Product Added Successfully.";

}
else
{

$_SESSION['error'] = "Unable to Add Product: " . pg_last_error($conn);

}

header("Location:products.php");

exit();

?>
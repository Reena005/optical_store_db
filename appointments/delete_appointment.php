<?php

require("../config/database.php");

if(session_status()===PHP_SESSION_NONE){
session_start();
}

$id=$_GET['id'];

$result=pg_query_params(

$conn,

"DELETE FROM appointments

WHERE appointment_id=$1",

array($id)

);

if($result)
$_SESSION['success']="Appointment deleted successfully.";
else
$_SESSION['error']="Unable to delete appointment.";

header("Location: appointments.php");
exit();

?>
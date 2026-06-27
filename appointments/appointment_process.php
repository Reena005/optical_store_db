<?php

require("../config/database.php");

if(session_status()===PHP_SESSION_NONE){
    session_start();
}

$customer=$_POST['customer_id'];
$doctor=$_POST['doctor_name'];
$date=$_POST['appointment_date'];
$time=$_POST['appointment_time'];
$status=$_POST['status'];
$notes=trim($_POST['notes']);

$result=pg_query_params(

$conn,

"INSERT INTO appointments(

customer_id,
doctor_name,
appointment_date,
appointment_time,
status,
notes

)

VALUES(

$1,$2,$3,$4,$5,$6

)",

array(

$customer,
$doctor,
$date,
$time,
$status,
$notes

)

);

if($result){

$_SESSION['success']="Appointment booked successfully.";

}else{

$_SESSION['error']="Unable to book appointment.";

}

header("Location: appointments.php");
exit();

?>
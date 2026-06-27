<?php

require("../includes/auth_check.php");
require("../config/database.php");

if(session_status()===PHP_SESSION_NONE){
session_start();
}

if($_SERVER["REQUEST_METHOD"]=="POST")
{

$id=$_POST['appointment_id'];

$result=pg_query_params(

$conn,

"UPDATE appointments

SET

doctor_name=$1,

appointment_date=$2,

appointment_time=$3,

status=$4,

notes=$5

WHERE appointment_id=$6",

array(

$_POST['doctor_name'],

$_POST['appointment_date'],

$_POST['appointment_time'],

$_POST['status'],

trim($_POST['notes']),

$id

)

);

if($result)
$_SESSION['success']="Appointment updated successfully.";
else
$_SESSION['error']="Unable to update appointment.";

header("Location: appointments.php");
exit();

}

$id=$_GET['id'];

$query=pg_query_params(

$conn,

"SELECT a.*,c.customer_name

FROM appointments a

LEFT JOIN customers c

ON a.customer_id=c.customer_id

WHERE appointment_id=$1",

array($id)

);

$row=pg_fetch_assoc($query);

include("../includes/header.php");
include("../includes/navbar.php");

?>

<div class="d-flex">

<?php include("../includes/sidebar.php");?>

<div class="content flex-grow-1">

<div class="container-fluid">

<h2 class="mb-4">

Edit Appointment

</h2>

<div class="card shadow">

<div class="card-body">

<form method="POST">

<input
type="hidden"
name="appointment_id"
value="<?= $row['appointment_id'];?>">

<div class="mb-3">

<label>Customer</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($row['customer_name']);?>"
readonly>

</div>

<div class="mb-3">

<label>Doctor</label>

<input
type="text"
name="doctor_name"
class="form-control"
value="<?= htmlspecialchars($row['doctor_name']);?>"
required>

</div>

<div class="row">

<div class="col-md-6">

<label>Date</label>

<input
type="date"
name="appointment_date"
class="form-control"
value="<?= $row['appointment_date'];?>"
required>

</div>

<div class="col-md-6">

<label>Time</label>

<input
type="time"
name="appointment_time"
class="form-control"
value="<?= substr($row['appointment_time'],0,5);?>"
required>

</div>

</div>

<br>

<div class="mb-3">

<label>Status</label>

<select
name="status"
class="form-select">

<option <?=($row['status']=="Booked")?"selected":"";?>>
Booked
</option>

<option <?=($row['status']=="Checked")?"selected":"";?>>
Checked
</option>

<option <?=($row['status']=="Completed")?"selected":"";?>>
Completed
</option>

<option <?=($row['status']=="Cancelled")?"selected":"";?>>
Cancelled
</option>

</select>

</div>

<div class="mb-3">

<label>Notes</label>

<textarea
name="notes"
class="form-control"
rows="4"><?= htmlspecialchars($row['notes']);?></textarea>

</div>

<button class="btn btn-success">

Update Appointment

</button>

<a
href="appointments.php"
class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

</div>

</div>

</div>

<?php include("../includes/footer.php");?>
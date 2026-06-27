<?php

require("../includes/auth_check.php");
require("../config/database.php");
require("../vendor/autoload.php");

use Dompdf\Dompdf;

if (!isset($_GET['prescription_id'])) {
    die("Invalid customer.");
}

$customer_id = $_GET['prescription_id'];

$query = "
SELECT 
    c.customer_name,
    c.phone,
    c.email,
    c.address,
    p.*
FROM prescriptions p
JOIN customers c ON p.customer_id = c.customer_id
WHERE p.customer_id = $1
ORDER BY p.prescription_id ASC
LIMIT 1
";
$result = pg_query_params($conn, $query, array($prescription_id));

if (!$result || pg_num_rows($result) == 0) {
    die("No prescription found.");
}

$data = pg_fetch_assoc($result);

$html = '
<h2 style="text-align:center;">Clarity Optical Store</h2>
<h3 style="text-align:center;">Optical Prescription</h3>

<hr>

<h4>Customer Details</h4>
<p>
<b>Name:</b> '.htmlspecialchars($data['customer_name']).'<br>
<b>Phone:</b> '.htmlspecialchars($data['phone']).'<br>
<b>Email:</b> '.htmlspecialchars($data['email']).'<br>
<b>Address:</b> '.htmlspecialchars($data['address']).'
</p>

<h4>Prescription Details</h4>

<table border="1" width="100%" cellspacing="0" cellpadding="8">
<tr>
    <th>Eye</th>
    <th>SPH</th>
    <th>CYL</th>
    <th>AXIS</th>
    <th>ADD</th>
</tr>

<tr>
    <td><b>Right Eye / OD</b></td>
    <td>'.htmlspecialchars($data['right_sph']).'</td>
    <td>'.htmlspecialchars($data['right_cyl']).'</td>
    <td>'.htmlspecialchars($data['right_axis']).'</td>
    <td>'.htmlspecialchars($data['right_add']).'</td>
</tr>

<tr>
    <td><b>Left Eye / OS</b></td>
    <td>'.htmlspecialchars($data['left_sph']).'</td>
    <td>'.htmlspecialchars($data['left_cyl']).'</td>
    <td>'.htmlspecialchars($data['left_axis']).'</td>
    <td>'.htmlspecialchars($data['left_add']).'</td>
</tr>
</table>

<br>

<p>
<b>Doctor / Optometrist:</b> '.htmlspecialchars($data['doctor_name']).'<br>
<b>Prescription Date:</b> '.htmlspecialchars($data['prescription_date']).'
</p>

<br><br>

<table width="100%">
<tr>
<td>
_________________________<br>
Doctor Signature
</td>
<td style="text-align:right;">
_________________________<br>
Store Seal
</td>
</tr>
</table>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$dompdf->stream("Prescription_" . $prescription_id . ".pdf", ["Attachment" => true]);

?>
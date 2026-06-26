<?php

require("../config/database.php");
require("../vendor/autoload.php");

use Dompdf\Dompdf;

$result = pg_query(
    $conn,
    "SELECT customer_id, customer_name, phone, email, gender, age, address
     FROM customers
     ORDER BY customer_id DESC"
);

$html = '
<h2 style="text-align:center;">Optical Store - Customer Report</h2>
<table border="1" width="100%" cellspacing="0" cellpadding="6">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Gender</th>
    <th>Age</th>
    <th>Address</th>
</tr>
';

while ($row = pg_fetch_assoc($result)) {
    $html .= '
    <tr>
        <td>' . htmlspecialchars($row['customer_id']) . '</td>
        <td>' . htmlspecialchars($row['customer_name']) . '</td>
        <td>' . htmlspecialchars($row['phone']) . '</td>
        <td>' . htmlspecialchars($row['email']) . '</td>
        <td>' . htmlspecialchars($row['gender']) . '</td>
        <td>' . htmlspecialchars($row['age']) . '</td>
        <td>' . htmlspecialchars($row['address']) . '</td>
    </tr>';
}

$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "landscape");
$dompdf->render();
$dompdf->stream("customers_report.pdf", ["Attachment" => true]);

?>
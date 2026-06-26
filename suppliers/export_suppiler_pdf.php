<?php

require("../config/database.php");
require("../vendor/autoload.php");

use Dompdf\Dompdf;

$query = "
SELECT supplier_id, supplier_name, contact_person, phone, email, address
FROM suppliers
ORDER BY supplier_id DESC
";

$result = pg_query($conn, $query);

$html = '
<h2 style="text-align:center;">Optical Store Management System</h2>
<h3 style="text-align:center;">Supplier Report</h3>

<table border="1" width="100%" cellspacing="0" cellpadding="6">
<tr>
    <th>ID</th>
    <th>Supplier Name</th>
    <th>Contact Person</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Address</th>
</tr>
';

while ($row = pg_fetch_assoc($result)) {
    $html .= '
    <tr>
        <td>' . htmlspecialchars($row['supplier_id']) . '</td>
        <td>' . htmlspecialchars($row['supplier_name']) . '</td>
        <td>' . htmlspecialchars($row['contact_person']) . '</td>
        <td>' . htmlspecialchars($row['phone']) . '</td>
        <td>' . htmlspecialchars($row['email']) . '</td>
        <td>' . htmlspecialchars($row['address']) . '</td>
    </tr>
    ';
}

$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "landscape");
$dompdf->render();
$dompdf->stream("suppliers_report.pdf", ["Attachment" => true]);

?>
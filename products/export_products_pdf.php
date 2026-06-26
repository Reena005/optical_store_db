<?php

require("../config/database.php");
require("../vendor/autoload.php");

use Dompdf\Dompdf;

$query = "
SELECT
p.product_name,
p.brand,
c.category_name,
p.price,
p.stock
FROM products p
LEFT JOIN categories c
ON p.category_id=c.category_id
ORDER BY product_name
";

$result = pg_query($conn,$query);

$html='

<h2 style="text-align:center;">
Optical Store Management System
</h2>

<h3 style="text-align:center;">
Product Report
</h3>

<table border="1" width="100%" cellspacing="0" cellpadding="6">

<tr>

<th>Product</th>

<th>Brand</th>

<th>Category</th>

<th>Price</th>

<th>Stock</th>

</tr>

';

while($row=pg_fetch_assoc($result))
{

$html.='

<tr>

<td>'.$row['product_name'].'</td>

<td>'.$row['brand'].'</td>

<td>'.$row['category_name'].'</td>

<td>₹'.$row['price'].'</td>

<td>'.$row['stock'].'</td>

</tr>

';

}

$html.='</table>';

$pdf=new Dompdf();

$pdf->loadHtml($html);

$pdf->setPaper("A4","landscape");

$pdf->render();

$pdf->stream(
"Products_Report.pdf",
array("Attachment"=>true)
);

?>
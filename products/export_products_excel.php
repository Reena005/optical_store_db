<?php

require("../config/database.php");
require("../vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$query="

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

$result=pg_query($conn,$query);

$spreadsheet=new Spreadsheet();

$sheet=$spreadsheet->getActiveSheet();

$sheet->setCellValue("A1","Product");
$sheet->setCellValue("B1","Brand");
$sheet->setCellValue("C1","Category");
$sheet->setCellValue("D1","Price");
$sheet->setCellValue("E1","Stock");

$rowNumber=2;

while($row=pg_fetch_assoc($result))
{

$sheet->setCellValue("A".$rowNumber,$row['product_name']);
$sheet->setCellValue("B".$rowNumber,$row['brand']);
$sheet->setCellValue("C".$rowNumber,$row['category_name']);
$sheet->setCellValue("D".$rowNumber,$row['price']);
$sheet->setCellValue("E".$rowNumber,$row['stock']);

$rowNumber++;

}

$writer=new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment; filename="Products_Report.xlsx"');

header('Cache-Control: max-age=0');

$writer->save('php://output');

exit();

?>
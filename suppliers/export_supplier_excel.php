<?php

require("../config/database.php");
require("../vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$query = "
SELECT supplier_id, supplier_name, contact_person, phone, email, address
FROM suppliers
ORDER BY supplier_id DESC
";

$result = pg_query($conn, $query);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue("A1", "ID");
$sheet->setCellValue("B1", "Supplier Name");
$sheet->setCellValue("C1", "Contact Person");
$sheet->setCellValue("D1", "Phone");
$sheet->setCellValue("E1", "Email");
$sheet->setCellValue("F1", "Address");

$rowNumber = 2;

while ($row = pg_fetch_assoc($result)) {
    $sheet->setCellValue("A" . $rowNumber, $row['supplier_id']);
    $sheet->setCellValue("B" . $rowNumber, $row['supplier_name']);
    $sheet->setCellValue("C" . $rowNumber, $row['contact_person']);
    $sheet->setCellValue("D" . $rowNumber, $row['phone']);
    $sheet->setCellValue("E" . $rowNumber, $row['email']);
    $sheet->setCellValue("F" . $rowNumber, $row['address']);

    $rowNumber++;
}

$writer = new Xlsx($spreadsheet);

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=suppliers_report.xlsx");
header("Cache-Control: max-age=0");

$writer->save("php://output");
exit();

?>
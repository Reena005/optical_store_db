<?php

require("../config/database.php");
require("../vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$result = pg_query(
    $conn,
    "SELECT customer_id, customer_name, phone, email, gender, age, address
     FROM customers
     ORDER BY customer_id DESC"
);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue("A1", "ID");
$sheet->setCellValue("B1", "Customer Name");
$sheet->setCellValue("C1", "Phone");
$sheet->setCellValue("D1", "Email");
$sheet->setCellValue("E1", "Gender");
$sheet->setCellValue("F1", "Age");
$sheet->setCellValue("G1", "Address");

$rowNumber = 2;

while ($row = pg_fetch_assoc($result)) {
    $sheet->setCellValue("A" . $rowNumber, $row['customer_id']);
    $sheet->setCellValue("B" . $rowNumber, $row['customer_name']);
    $sheet->setCellValue("C" . $rowNumber, $row['phone']);
    $sheet->setCellValue("D" . $rowNumber, $row['email']);
    $sheet->setCellValue("E" . $rowNumber, $row['gender']);
    $sheet->setCellValue("F" . $rowNumber, $row['age']);
    $sheet->setCellValue("G" . $rowNumber, $row['address']);

    $rowNumber++;
}

$writer = new Xlsx($spreadsheet);

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=customers_report.xlsx");
header("Cache-Control: max-age=0");

$writer->save("php://output");
exit();

?>
<?php
require 'vendor/autoload.php';
include 'config.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
if(!$event_id) die("No event selected");
$event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM events WHERE id=$event_id"));
$attendees = mysqli_query($conn, "SELECT * FROM attendees WHERE event_id=$event_id ORDER BY scanned_at DESC");
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Event: ' . $event['event_name']);
$sheet->setCellValue('A2', 'Date: ' . $event['event_date'] . ' at ' . $event['event_time']);
$sheet->setCellValue('A3', 'Venue: ' . $event['venue']);
$sheet->setCellValue('A5', '#');
$sheet->setCellValue('B5', 'Name');
$sheet->setCellValue('C5', 'Email');
$sheet->setCellValue('D5', 'Phone');
$sheet->setCellValue('E5', 'Check-in Time');
$row = 6; $i = 1;
while($a = mysqli_fetch_assoc($attendees)){
    $sheet->setCellValue('A'.$row, $i++);
    $sheet->setCellValue('B'.$row, $a['name']);
    $sheet->setCellValue('C'.$row, $a['email']);
    $sheet->setCellValue('D'.$row, $a['phone']);
    $sheet->setCellValue('E'.$row, $a['scanned_at']);
    $row++;
}
foreach(range('A','E') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
$filename = 'attendance_' . preg_replace('/[^a-zA-Z0-9]/', '_', $event['event_name']) . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
(new Xlsx($spreadsheet))->save('php://output');
?>
<?php
session_start();
include '../db_connect.php';

require '../vendor/autoload.php'; // Đường dẫn tới autoload của Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$report = $_GET['report'] ?? '';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

if ($report === 'daily_revenue') {
    $stmt = $pdo->query("
        SELECT DATE(order_date) AS order_day, SUM(total_amount) AS daily_revenue
        FROM orders
        WHERE status = 'Pending'
        GROUP BY DATE(order_date)
        ORDER BY order_day DESC
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Revenue');
    $row = 2;
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item['order_day']);
        $sheet->setCellValue('B' . $row, $item['daily_revenue']);
        $row++;
    }
    $filename = 'daily_revenue_report_' . date('Ymd') . '.xlsx';
} elseif ($report === 'top_products') {
    $stmt = $pdo->query("
        SELECT p.product_id, p.name, SUM(od.quantity) AS total_sold
        FROM order_details od
        JOIN products p ON od.product_id = p.product_id
        GROUP BY p.product_id, p.name
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sheet->setCellValue('A1', 'Product ID');
    $sheet->setCellValue('B1', 'Name');
    $sheet->setCellValue('C1', 'Total Sold');
    $row = 2;
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item['product_id']);
        $sheet->setCellValue('B' . $row, $item['name']);
        $sheet->setCellValue('C' . $row, $item['total_sold']);
        $row++;
    }
    $filename = 'top_products_report_' . date('Ymd') . '.xlsx';
} else {
    die("Invalid report type");
}

// Xuất file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
<?php
session_start();
include '../db_connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$type = $_GET['type'] ?? 'daily_revenue';
$data = ['labels' => [], 'values' => []];

if ($type === 'daily_revenue') {
    $stmt = $pdo->query("
        SELECT DATE(order_date) AS order_day, SUM(total_amount) AS daily_revenue
        FROM orders
        WHERE status = 'Pending'
        GROUP BY DATE(order_date)
        ORDER BY order_day ASC
    ");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $data['labels'][] = $row['order_day'];
        $data['values'][] = (float)$row['daily_revenue'];
    }
} elseif ($type === 'top_products') {
    $stmt = $pdo->query("
        SELECT p.name, SUM(od.quantity) AS total_sold
        FROM order_details od
        JOIN products p ON od.product_id = p.product_id
        GROUP BY p.product_id, p.name
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $data['labels'][] = $row['name'];
        $data['values'][] = (int)$row['total_sold'];
    }
}

header('Content-Type: application/json');
echo json_encode($data);
exit();
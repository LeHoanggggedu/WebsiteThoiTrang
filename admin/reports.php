<?php
session_start();
include '../db_connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Báo cáo 1: Doanh thu theo ngày
$stmt = $pdo->query("
    SELECT DATE(order_date) AS order_day, SUM(total_amount) AS daily_revenue
    FROM orders
    WHERE status = 'Pending'
    GROUP BY DATE(order_date)
    ORDER BY order_day DESC
");
$daily_revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Báo cáo 2: Sản phẩm bán chạy nhất
$stmt = $pdo->query("
    SELECT p.product_id, p.name, SUM(od.quantity) AS total_sold
    FROM order_details od
    JOIN products p ON od.product_id = p.product_id
    GROUP BY p.product_id, p.name
    ORDER BY total_sold DESC
    LIMIT 5
");
$top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Báo cáo 3: Tổng số đơn hàng
$stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM orders WHERE status = 'Pending'");
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports - Dimoi Archive</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>Dimoi Admin</h2>
            </div>
            <nav>
                <ul>
                    <li>
                        <a href="index.php#orders" data-tab="orders">
                            <i class="bi bi-cart"></i> Orders
                        </a>
                    </li>
                    <li>
                        <a href="index.php#products" data-tab="products">
                            <i class="bi bi-box"></i> Products
                        </a>
                    </li>
                    <li class="active">
                        <a href="reports.php">
                            <i class="bi bi-bar-chart"></i> Reports
                        </a>
                    </li>
                    <li>
                <a href="overview.php">
                    <i class="bi bi-graph-up"></i> Overview
                </a>
            </li>
                    <li>
                        <a href="../index.php">
                            <i class="bi bi-house"></i> Back to Site
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2>Reports</h2>

            <!-- Báo cáo 1: Doanh thu theo ngày -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Daily Revenue</h5>
                    <a href="export_excel.php?report=daily_revenue" class="btn btn-sm btn-success float-end">Export to Excel</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($daily_revenue as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['order_day']); ?></td>
                                        <td>$<?php echo number_format($row['daily_revenue'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Báo cáo 2: Sản phẩm bán chạy -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Top Selling Products</h5>
                    <a href="export_excel.php?report=top_products" class="btn btn-sm btn-success float-end">Export to Excel</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Total Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_products as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo $row['total_sold']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Báo cáo 3: Tổng số đơn hàng -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Total Orders</h5>
                </div>
                <div class="card-body">
                    <p><strong>Total Orders (Pending):</strong> <?php echo $total_orders; ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
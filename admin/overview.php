<?php
session_start();
include '../db_connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Overview - Dimoi Archive</title>
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
                    <li>
                        <a href="reports.php">
                            <i class="bi bi-bar-chart"></i> Reports
                        </a>
                    </li>
                    <li class="active">
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
            <h2>Overview</h2>
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Statistics</h5>
                    <div class="float-end">
                        <select id="chart-type" class="form-select" style="width: auto; display: inline-block;">
                            <option value="daily_revenue">Daily Revenue</option>
                            <option value="top_products">Top Selling Products</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="overviewChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Thư viện Bootstrap và Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        let myChart;

        // Hàm vẽ biểu đồ
        function drawChart(type, labels, data) {
            const ctx = document.getElementById('overviewChart').getContext('2d');
            
            // Hủy biểu đồ cũ nếu có
            if (myChart) {
                myChart.destroy();
            }

            // Cấu hình biểu đồ
            myChart = new Chart(ctx, {
                type: type === 'daily_revenue' ? 'line' : 'bar', // Line cho doanh thu, Bar cho sản phẩm
                data: {
                    labels: labels,
                    datasets: [{
                        label: type === 'daily_revenue' ? 'Revenue ($)' : 'Units Sold',
                        data: data,
                        backgroundColor: type === 'daily_revenue' ? 'rgba(75, 192, 192, 0.2)' : 'rgba(54, 162, 235, 0.2)',
                        borderColor: type === 'daily_revenue' ? 'rgba(75, 192, 192, 1)' : 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Hàm lấy dữ liệu từ server
        function fetchChartData(type) {
            fetch(`get_chart_data.php?type=${type}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    drawChart(type, data.labels, data.values);
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                });
        }

        // Xử lý sự kiện khi thay đổi loại biểu đồ
        document.getElementById('chart-type').addEventListener('change', function() {
            const selectedType = this.value;
            fetchChartData(selectedType);
        });

        // Load biểu đồ mặc định khi trang mở
        window.onload = function() {
            fetchChartData('daily_revenue');
        };
    </script>
</body>
</html>
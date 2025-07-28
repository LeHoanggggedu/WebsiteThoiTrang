<?php
$host = 'localhost';
$port = 3306; // Dựa trên servername "localhost:3306" từ hàm cũ
$dbname = 'cuahangquanao'; // Tên database từ hàm cũ
$username = 'root'; // Username từ hàm cũ
$password = ''; // Password từ hàm cũ (trống)

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; // Dòng này có thể bỏ khi chạy thực tế
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<?php
$host = 'localhost';
$port = 3360;
$dbname = 'cuahangquanao'; // Thay bằng tên database của bạn
$username = 'root';
$password = ''; // Thay bằng mật khẩu của bạn nếu có

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
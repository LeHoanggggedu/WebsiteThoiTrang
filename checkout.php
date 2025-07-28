<?php
session_start(); // Khởi động session
include 'db_connect.php'; // Kết nối database

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin người dùng từ database
$stmt = $pdo->prepare("SELECT email, phone FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $items = [];
    if (isset($_SESSION['checkout'])) {
        // Từ "Buy it now"
        $items[] = $_SESSION['checkout'];
    } elseif (!empty($_SESSION['cart'])) {
        // Từ giỏ hàng
        $items = $_SESSION['cart'];
    }

    if (empty($items)) {
        $error = "Không có sản phẩm để đặt hàng.";
    } else {
        // Tính tổng tiền
        $total_amount = 0;
        foreach ($items as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }
        $total_amount += $total_amount * 0.1; // Thêm thuế 10%

        // Thêm vào bảng orders
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, order_date) VALUES (?, ?, 'Pending', NOW())");
        $stmt->execute([$user_id, $total_amount]);
        $order_id = $pdo->lastInsertId(); // Lấy order_id vừa thêm

        // Thêm vào bảng order_details và cập nhật số lượng tồn kho
        foreach ($items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_details (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $item['size']]); // Thêm size

            // Cập nhật số lượng tồn kho trong bảng products
            $size_field = "stock_" . strtolower($item['size']);
            $stmt = $pdo->prepare("UPDATE products SET $size_field = $size_field - ? WHERE product_id = ? AND $size_field >= ?");
            $stmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
        }

        // Tổng hợp và lưu thông tin địa chỉ vào bảng users
        $country = $_POST['country'] ?? '';
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $address = $_POST['address'] ?? '';
        $apartment = $_POST['apartment'] ?? '';
        $city = $_POST['city'] ?? '';
        $postal_code = $_POST['postal_code'] ?? '';
        $phone = $_POST['phone'] ?? '';

        $full_address = implode(' -- ', array_filter([
            $country,
            $first_name,
            $last_name,
            $address,
            $apartment,
            $city,
            $postal_code,
            $phone
        ])); // Tổng hợp các trường, bỏ qua rỗng

        $stmt = $pdo->prepare("UPDATE users SET address = ? WHERE user_id = ?");
        $stmt->execute([$full_address, $user_id]);

        // Xóa session sau khi đặt hàng
        unset($_SESSION['checkout']);
        unset($_SESSION['cart']);
        $success = "Đơn hàng của bạn đã được đặt thành công!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Dimoi Archive</title>
    <link rel="stylesheet" href="assets/css/style_checkout.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Dimoi Archive.</h1>
        </header>
        <main>
            <div class="checkout-left">
                <form method="POST" id="checkoutForm">
                    <section class="contact">
                        <h2>Contact</h2>
                        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                        <div>
                            <input type="checkbox" id="news-offers" name="news_offers">
                            <label for="news-offers">Email me with news and offers</label>
                        </div>
                    </section>

                    <section class="delivery">
                        <h2>Delivery</h2>
                        <select name="country" required>
                            <option value="Vietnam" >Vietnam</option>
                            <option value="USA" >USA</option>
                            <option value="Japan">Japan</option>
                        </select>
                        <div class="input-row">
                            <input type="text" name="first_name" placeholder="First name" required>
                            <input type="text" name="last_name" placeholder="Last name" required>
                        </div>
                        <input type="text" name="address" placeholder="Address" required>
                        <input type="text" name="apartment" placeholder="Apartment, suite, etc. (optional)">
                        <div class="input-row">
                            <input type="text" name="city" placeholder="City" required>
                            <input type="text" name="postal_code" placeholder="Postal code (optional)">
                        </div>
                        <input type="text" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                    </section>
            </div>

            <div class="checkout-right">
                <section class="order-summary">
                    <h2>Order Summary</h2>
                    <?php 
                    $items = [];
                    if (isset($_SESSION['checkout'])) {
                        $items[] = $_SESSION['checkout'];
                    } elseif (!empty($_SESSION['cart'])) {
                        $items = $_SESSION['cart'];
                    }

                    if (empty($items)): ?>
                        <p>Your cart is empty.</p>
                    <?php else: 
                        $subtotal = 0;
                        foreach ($items as $item): 
                            $total = $item['price'] * $item['quantity'];
                            $subtotal += $total;
                    ?>
                        <div class="product">
                            <p><?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['name']); ?> (Size: <?php echo htmlspecialchars($item['size']); ?>)</p>
                            <p>$<?php echo number_format($total, 2); ?></p>
                        </div>
                    <?php endforeach; 
                        $tax = $subtotal * 0.1; // Thuế 10%
                        $grand_total = $subtotal + $tax;
                    ?>
                        <hr>
                        <div class="summary">
                            <p>Subtotal</p>
                            <p>$<?php echo number_format($subtotal, 2); ?></p>
                        </div>
                        <div class="summary">
                            <p>Shipping</p>
                            <p>FREE</p>
                        </div>
                        <div class="summary">
                            <p>Estimated taxes</p>
                            <p>$<?php echo number_format($tax, 2); ?></p>
                        </div>
                        <hr>
                        <div class="total">
                            <p>Total</p>
                            <p>$<?php echo number_format($grand_total, 2); ?></p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </main>
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php elseif (isset($success)): ?>
            <p class="text-success text-center"><?php echo $success; ?></p>
        <?php else: ?>
            <button type="submit" name="place_order" class="order-button">Đặt hàng</button>
        </form>
        <?php endif; ?>
    </div>
    <script src="assets/js/checkout.js"></script>
</body>
</html>
<?php
session_start(); // Khởi động session
include 'db_connect.php'; // Kết nối database
include 'header.php';     // Nhúng header.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Dimoi Archive</title>
    <link rel="stylesheet" href="assets/css/style_cartt.css">
</head>
<body>
    <div class="cart">
        <div class="hed">
            <h1 class="cart-title">Your cart</h1>
            <a href="products.php" class="continue-shopping">Continue shopping</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (empty($_SESSION['cart'])): ?>
                    <tr>
                        <td colspan="3">Your cart is empty.</td>
                    </tr>
                <?php else: 
                    $subtotal = 0;
                    foreach ($_SESSION['cart'] as $cart_key => $item): 
                        $image_urls = explode(';', $item['image_url'] ?? '');
                        $first_image = !empty($image_urls[0]) ? $image_urls[0] : 'default.jpg';
                        $total = $item['price'] * $item['quantity'];
                        $subtotal += $total;
                ?>
                    <tr>
                        <td>    
                            <div class="product">
                                <img src="assets/images/<?php echo htmlspecialchars($first_image); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div>
                                    <p class="name"><a href="product.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></p>
                                    <p class="price"><?php echo number_format($item['price'], 0); ?>$</p>
                                    <p>SIZE: <?php echo htmlspecialchars($item['size']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="quantity">
                                <button>-</button>
                                <input type="number" value="<?php echo $item['quantity']; ?>" min="1">
                                <button>+</button>
                                <button class="delete" data-key="<?php echo $cart_key; ?>">🗑</button>
                            </div>
                        </td>
                        <td class="total"><?php echo number_format($total, 0); ?>$</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="cart-summary">
        <p>Subtotal: <span id="subtotal"><?php echo isset($subtotal) ? number_format($subtotal, 0) . '$' : '0$'; ?></span></p>
        <p>Taxes and shipping calculated at checkout</p>
        <!-- Đảm bảo nút Check out không bị chặn bởi JS -->
        <a href="checkout.php" id="checkout-link"><button class="checkout-btn">Check out</button></a>
    </div>
    <?php include 'footer.php'; // Nhúng footer.php ?>

    <script src="assets/js/cacula_cart.js"></script>
    <script>
        // Xử lý xóa sản phẩm
        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function() {
                const cartKey = this.dataset.key;
                fetch('cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'delete_item=' + cartKey
                }).then(() => location.reload());
            });
        });



        // Đảm bảo nút Check out chuyển hướng đúng
        document.getElementById('checkout-link').addEventListener('click', function(e) {
            e.preventDefault(); // Ngăn chặn mặc định để kiểm tra
            window.location.href = this.href; // Ép chuyển hướng
        });
    </script>
</body>
</html>

<?php
// Xử lý xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $cart_key = $_POST['delete_item'];
    if (isset($_SESSION['cart'][$cart_key])) {
        unset($_SESSION['cart'][$cart_key]);
    }
    exit();
}
?>
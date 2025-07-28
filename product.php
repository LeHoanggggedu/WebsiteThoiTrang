<?php
session_start(); // Khởi động session
include 'db_connect.php'; // Kết nối database

// Lấy product_id từ URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    die("Invalid product ID");
}

// Truy vấn thông tin sản phẩm
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found");
}

// Xử lý "Add to Cart"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (empty($_POST['size'])) {
        $error = "Please select a size before adding to cart.";
    } else {
        $size = $_POST['size'];
        $quantity = (int)$_POST['quantity'];

        $cart_item = [
            'product_id' => $product['product_id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'size' => $size,
            'quantity' => $quantity,
            'image_url' => $product['image_url']
        ];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $cart_key = $product['product_id'] . '_' . $size;
        if (isset($_SESSION['cart'][$cart_key])) {
            $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cart_key] = $cart_item;
        }

        header("Location: cart.php");
        exit();
    }
}

// Xử lý "Buy it now"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now'])) {
    if (empty($_POST['size'])) {
        $error = "Please select a size before proceeding to checkout.";
    } else {
        $size = $_POST['size'];
        $quantity = (int)$_POST['quantity'];

        // Lưu thông tin đơn hàng tạm thời vào session để checkout
        $_SESSION['checkout'] = [
            'product_id' => $product['product_id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'size' => $size,
            'quantity' => $quantity,
            'image_url' => $product['image_url']
        ];

        header("Location: checkout.php");
        exit();
    }
}

// Tách danh sách ảnh
$image_urls = explode(';', $product['image_url']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Dimoi Archive</title>
    <link rel="stylesheet" href="assets/css/style_cart.css">
    <link rel="stylesheet" href="assets/css/style_product.css">
    <style>

        .size-btn:disabled {
            opacity: 0.5;
            background-color: #e0e0e0;
            border-color: #d0d0d0;
            color: #888;
            cursor: not-allowed;
            pointer-events: none;
        }

    </style>
</head>
<body>
    <?php include 'header.php'; // Nhúng header.php ?>
    <div class="menu">
        <ul>
            <li class="menu-heading">New Arrival</li>
            <ul>
            <li><a href="products.php">T Shirt</a></li>
            <li><a href="products.php">Hoodie</a></li>
            <li><a href="products.php">Shirt</a></li>
            <li><a href="products.php">Vest</a></li>
            </ul>
            <li class="menu-heading">Outerwear</li>
            <ul>
            <li><a href="products.php">Jackets</a></li>
            <li><a href="products.php">Shackets</a></li>
            <li><a href="products.php">Bomber</a></li>
            </ul>
            <li class="menu-heading">Bottoms</li>
            <ul>
            <li><a href="products.php">Jeans</a></li>
            <li><a href="products.php">Shorts</a></li>
            <li><a href="products.php">Khaki Pants</a></li>
            <li><a href="products.php">Skirt</a></li>
            </ul>
        </ul>
    </div>
    <main>
        <div class="toto">
            <div class="product-page">
                <div class="image-section">
                    <div class="main-image">
                        <img src="assets/images/<?php echo htmlspecialchars($image_urls[0] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="thumbnail-container">
                        <?php foreach ($image_urls as $url): ?>
                            <?php if (!empty($url)): ?>
                                <div class="thumbnail">
                                    <img src="assets/images/<?php echo htmlspecialchars($url); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="product-container">
                <h4>Dimoi Flexinn</h4>
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
              
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>

                <form method="POST" id="cart-form">
                    <div class="size-section">
                        <p>SIZE</p>
                        <button type="button" class="size-btn" data-size="S" <?php echo $product['stock_s'] <= 1 ? 'disabled' : ''; ?>>SIZE S</button>
                        <button type="button" class="size-btn" data-size="M" <?php echo $product['stock_m'] <= 1 ? 'disabled' : ''; ?>>SIZE M</button>
                        <button type="button" class="size-btn" data-size="L" <?php echo $product['stock_l'] <= 1 ? 'disabled' : ''; ?>>SIZE L</button>
                        <input type="hidden" name="size" id="selected-size" value="">
                    </div>
              
                    <div class="quantity-section">
                        <p>Quantity</p>
                        <div class="quantity-controls">
                            <button type="button" class="quantity-btn" onclick="updateQuantity(-1)">-</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" class="custom-number-input" />
                            <button type="button" class="quantity-btn" onclick="updateQuantity(1)">+</button>
                        </div>
                    </div>
              
                    <div class="action-buttons">
                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to cart</button>
                        <button type="submit" name="buy_now" class="buy-now">Buy it now</button>
                    </div>
                </form>
              
                <ul class="product-details">
                    <li>COLOR: BLACK</li>
                    <li>SIZE: S / M / L</li>
                    <li>FORM: BAGGY</li>
                    <li>WASHED</li>
                    <li>PRINTED ON FRONT AND BACK</li>
                    <li>SIZE CHART IN THE LAST PIC</li>
                </ul>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; // Nhúng footer.php ?>
    <script src="assets/js/product.js"></script>
    <script>
        // Cập nhật số lượng
        function updateQuantity(change) {
            let input = document.getElementById('quantity');
            let value = parseInt(input.value) + change;
            if (value < 1) value = 1;
            input.value = value;
        }

        // Chọn size
        document.querySelectorAll('.size-btn:not(:disabled)').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('selected-size').value = this.dataset.size;
            });
        });
    </script>
</body>
</html>
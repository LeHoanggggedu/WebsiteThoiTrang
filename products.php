<?php
include 'db_connect.php'; // Kết nối database
include 'header.php';     // Nhúng header.php


$category = isset($_GET['category']) ? $_GET['category'] : null;


if ($category) {
    $stmt = $pdo->prepare("SELECT product_id, name, price, image_url FROM products WHERE category = ?");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("SELECT product_id, name, price, image_url FROM products");
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Dimoi Archive</title>
    <link rel="stylesheet" href="assets/css/style_cart.css?v=2">
</head>
<body>
    <main>
    <div class="menu">
        <ul>
            <li class="menu-heading">New Arrival</li>
            <ul>
                <li><a href="products.php?category=ao">T Shirt</a></li>
                <li><a href="products.php?category=ao">Hoodie</a></li>
                <li><a href="products.php?category=ao">Shirt</a></li>
                <li><a href="products.php?category=ao">Vest</a></li>
            </ul>
            <li class="menu-heading">Outerwear</li>
            <ul>
                <li><a href="products.php?category=ao">Jackets</a></li>
                <li><a href="products.php?category=ao">Shackets</a></li>
                <li><a href="products.php?category=ao">Bomber</a></li>
            </ul>
            <li class="menu-heading">Bottoms</li>
            <ul>
                <li><a href="products.php?category=quan">Jeans</a></li>
                <li><a href="products.php?category=quan">Shorts</a></li>
                <li><a href="products.php?category=quan">Khaki Pants</a></li>
                <li><a href="products.php?category=quan">Skirt</a></li>
            </ul>
        </ul>
    </div>
        <div class="product-grid">
            <?php if (empty($products)): ?>
                <p>No products found in this category.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <?php 
                    $image_urls = explode(';', $product['image_url']);
                    $first_image = !empty($image_urls[0]) ? $image_urls[0] : 'default.jpg'; 
                    $second_image = !empty($image_urls[1]) ? $image_urls[1] : $first_image; // Nếu không có ảnh thứ 2, dùng ảnh thứ 1
                    ?>
                    <div class="product-card">
                        <div class="productimg">
                            <a href="product.php?id=<?php echo $product['product_id']; ?>">
                                <img src="assets/images/<?php echo htmlspecialchars($first_image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="default-img">
                                <img src="assets/images/<?php echo htmlspecialchars($second_image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="hover-img">
                            </a>
                        </div>
                        <div class="textt">
                            <h2>
                                <a href="product.php?id=<?php echo $product['product_id']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h2>
                            <p>$<?php echo number_format($product['price'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'footer.php'; // Nhúng footer.php ?>
</body>
</html>
<?php
session_start();
include '../db_connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Xử lý xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $image_urls = explode(';', $product['image_url']);
        foreach ($image_urls as $url) {
            if (!empty($url)) {
                $file_path = "../assets/images/" . $url;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
    }

    header("Location: index.php");
    exit();
}

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock_s = $_POST['stock_s'];
    $stock_m = $_POST['stock_m'];
    $stock_l = $_POST['stock_l'];

    if (isset($_FILES['images']) && !empty($_FILES['images']['name'])) {
        $images = $_FILES['images'];
        if (count($images['name']) > 5) {
            die("Tối đa 5 ảnh được phép upload!");
        }

        $image_urls = [];
        $target_dir = "../assets/images/";

        foreach ($images['name'] as $key => $image_name) {
            if ($images['error'][$key] == 0) {
                $new_image_name = time() . '_' . $key . '_' . basename($image_name);
                $target_file = $target_dir . $new_image_name;
                if (move_uploaded_file($images['tmp_name'][$key], $target_file)) {
                    $image_urls[] = $new_image_name;
                }
            }
        }

        $image_url_string = implode(';', $image_urls);
    } else {
        $image_url_string = '';
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, price, stock_s, stock_m, stock_l, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $stock_s, $stock_m, $stock_l, $image_url_string]);
}

// Lấy danh sách sản phẩm
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách đơn hàng
$stmt = $pdo->query("
    SELECT o.order_id, o.user_id, o.total_amount, o.status, o.order_date, u.username AS customer_name
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dimoi Archive</title>
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
                    <li class="active">
                        <a href="#orders" data-tab="orders">
                            <i class="bi bi-cart"></i> Orders
                        </a>
                    </li>
                    <li>
                        <a href="#products" data-tab="products">
                            <i class="bi bi-box"></i> Products
                        </a>
                    </li>
                    <li>
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
            <!-- Orders Tab -->
            <div class="tab-content active" id="orders">
                <h2>Orders Management</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><span class="badge <?php echo $order['status'] === 'Pending' ? 'bg-warning' : 'bg-success'; ?>"><?php echo $order['status']; ?></span></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-order-btn" data-order-id="<?php echo $order['order_id']; ?>" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">View</button>
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Products Tab -->
            <div class="tab-content" id="products">
                <h2>Products Management</h2>
                <button class="btn btn-primary mb-3" id="addProductBtn">
                    <i class="bi bi-plus"></i> Add New Product
                </button>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Images</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock S</th>
                                <th>Stock M</th>
                                <th>Stock L</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $image_urls = explode(';', $product['image_url']);
                                        foreach ($image_urls as $url) {
                                            if (!empty($url)) {
                                                echo '<img src="../assets/images/' . htmlspecialchars($url) . '" alt="Product" class="product-thumb" style="max-width: 50px; margin-right: 5px;">';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['stock_s']; ?></td>
                                    <td><?php echo $product['stock_m']; ?></td>
                                    <td><?php echo $product['stock_l']; ?></td>
                                    <td>
                                        <!-- <button class="btn btn-sm btn-warning">Edit</button>-->
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <input type="hidden" name="delete_product" value="1">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock Size S</label>
                            <input type="number" name="stock_s" class="form-control" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock Size M</label>
                            <input type="number" name="stock_m" class="form-control" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock Size L</label>
                            <input type="number" name="stock_l" class="form-control" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Images (tối đa 5 ảnh)</label>
                            <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                        </div>
                        <input type="hidden" name="add_product" value="1">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addProductForm" class="btn btn-primary">Add Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="order-details-content">
                    <!-- Nội dung chi tiết đơn hàng sẽ được điền bằng AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
        // Xử lý nút View để lấy chi tiết đơn hàng
        document.querySelectorAll('.view-order-btn').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                
                fetch('get_order_details.php?order_id=' + orderId)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        const content = document.getElementById('order-details-content');
                        content.innerHTML = `
                            <p><strong>Order ID:</strong> #${data.order_id}</p>
                            <p><strong>Customer:</strong> ${data.customer_name}</p>
                            <p><strong>Address:</strong> ${data.address || 'Chưa có địa chỉ'}</p>
                            <h6>Products:</h6>
                            <ul>
                                ${data.products.map(item => `
                                    <li>${item.quantity}x ${item.product_name} (Size: ${item.size}) - $${item.price.toFixed(2)}</li>
                                `).join('')}
                            </ul>
                            <p><strong>Total:</strong> $${data.total_amount.toFixed(2)}</p>
                        `;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('order-details-content').innerHTML = `<p>Lỗi khi tải chi tiết đơn hàng: ${error.message}</p>`;
                    });
            });
        });
    </script>
</body>
</html>
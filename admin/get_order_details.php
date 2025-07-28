    <?php
    include '../db_connect.php'; // Kết nối database

    header('Content-Type: application/json'); // Đảm bảo trả về JSON

    if (isset($_GET['order_id'])) {
        $order_id = (int)$_GET['order_id'];

        try {
            // Truy vấn thông tin đơn hàng
            $stmt = $pdo->prepare("
                SELECT o.order_id, o.total_amount, u.username AS customer_name, u.address
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.user_id
                WHERE o.order_id = ?
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                // Truy vấn chi tiết sản phẩm trong đơn hàng, bao gồm size
                $stmt = $pdo->prepare("
                    SELECT od.quantity, p.name AS product_name, od.price, od.size
                    FROM order_details od
                    JOIN products p ON od.product_id = p.product_id
                    WHERE od.order_id = ?
                ");
                $stmt->execute([$order_id]);
                $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Tạo mảng trả về
                $response = [
                    'order_id' => $order['order_id'],
                    'customer_name' => $order['customer_name'] ?? 'Unknown User',
                    'address' => $order['address'] ?? 'No address provided',
                    'total_amount' => (float)$order['total_amount'],
                    'products' => array_map(function($item) {
                        return [
                            'product_name' => $item['product_name'],
                            'quantity' => $item['quantity'],
                            'price' => (float)$item['price'],
                            'size' => $item['size'] ?? 'N/A' // Lấy size từ order_details, mặc định N/A nếu không có
                        ];
                    }, $order_details)
                ];

                echo json_encode($response);
            } else {
                echo json_encode(['error' => 'Order not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Invalid request: No order_id provided']);
    }
    exit();
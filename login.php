<?php
session_start(); // Khởi động session
include 'db_connect.php'; // Kết nối database

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_phone = trim($_POST['email_or_phone']);
    $password = trim($_POST['password']);

    // Truy vấn người dùng từ email hoặc số điện thoại
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$email_or_phone, $email_or_phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Đăng nhập thành công
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Kiểm tra role và chuyển hướng
        if ($user['role'] === 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php"); // Role là 'member' hoặc 'user'
        }
        exit();
    } else {
        $error = "Email/Số điện thoại hoặc mật khẩu không đúng!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Dimoi Archive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style_dangky.css">
    <link rel="stylesheet" href="assets/css/style_cart.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; // Nhúng header.php ?>
    <div class="menu">
        <ul>
            <li class="menu-heading">New Arrival</li>
            <ul>
                <li><a href="cart.php">T Shirt</a></li>
                <li><a href="cart.php">Hoodie</a></li>
                <li><a href="cart.php">Shirt</a></li>
                <li><a href="cart.php">Vest</a></li>
            </ul>
            <li class="menu-heading">Outerwear</li>
            <ul>
                <li><a href="cart.php">Jackets</a></li>
                <li><a href="cart.php">Shackets</a></li>
                <li><a href="cart.php">Bomber</a></li>
            </ul>
            <li class="menu-heading">Bottoms</li>
            <ul>
                <li><a href="cart.php">Jeans</a></li>
                <li><a href="cart.php">Shorts</a></li>
                <li><a href="cart.php">Khaki Pants</a></li>
                <li><a href="cart.php">Skirt</a></li>
            </ul>
        </ul>
    </div>
    <main>
        <div class="contai">
            <h2 class="text-center mb-4">Welcome Back! <3</h2>
            <?php if (isset($error)): ?>
                <p class="text-danger text-center"><?php echo $error; ?></p>
            <?php endif; ?>
            <form id="loginForm" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email_or_phone" class="form-label">Email or Phone number</label>
                    <input type="text" id="email_or_phone" name="email_or_phone" class="form-control" required>
                    <div class="invalid-feedback">please enter your email or phone number</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">Please enter your password.</div>
                </div>
                <div class="authen">
                    <a href="#">Forgot your password?</a>
                    <button type="submit" class="btn btn-outline-dark">Login</button>
                    <a href="dangky.php">Create account</a>
                </div>
            </form>
            <p id="result" class="mt-3"></p>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const toggleIcon = this.querySelector('i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        });

        // Client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            const form = this;
            const resultElement = document.getElementById('result');
            resultElement.textContent = '';
            resultElement.className = '';

            if (!form.checkValidity()) {
                event.preventDefault();
                form.classList.add('was-validated');
            }
        });
    </script>
</body>
</html>
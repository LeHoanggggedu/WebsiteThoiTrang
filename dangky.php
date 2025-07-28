<?php
session_start(); // Khởi động session
include 'db_connect.php'; // Kết nối database

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Kiểm tra email đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $error = "Email đã được đăng ký!";
    } else {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Lưu vào bảng users với role "member"
        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, role) VALUES (?, ?, ?, ?, 'member')");
        try {
            $stmt->execute([$fullname, $email, $phone, $hashed_password]);
            $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
        } catch (PDOException $e) {
            $error = "Đã xảy ra lỗi: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Người Dùng - Dimoi Archive</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style_dangky.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; // Nhúng header.php ?>
    <main>
        <div class="contai">
            <h2 class="text-center mb-4">Đăng Ký Người Dùng</h2>
            <?php if (isset($error)): ?>
                <p class="text-danger text-center"><?php echo $error; ?></p>
            <?php elseif (isset($success)): ?>
                <p class="text-success text-center"><?php echo $success; ?></p>
            <?php endif; ?>
            <form id="registrationForm" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="fullname" class="form-label">Họ và tên (*)</label>
                    <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Nhập họ và tên" required>
                    <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email (*)</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Nhập email" required>
                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại (*)</label>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Nhập số điện thoại" required>
                    <div class="invalid-feedback">Vui lòng nhập số điện thoại hợp lệ.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Đặt mật khẩu (*)</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">Mật khẩu của bạn đang bao gồm dấu tiếng Việt!!!</div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-outline-dark">Đăng Ký</button>
                </div>
            </form>
            <p id="result" class="mt-3"></p>
        </div>
    </main>

    <!-- Link Bootstrap Bundle with Popper -->
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
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            const form = this;
            const resultElement = document.getElementById('result');
            resultElement.textContent = '';
            resultElement.className = '';

            if (!form.checkValidity()) {
                event.preventDefault();
                form.classList.add('was-validated');
                return;
            }

            const phone = document.getElementById('phone').value.trim();
            const password = document.getElementById('password').value.trim();

            // Kiểm tra định dạng số điện thoại
            const phoneRegex = /^[0-9]{10,11}$/;
            if (!phoneRegex.test(phone)) {
                event.preventDefault();
                resultElement.textContent = "Số điện thoại không hợp lệ. Phải từ 10-11 chữ số!";
                resultElement.className = 'text-danger';
                return;
            }

            // Kiểm tra mật khẩu chứa dấu tiếng Việt
            const vietnameseCharRegex = /[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/i;
            if (vietnameseCharRegex.test(password)) {
                event.preventDefault();
                resultElement.textContent = "Mật khẩu không được chứa dấu tiếng Việt!";
                resultElement.className = 'text-danger';
                return;
            }
        });
    </script>
</body>
</html>
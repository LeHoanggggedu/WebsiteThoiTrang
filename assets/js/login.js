
    // Xử lý hiển thị/ẩn mật khẩu
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

    // Xử lý logic kiểm tra form
    function validateForm() {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        const resultElement = document.getElementById('result');

        // Reset lỗi hiển thị
        resultElement.textContent = '';
        resultElement.className = '';

        // Kiểm tra nếu các trường bị bỏ trống
        if (!email || !password) {
            resultElement.textContent = "Vui lòng nhập đầy đủ thông tin!";
            resultElement.className = 'text-danger';
            return;
        }

        // Kiểm tra định dạng email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            resultElement.textContent = "Email không hợp lệ!";
            resultElement.className = 'text-danger';
            return;
        }

        // Tạm thời hiển thị thành công (chưa kiểm tra cơ sở dữ liệu)
        resultElement.textContent = "Đăng nhập thành công (tạm thời)!";
        resultElement.className = 'text-success';
        setTimeout(() => {
            window.location.href = "file:///C:/wamp64/www/php/view_web/index.html";
        }, 200); // Chuyển trang sau 1 giây
    }


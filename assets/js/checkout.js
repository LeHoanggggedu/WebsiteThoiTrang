// Script for checkout functionality

// Kiểm tra email hợp lệ khi người dùng nhập
const emailInput = document.querySelector('input[type="email"]');
emailInput.addEventListener('blur', () => {
    const email = emailInput.value;
    if (!validateEmail(email)) {
        alert('Vui lòng nhập địa chỉ email hợp lệ.');
    }
});

// Hàm kiểm tra email hợp lệ
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Thêm sự kiện khi nhấn nút thanh toán
const orderButton = document.querySelector('.order-button');
if (orderButton) {
    orderButton.addEventListener('click', () => {
        alert('Đơn hàng của bạn đã được đặt thành công!');
    });
}

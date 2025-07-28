// Lấy tất cả các phần tử <a> trong menu
const menuLinks = document.querySelectorAll('.menu nav ul li a');

// Gắn sự kiện click vào từng link
menuLinks.forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault(); // Ngăn chặn hành động mặc định của link
        const newPage = link.getAttribute('href'); // Lấy đường dẫn từ thuộc tính href
        window.location.href = newPage; // Chuyển hướng đến trang mới
    });
});

function updateSubtotal() {
    const totals = document.querySelectorAll('.total');
    let subtotal = 0;
    
    totals.forEach(total => {
        subtotal += parseFloat(total.textContent.replace('$', ''));
    });
    
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
}

document.querySelectorAll(".quantity button").forEach((btn) => {
    btn.addEventListener("click", (e) => {
        const input = e.target.parentNode.querySelector("input");
        let value = parseInt(input.value);

        if (e.target.textContent === "+") {
            value++;
        } else if (e.target.textContent === "-" && value > 1) {
            value--;
        }
        
        input.value = value;

        // Cập nhật tổng giá sản phẩm
        const price = parseFloat(
            e.target.closest("tr").querySelector(".price").textContent.replace("$", "")
        );
        const total = e.target.closest("tr").querySelector(".total");
        total.textContent = `$${(price * value).toFixed(2)}`;

        // Cập nhật tổng giá tiền
        updateSubtotal();
    });
});

// Lắng nghe sự kiện click cho tất cả nút xóa
document.querySelectorAll('.delete').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Lấy thông tin sản phẩm để hiển thị trong thông báo
        const productRow = this.closest('tr');
        const productName = productRow.querySelector('.name').textContent;
        
        // Hiển thị hộp thoại xác nhận
        if (confirm(`Are you sure you want to remove "${productName}" from your cart?`)) {
            // Nếu user nhấn OK, xóa sản phẩm
            productRow.remove();
            // Cập nhật lại tổng tiền
            updateSubtotal();
        }
    });
});

// Cập nhật tổng giá tiền khi tải trang
updateSubtotal();

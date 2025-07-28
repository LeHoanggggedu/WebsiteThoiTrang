document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching
    const tabLinks = document.querySelectorAll('.sidebar nav a[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            tabLinks.forEach(l => l.parentElement.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            link.parentElement.classList.add('active');
            document.getElementById(link.dataset.tab).classList.add('active');
        });
    });

    // Add Product Modal
    const addProductBtn = document.getElementById('addProductBtn');
    const addProductModal = new bootstrap.Modal(document.getElementById('addProductModal'));

    addProductBtn.addEventListener('click', () => {
        addProductModal.show();
    });

    // Delete Product Confirmation
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            if (confirm('Are you sure you want to delete this product?')) {
                console.log('Product deleted');
                // Thêm logic xóa sản phẩm nếu cần
            }
        });
    });

    // Form Submission
    const addProductForm = document.getElementById('addProductForm');
    addProductForm.addEventListener('submit', (e) => {
        console.log('Form submitted');
    });
});
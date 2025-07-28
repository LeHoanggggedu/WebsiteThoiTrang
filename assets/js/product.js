// Lưu trữ ID sản phẩm (ví dụ minh họa)
const productId = 123;

// Cập nhật số lượng sản phẩm
function updateQuantity(change) {
  const quantityInput = document.getElementById("quantity");
  let currentQuantity = parseInt(quantityInput.value, 10);
  currentQuantity = Math.max(1, currentQuantity + change); // Không cho số lượng dưới 1
  quantityInput.value = currentQuantity;
}

// Xử lý chọn size
const sizeButtons = document.querySelectorAll(".size-btn");
sizeButtons.forEach((button) => {
  button.addEventListener("click", () => {
    sizeButtons.forEach((btn) => btn.classList.remove("active")); // Bỏ chọn tất cả
    button.classList.add("active"); // Chọn button hiện tại
  });
});

// Hàm xử lý "Add to Cart"
function addToCart() {
  const selectedSize = document.querySelector(".size-btn.active")?.getAttribute("data-size");
  const quantity = document.getElementById("quantity").value;

  if (!selectedSize) {
    alert("Please select a size!");
    return;
  }

  const cartData = {
    productId,
    size: selectedSize,
    quantity,
  };

  console.log("Add to Cart:", cartData); // Xử lý gửi dữ liệu vào giỏ hàng
  alert("Added to cart!");
}

// Hàm xử lý "Buy It Now"
function buyNow() {
  const selectedSize = document.querySelector(".size-btn.active")?.getAttribute("data-size");
  const quantity = document.getElementById("quantity").value;

  if (!selectedSize) {
    alert("Please select a size!");
    return;
  }

  const checkoutData = {
    productId,
    size: selectedSize,
    quantity,
  };

  console.log("Buy Now:", checkoutData); // Xử lý chuyển hướng đến trang thanh toán
  alert("Proceeding to checkout!");
}

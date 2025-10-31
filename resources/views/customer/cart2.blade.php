@extends('customer.layout.master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/cart/style2.css') }}">
@endsection

@section('content')
    <div class="cart-container" style="margin-top: 120px;">
        <h1 class="cart-title text-black mb-4">Keranjang Belanja</h1>

        <!-- Cart Items Container -->
        <div id="cartItemsContainer">
            <!-- Cart items will be loaded here -->
        </div>

        <!-- Total Price Section -->
        <div id="sumPrice" class="total-section mt-5 p-4">
            <div class="total-content">
                <p id="totalPriceText" class="total-text"
                   style="">Total (Keranjang): Rp0 </p>
                <hr id="garisTotal" class="mb-1 mt-2">
            </div>
        </div>

        <!-- Cart Footer Buttons -->
        <div class="cart-footer mt-5">
            <button id="checkoutBtn" class="btn-checkout"
                    style="display: none;" onclick="checkoutAll()">
                Checkout Semua
            </button>
            <button id="clearCartBtn" class="btn-clear-cart"
                    style="display: none;" onclick="clearCart()">
                Kosongkan Keranjang
            </button>
        </div>

        <!-- Empty Cart Message -->
        <div id="emptyCart" class="empty-cart" style="display: none;">
            <h3>Keranjang Anda Kosong</h3>
            <p>Silakan tambahkan produk ke keranjang untuk melanjutkan belanja</p>
            <a href="{{ route('products') }}" class="continue-shopping">Lanjut Belanja</a>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCartItems();
            setupGlobalEventListeners();
        });

        let cartData = [];

        // Load cart items from session
        async function loadCartItems() {
            try {
                const response = await fetch('/cart-items', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    cartData = data.cart;
                    displayCartItems(cartData);
                    updateTotalPrice();
                } else {
                    showEmptyCart();
                }
            } catch (error) {
                console.error('Error loading cart:', error);
                showEmptyCart();
            }
        }

        // Display cart items
        function displayCartItems(items) {
            const container = document.getElementById('cartItemsContainer');
            const emptyCart = document.getElementById('emptyCart');
            const clearCartBtn = document.getElementById('clearCartBtn');
            const checkoutBtn = document.getElementById('checkoutBtn');

            if (!items || Object.keys(items).length === 0) {
                showEmptyCart();
                return;
            }

            emptyCart.style.display = 'none';
            clearCartBtn.style.display = 'block';
            checkoutBtn.style.display = 'block';
            container.innerHTML = '';

            Object.values(items).forEach(item => {
                const cartItemHTML = createCartItemHTML(item);
                container.appendChild(cartItemHTML);
            });
        }

        // Create cart item HTML element - NEW DESIGN
        function createCartItemHTML(item) {
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item-card';
            cartItem.dataset.itemId = item.id;

            const totalPrice = item.berat_kg * item.harga;

            cartItem.innerHTML = `
                <div class="item-image-wrapper">
                    <img src="${item.gambar}" alt="${item.nama}" class="item-image">
                </div>
                <div class="item-details-wrapper">
                    <h3 class="item-name">${item.nama}</h3>
                    <p class="item-price">Total (Keranjang): Rp${totalPrice.toLocaleString('id-ID')}</p>

                    <div class="item-quantity-section">
                        <span class="quantity-label-text">Jumlah (kg):</span>
                        <div class="quantity-controls-wrapper mt-2">
                            <button type="button" class="btn-qty minus-btn" data-id="${item.id}">−</button>
                            <input type="text" class="qty-input" value="${item.berat_kg}" data-id="${item.id}" readonly>
                            <button type="button" class="btn-qty plus-btn" data-id="${item.id}">+</button>
                        </div>
                    </div>
                    <button class="btn-checkout-item mt-2"
                            onclick="checkoutItem('${item.id}')">Checkout</button>

                </div>
            `;

            return cartItem;
        }

        // Setup global event listeners
        function setupGlobalEventListeners() {
            const container = document.getElementById('cartItemsContainer');

            container.addEventListener('click', function(event) {
                if (event.target.classList.contains('plus-btn')) {
                    const itemId = event.target.dataset.id;
                    updateQuantity(itemId, 0.25);
                } else if (event.target.classList.contains('minus-btn')) {
                    const itemId = event.target.dataset.id;
                    updateQuantity(itemId, -0.25);
                }
            });
        }

        // Update item quantity
        async function updateQuantity(itemId, change) {
            const currentItem = cartData[itemId];
            if (!currentItem) return;

            const newQuantity = parseFloat((currentItem.berat_kg + change).toFixed(2));

            if (newQuantity < 0.25) {
                if (confirm('Hapus item dari keranjang?')) {
                    removeFromCart(itemId);
                }
                return;
            }

            if (newQuantity > 5) {
                alert('Maksimal 5 kg per item');
                return;
            }

            try {
                const response = await fetch('/update-cart-quantity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        berat_kg: newQuantity
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    cartData = data.cart;
                    updateItemDisplay(itemId, newQuantity);
                    updateTotalPrice();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
                alert('Terjadi kesalahan saat mengupdate jumlah');
            }
        }

        // Update item display
        function updateItemDisplay(itemId, newQuantity) {
            const quantityInput = document.querySelector(`input[data-id="${itemId}"]`);
            const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);

            if (quantityInput && cartItem) {
                quantityInput.value = newQuantity;

                const item = cartData[itemId];
                const totalPrice = newQuantity * item.harga;
                const totalElement = cartItem.querySelector('.item-price');
                totalElement.textContent = `Total: Rp${totalPrice.toLocaleString('id-ID')}`;
            }
        }

        // Remove item from cart
        async function removeFromCart(itemId) {
            try {
                const response = await fetch('/remove-from-cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        item_id: itemId
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    cartData = data.cart;
                    loadCartItems();
                    updateTotalPrice();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error removing item:', error);
                alert('Terjadi kesalahan saat menghapus item');
            }
        }

        // Clear entire cart
        async function clearCart() {
            if (!confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang?')) {
                return;
            }

            try {
                const response = await fetch("{{ route('cart.clear') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    cartData = {};
                    showEmptyCart();
                    document.getElementById('clearCartBtn').style.display = 'none';
                    updateTotalPrice();
                    alert('Keranjang berhasil dikosongkan');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error clearing cart:', error);
                alert('Terjadi kesalahan saat mengosongkan keranjang');
            }
        }

        // Checkout functions
        function checkoutItem(itemId) {
            const item = cartData[itemId];
            if (!item) return;
            alert(`Checkout ${item.nama} (${item.berat_kg}kg)`);
        }

        function checkoutAll() {
            if (!cartData || Object.keys(cartData).length === 0) return;

            const barang = Object.values(cartData);
            const barangs = barang.map(item => {
                return {
                    id: item.id,
                    berat_kg: item.berat_kg
                };
            });

            if (confirm(`Checkout semua item?`)) {
                console.log(barangs);
                window.location.href = "{{ route('checkout') }}";
            }
        }

        // Show empty cart message
        function showEmptyCart() {
            document.getElementById('cartItemsContainer').innerHTML = '';
            document.getElementById('emptyCart').style.display = 'block';
            document.getElementById('clearCartBtn').style.display = 'none';
            document.getElementById('checkoutBtn').style.display = 'none';
            document.getElementById('totalPriceText').textContent = "Total (Keranjang): Rp0";
        }

        // Update total price
        function updateTotalPrice() {
            let total = 0;

            Object.values(cartData).forEach(item => {
                total += item.berat_kg * item.harga;
            });

            const totalPriceElement = document.getElementById('totalPriceText');
            if (totalPriceElement) {
                totalPriceElement.textContent = `Total (Keranjang): Rp${total.toLocaleString('id-ID')}`;
            }
        }
    </script>
@endsection

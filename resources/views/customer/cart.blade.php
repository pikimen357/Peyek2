@extends('customer.layout.master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/cart/style.css') }}">

@endsection

@section('content')
    <div class="cart-container p-3">
        <div class="cart-header" >
            <h1 class="cart-title fs-3">Keranjang</h1>
        </div>

        <!-- Cart Items Container -->
        <div id="cartItemsContainer">
            <!-- Cart items will be loaded here -->
        </div>

        <div id="sumPrice" class="mb-5 mt-3">
            <div class="cart-item-content">
                <div class="cart-item-details">
                    <p id="totalPriceText" class="fs-5">Total Belanja : Rp0</p>
                </div>
            </div>
        </div>


        <div class="cart-footer">
                        <!-- Clear Cart Button -->
            <button id="clearCartBtn" class="clear-cart-btn btn btn-danger p-2 w-100"
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
                    updateTotalPrice(); // hitung total setelah load
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

            if (!items || Object.keys(items).length === 0) {
                showEmptyCart();
                return;
            }

            emptyCart.style.display = 'none';
            clearCartBtn.style.display = 'inline-block';
            container.innerHTML = '';

            Object.values(items).forEach(item => {
                const cartItemHTML = createCartItemHTML(item);
                container.appendChild(cartItemHTML);
            });
        }

        // Create cart item HTML element - FIXED
        function createCartItemHTML(item) {
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.dataset.itemId = item.id;

            const totalPrice = item.berat_kg * item.harga;

            cartItem.innerHTML = `
                <div class="cart-item-content">
                    <img src="${item.gambar}" alt="${item.nama}" class="cart-item-image">
                    <div class="cart-item-details">
                        <h3 class="cart-item-name" style="font-size: 16px">${item.nama}</h3>
                        <p class="cart-item-total"  style="font-size: 14px">Total: Rp${totalPrice.toLocaleString('id-ID')}</p>
                        <div class="quantity-controls" >
                            <p class="quantity-label"  style="font-size: 12px">Jumlah (kg):</p>
                            <div class="quantity-input-group">
                                <button type="button" class="btn-quantity minus-btn"
                                        style="font-size: 10px" data-id="${item.id}">−</button>
                                <input type="text" class="quantity-input" value="${item.berat_kg}"
                                        data-id="${item.id}" readonly>
                                <button type="button" class="btn-quantity plus-btn"
                                        data-id="${item.id}">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cart-item-content mt-3 d-flex justify-content-end">
                   <button class="checkout-btn" onclick="checkoutItem('${item.id}')">Checkout</button>
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
                    updateTotalPrice(); // hitung ulang setelah update qty
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
                const totalElement = cartItem.querySelector('.cart-item-total');
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
                    updateTotalPrice(); // hitung ulang setelah hapus
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
                    updateTotalPrice(); // set ke 0
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

        // Checkout specific item
        function checkoutItem(itemId) {
            const item = cartData[itemId];
            if (!item) return;
            alert(`Checkout ${item.nama} - ${item.berat_kg}kg`);
        }

        // Show empty cart message
        function showEmptyCart() {
            document.getElementById('cartItemsContainer').innerHTML = '';
            document.getElementById('emptyCart').style.display = 'block';
            document.getElementById('clearCartBtn').style.display = 'none';
            document.getElementById('totalPriceText').textContent = "Total Belanja : Rp0";
        }

        // Hitung total semua item
        function updateTotalPrice() {
            let total = 0;

            Object.values(cartData).forEach(item => {
                total += item.berat_kg * item.harga;
            });

            const totalPriceElement = document.getElementById('totalPriceText');
            if (totalPriceElement) {
                totalPriceElement.textContent = `Total Belanja : Rp${total.toLocaleString('id-ID')}`;
            }
        }
    </script>
@endsection


@extends('customer.layout.master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/products/style.css') }}">
    <style>
        .ai-badge {
            background: #c8ae7c;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
        }
        .chat-mode-selector {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
        }
        .chat-mode-btn {
            flex: 1;
            padding: 5px 10px;
            border: 1px solid #c8ae7c;
            background: white;
            border-radius: 15px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .chat-mode-btn.active {
            background: #c8ae7c;
            color: white;
        }
    </style>
@endsection

@section('content')
    <main>
    <div class="d-flex justify-content-center align-items-center container" id="orderCont">
      <div class="box-wrapper row gap-1 mt-2" style="width: 370px;">
        <div class="col  mt-2" style="margin-right: 8px;">
          <img src="{{ asset('img_item_upload/' . $defaultItem->gambar) }}"
               class="mt-1" id="topImg" data-id="{{ $defaultItem->id }}" alt="">
        </div>
        <div class="col  mt-2" id="col2">

          <h5 id="Pkacang">{{ $defaultItem->nama_peyek }}</h5>
          <p id="hargaDisplay" style="font-size: 10px;"><strong>Rp{{ number_format($defaultItem->hrg_kiloan, 0, ',', '.') }}/kg
              </strong></p>
          <p id="toping" class="transparent-text">Topping {{ $defaultItem->topping }}</p>
          <label for="jumlah" style="font-size: 10px;">Jumlah (kg):</label>
          <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
            <button type="button" class="btn btn-outline-dark" id="minus"
              style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .5rem;">−</button>
            <input type="text" class="form-control form-control-sm" id="jumlah" value="0.5" readonly>
            <button type="button" class="btn btn-outline-dark" id="plus"
              style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .5rem;">＋</button>
          </div>
          <p class="mt-2" id="harga" value="{{ $defaultItem->hrg_kiloan }}"></p>

            {{-- Hidden untuk id nya--}}
          <input type="hidden" id="idPeyekHidden" name="idPeyek" value="{{ $defaultItem->id }}">
        </div>

        <button type="button" class="btn btn-dark btn-sm mt-2 p-2 mb-2" id="checkout" >
            Keranjang
        </button>

      </div>
    </div>

    <div class="d-flex justify-content-center container p-4 mt-5" id="varCont">
      <div class="row w-100 mb-5" style="max-width: 600px;">
        <h2 id="varianLain" class="mb-4 fw-bold fs-3">Varian Lainnya</h2>
        @foreach($items as $item)
          <div class="col-5 col-md-4 col-lg-3 varian-item p-3 p-md-3 mb-3"
               id="itemsCont"
               style="line-height: 22px; cursor: pointer;"
               data-id="{{ $item->id }}"
               data-nama="{{ $item->nama_peyek }}"
               data-harga="{{ $item->hrg_kiloan }}"
               data-topping="{{ $item->topping }}"
               data-gambar="{{ asset('img_item_upload/' . $item->gambar) }}">

            <div class="varian-content"> <!-- Tambahkan wrapper untuk konten -->
              <img src="{{ asset('img_item_upload/' . $item->gambar) }}" class="Vlimg" alt=""> <!-- Tambahkan w-100 -->
              <h3 class="mt-2 mt-md-3" style="font-size: 13.5px;">{{ $item->nama_peyek }}</h3>
              <p class="mb-1" style="font-size: 12px;">
                <span class="transparent-text">{{ ucfirst($item->topping) }}</span><br>
                Rp{{ number_format($item->hrg_kiloan, 0, ',', '.') }}/kg
              </p>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Chatbot -->
    <div class="chatbot-container">
        <button class="chatbot-button" id="chatbotToggle">
            <i class="fas fa-comments text-white"></i>
        </button>

        <div class="chatbot-modal" id="chatbotModal">
            <div class="chatbot-header">
                <span>CHATBOT <span class="ai-badge">AI</span></span>
                <button class="chatbot-close" id="chatbotClose">&times;</button>
            </div>

            <div class="chatbot-messages" id="chatbotMessages">
                <div class="chat-mode-selector">
                    <button class="chat-mode-btn active" data-mode="ai">🤖 AI Cerdas</button>
                    <button class="chat-mode-btn" data-mode="basic">⚡ Cepat</button>
                </div>
                <div class="message bot">
                    <div class="message-bubble">
                        Halo! Saya asisten AI Peyek Kriuk. Saya bisa membantu dengan pertanyaan kompleks tentang produk, penjualan, dan lainnya!
                        <div class="quick-replies">
                            <div class="quick-reply" data-reply="Apa produk terlaris berdasarkan data penjualan?">Produk Terlaris</div>
                            <div class="quick-reply" data-reply="Berapa lama pesanan biasanya selesai?">Waktu Pesanan</div>
                            <div class="quick-reply" data-reply="Bisa kirim ke daerah mana saja?">Area Pengiriman</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chatbot-input-container">
                <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Tanya apapun tentang peyek...">
                <button class="chatbot-send" id="chatbotSend">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

  </main>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        // Variabel global
        let currentChatMode = 'ai'; // 'ai' atau 'basic'

        const jumlahInput = document.getElementById('jumlah');
        const plusBtn = document.getElementById('plus');
        const minusBtn = document.getElementById('minus');
        const harga = document.getElementById('harga');
        const checkout = document.getElementById('checkout');

        const topImg = document.getElementById('topImg');
        const topTitle = document.getElementById('Pkacang');
        const topHargaDisplay = document.getElementById('hargaDisplay');
        const topTopping = document.getElementById('toping');

        const idPeyekHidden = document.getElementById('idPeyekHidden');

        const varianItems = document.querySelectorAll('.varian-item');

        let jumlah = 0.5;
        let hargaPerKg = {{ $defaultItem->hrg_kiloan }};

        function updateHarga() {
          const total = jumlah * hargaPerKg;
          harga.textContent = `Rp${total.toLocaleString('id-ID')}`;
        }

        plusBtn.addEventListener('click', () => {
          if (jumlah < 5) {
            jumlah = parseFloat((jumlah + 0.25).toFixed(2));
            jumlahInput.value = jumlah;
            updateHarga();
          }
        });

        minusBtn.addEventListener('click', () => {
          if (jumlah > 0.25) {
            jumlah = parseFloat((jumlah - 0.25).toFixed(2));
            jumlahInput.value = jumlah;
            updateHarga();
          }
        });

        updateHarga();

        let selectedItemId = null;

        // Fungsi ganti varian utama saat diklik
        varianItems.forEach(item => {
          item.addEventListener('click', () => {
            const nama = item.getAttribute('data-nama');
            const hargaBaru = parseInt(item.getAttribute('data-harga'));
            const topping = item.getAttribute('data-topping');
            const gambar = item.getAttribute('data-gambar');
            const idPeyek = item.getAttribute('data-id');
            selectedItemId = idPeyek;

            topImg.src = gambar;
            topImg.dataset.id = idPeyek;
            topTitle.textContent = nama;
            topHargaDisplay.innerHTML = `<strong>Rp${hargaBaru.toLocaleString('id-ID')}/kg</strong>`;
            topTopping.textContent = `Toping ${topping}`;

            idPeyekHidden.value = idPeyek;

            hargaPerKg = hargaBaru;
            harga.setAttribute("value", hargaBaru);
            updateHarga();

            // Scroll ke atas dengan efek smooth
            window.scrollTo({
              top: 0,
              behavior: 'smooth'
            });

          });
        });

        // Ganti bagian checkout event listener yang kosong dengan ini:
        checkout.addEventListener('click', async () => {
            // Ambil data item yang sedang dipilih
            const itemId = topImg.dataset.id;
            const beratKg = parseFloat(jumlahInput.value);

            const session_data = {
                item_id : document.getElementById("topImg").dataset.id,
                berat_kg: parseFloat(document.getElementById("jumlah").value)
            }

            // Validasi
            if (!itemId || beratKg <= 0) {
                alert(`Silakan pilih produk dan jumlah yang valid ${itemId} ${beratKg}`);
                return;
            }

            // Disable button sementara
            checkout.disabled = true;
            checkout.innerHTML = 'Loading...';

            try {
                const response = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }, body: JSON.stringify(session_data)
                });

                const data = await response.json();

                console.log(data);

                if (data.status === 'success') {
                    // Tampilkan pesan sukses
                    alert('Produk berhasil ditambahkan ke keranjang!');

                    // Optional: Update cart counter di UI jika ada
                    const cartCounter = document.getElementById('cart-counter');
                    if (cartCounter) {
                        cartCounter.textContent = data.cart_count;
                    }

                    // Optional: Reset jumlah ke default
                    jumlah = 0.5;
                    jumlahInput.value = jumlah;
                    updateHarga();

                    location.reload();

                } else {
                    alert('Error: ' + data.message);
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan ke keranjang');
            } finally {
                // Enable button kembali
                checkout.disabled = false;
                checkout.innerHTML = 'Keranjang';
            }
        });

        // ==================== CHATBOT FUNCTIONALITY ====================

        const chatbotToggle = document.getElementById('chatbotToggle');
        const chatbotModal = document.getElementById('chatbotModal');
        const chatbotClose = document.getElementById('chatbotClose');
        const chatbotMessages = document.getElementById('chatbotMessages');
        const chatbotInput = document.getElementById('chatbotInput');
        const chatbotSend = document.getElementById('chatbotSend');

        // Toggle chatbot
        chatbotToggle.addEventListener('click', () => {
            chatbotModal.style.display = chatbotModal.style.display === 'flex' ? 'none' : 'flex';
            if (chatbotModal.style.display === 'flex') {
                chatbotInput.focus();
            }
        });

        // Close chatbot
        chatbotClose.addEventListener('click', () => {
            chatbotModal.style.display = 'none';
        });

        // Chat mode selector
        document.querySelectorAll('.chat-mode-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.chat-mode-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentChatMode = this.dataset.mode;

                // Tampilkan pesan mode berubah
                const modeMessage = currentChatMode === 'ai'
                    ? 'Mode AI diaktifkan! Saya bisa menjawab pertanyaan kompleks.'
                    : 'Mode cepat diaktifkan! Cocok untuk pertanyaan sederhana.';

                sendBotMessage(modeMessage);
            });
        });

        // Send message function
        function sendMessage(message, isUser = true) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user' : 'bot'}`;

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            bubble.textContent = message;

            messageDiv.appendChild(bubble);
            chatbotMessages.appendChild(messageDiv);

            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

            if (isUser) {
                // Send to server based on current mode
                if (currentChatMode === 'ai') {
                    getAIResponse(message);
                } else {
                    getBasicResponse(message);
                }
            }
        }

        // Get AI response from DeepSeek
        async function getAIResponse(message) {
            try {
                // Show typing indicator
                showTypingIndicator();

                const response = await fetch('{{ route("chatbot.ai") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: message
                    })
                });

                const data = await response.json();

                // Remove typing indicator
                removeTypingIndicator();

                // Send bot response
                setTimeout(() => {
                    if (data.status === 'success') {
                        sendBotMessage(data.message);
                    } else {
                        sendBotMessage('Maaf, terjadi kesalahan. Silakan coba lagi.');
                    }
                }, 5000000);

            } catch (error) {
                console.error('AI Error:', error);
                removeTypingIndicator();
                setTimeout(() => {
                    sendBotMessage('Maaf, AI sedang sibuk. Silakan gunakan mode cepat atau coba lagi nanti.');
                }, 500);
            }
        }

        // Get basic response (rule-based)
        async function getBasicResponse(message) {
            try {
                // Show typing indicator
                showTypingIndicator();

                const response = await fetch('{{ route("chatbot") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: message
                    })
                });

                const data = await response.json();

                // Remove typing indicator
                removeTypingIndicator();

                // Send bot response
                setTimeout(() => {
                    sendBotMessage(data.message, data.products);
                }, 300);

            } catch (error) {
                console.error('Basic Error:', error);
                removeTypingIndicator();
                setTimeout(() => {
                    sendBotMessage('Maaf, terjadi kesalahan. Silakan coba lagi.');
                }, 300);
            }
        }

        // Send bot message with products
        function sendBotMessage(message, products = []) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message bot';

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            bubble.textContent = message;

            // Add products if available
            if (products && products.length > 0) {
                const productsContainer = document.createElement('div');
                productsContainer.className = 'products-container';
                productsContainer.style.cssText = 'margin-top: 10px; max-height: 200px; overflow-y: auto;';

                products.forEach(product => {
                    const productDiv = document.createElement('div');
                    productDiv.style.cssText = 'border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px; margin-bottom: 8px; background: white; cursor: pointer;';
                    productDiv.innerHTML = `
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <img src="${product.gambar}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" alt="${product.nama}">
                            <div>
                                <strong style="font-size: 12px;">${product.nama}</strong><br>
                                <span style="font-size: 11px; color: #666;">${product.topping}</span><br>
                                <span style="font-size: 11px; color: #d4af37; font-weight: bold;">${product.harga}</span>
                            </div>
                        </div>
                    `;

                    // Add click event to select product
                    productDiv.addEventListener('click', () => {
                        selectProduct(product);
                    });

                    productsContainer.appendChild(productDiv);
                });

                bubble.appendChild(productsContainer);
            }

            messageDiv.appendChild(bubble);
            chatbotMessages.appendChild(messageDiv);

            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        // Show typing indicator
        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot typing-indicator';
            typingDiv.innerHTML = `
                <div class="message-bubble">
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `;
            chatbotMessages.appendChild(typingDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        // Remove typing indicator
        function removeTypingIndicator() {
            const typingIndicator = chatbotMessages.querySelector('.typing-indicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        // Select product function
        function selectProduct(product) {
            // Find matching varian-item and trigger click
            const varianItems = document.querySelectorAll('.varian-item');
            varianItems.forEach(item => {
                if (item.getAttribute('data-id') === product.id) {
                    item.click();
                    // Close chatbot and scroll to top
                    chatbotModal.style.display = 'none';
                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    // Send confirmation message
                    setTimeout(() => {
                        chatbotModal.style.display = 'flex';
                        sendBotMessage(`Produk ${product.nama} telah dipilih! Anda bisa mengatur jumlah dan checkout sekarang.`);
                    }, 1000);
                }
            });
        }

        // Send button click
        chatbotSend.addEventListener('click', () => {
            const message = chatbotInput.value.trim();
            if (message) {
                sendMessage(message);
                chatbotInput.value = '';
            }
        });

        // Enter key to send
        chatbotInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                chatbotSend.click();
            }
        });

        // Quick replies
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quick-reply')) {
                const reply = e.target.getAttribute('data-reply');
                sendMessage(reply);
            }
        });

        // Close chatbot when clicking outside
        document.addEventListener('click', (e) => {
            if (!chatbotModal.contains(e.target) && !chatbotToggle.contains(e.target)) {
                chatbotModal.style.display = 'none';
            }
        });

        // Auto-focus input when modal opens
        chatbotModal.addEventListener('click', () => {
            chatbotInput.focus();
        });
    </script>
@endsection

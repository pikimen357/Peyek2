@extends('customer.layout.master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/checkout/style.css') }}">
@endsection

@section('content')
    @php
        $cartItems = session('cart', []);
        $totalHarga = 0;

        if(count($cartItems) > 0) {
            foreach($cartItems as $item) {
                $subtotal = $item['harga'] * $item['berat_kg'];
                $totalHarga += $subtotal;
            }
        }

        $diskon = 3000;
        $ongkir = 5000;
        $TOTAL = $totalHarga + $ongkir - $diskon;
    @endphp

    <!-- Main Content -->
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="order-container p-4">
                    <h2 class="text-center mb-5 mt-3 fs-5 fw-bold">DETAIL PEMESANAN</h2>

                    <!-- Product List Section -->
                    <h4 class="mb-3 fs-6 fw-bold">Produk Dipesan</h4>
                    <div class="product-list-section mb-5 p-2">
                        @foreach($cartItems as $item)
                            @php
                                $subtotal = $item['harga'] * $item['berat_kg'];
                            @endphp
                            <div class="order-card mb-3 rounded p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-3 col-3 mb-2 mb-md-0">
                                        <img src="{{ asset($item['gambar']) }}"
                                             alt="Peyek Kacang"
                                             class="img-fluid rounded product-image w-100">
                                    </div>
                                    <div class="col-md-9 col-9">
                                        <h5 class="product-title mb-1 fs-6">
                                            {{ $item['nama'] }} ({{ $item['berat_kg'] }} kg)
                                        </h5>
                                        <p class="product-description text-muted mb-1">
                                            {{ $item['topping'] }}
                                        </p>
                                        <p class="product-price fw-bold mb-0">
                                            Rp{{ number_format($item['harga'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Form Section -->
                    <div class="form-section mb-5">
                        <h4 class="mb-3 fs-6 fw-bold">Informasi Pengiriman</h4>
                        <form method="POST" action="">
                            <div class="row mb-3 p-2">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="pemesan" class="form-label">Pemesan</label>
                                    <input type="text" class="form-control"
                                           id="pemesan" name="pemesan"
                                           value="Farhan" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="telepon" class="form-label">Telepon (WhatsApp)</label>
                                    <input type="text" class="form-control"
                                           id="telepon" name="telepon"
                                           value="082223190195" required>
                                </div>
                            </div>

                            <div class="row mb-3 p-2">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label" for="kecamatan">Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan" class="form-select" required>
                                        <option value="">Pilih Kecamatan</option>
                                        <option value="Kismantoro">Kismantoro</option>
                                        <option value="Purwantoro">Purwantoro</option>
                                        <option value="Biting">Biting</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="desa">Desa</label>
                                    <select name="desa" id="desa" class="form-select" required>
                                        <option value="">Pilih Desa</option>
                                        <option value="Pucung">Pucung</option>
                                        <option value="Bugelan">Bugelan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 p-2">
                                <label for="alamat" class="form-label">Detail Alamat</label>
                                <textarea class="form-control"
                                          id="alamat" name="alamat" rows="3"
                                          placeholder="Contoh: Jl. Merdeka No. 123, RT 02/RW 05" required></textarea>
                            </div>

                            <div class="mb-4 p-2">
                                <label for="pesan" class="form-label">Pesan Khusus</label>
                                <textarea class="form-control"
                                          id="pesan" name="pesan" rows="3"
                                          placeholder="Contoh: Mohon peyek dikemas rapat"></textarea>
                            </div>

                            <div class="payment-section mb-4">
                                <h4 class="mb-3 fs-6 fw-bold">Metode Pembayaran</h4>
                                <div class="payment-methods">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="radio" name="payment" id="gopay" value="gopay"
                                               checked required>
                                        <label class="form-check-label" for="gopay">
                                            <img src="{{ asset('img_item_upload/gopay.png') }}" alt="GoPay" class="payment-logo">
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="radio" name="payment" id="qris" value="qris">
                                        <label class="form-check-label" for="qris">
                                            <img src="{{ asset('img_item_upload/qris.png') }}" alt="QRIS" class="payment-logo">
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="radio" name="payment" id="cod" value="cod">
                                        <label class="form-check-label" for="cod">
                                            <img src="{{ asset('img_item_upload/cod.png') }}" alt="COD" class="payment-logo">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Price Summary Section -->
                    <div class="price-summary-section mb-4">
                        <h4 class="mb-3 fs-6 fw-bold">Ringkasan Pembayaran</h4>
                        <div class="price-summary p-3 rounded">
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-2">Harga Total: <span class="fw-semibold">
                                            Rp{{  number_format($totalHarga, 0, ',', '.') }}</span></p>
                                    <p class="mb-2">Ongkir: <span class="fw-semibold">
                                            Rp{{  number_format($ongkir, 0, ',', '.') }}</span></p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="fst-italic mb-2 text-success">
                                        Diskon: Rp{{  number_format($diskon, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="total-price mt-3 pt-3 border-top">
                                <h5 class="text-center mb-0 fw-bold">
                                    Total: Rp{{  number_format($TOTAL, 0, ',', '.') }}
                                </h5>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="action-section">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg" id="beli">
                                Beli Sekarang
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
<script>
    // Optional: Dynamic Desa based on Kecamatan
    document.getElementById('kecamatan').addEventListener('change', function() {
        const desaSelect = document.getElementById('desa');
        const kecamatan = this.value;

        // Clear current options
        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

        // Define desa options per kecamatan
        const desaOptions = {
            'Kismantoro': ['Pucung', 'Wonorejo', 'Kismantoro'],
            'Purwantoro': ['Bugelan', 'Purwantoro', 'Girimargo'],
            'Biting': ['Biting', 'Sidoharjo', 'Karangsari']
        };

        // Populate desa options
        if (desaOptions[kecamatan]) {
            desaOptions[kecamatan].forEach(desa => {
                const option = document.createElement('option');
                option.value = desa;
                option.textContent = desa;
                desaSelect.appendChild(option);
            });
        }
    });
</script>
@endsection

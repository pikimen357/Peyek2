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
                    <h2 id="detailPemesanan" class="text-center mb-5 mt-3 fs-5 fw-bold">
                        DETAIL PEMESANAN</h2>

                    <!-- Product List Section -->
                    <h4 class="mb-3 fs-6 fw-bold">Produk Dipesan</h4>
                    <div class="product-list-section mb-5 p-2">
                        @foreach($items as $item)
                            @php
                                $subtotal = $item['harga'] * $item['berat_kg'];
                            @endphp
                            <div class="order-card mb-3 rounded p-3 ">
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
                                            Rp{{ number_format($item['harga'] * $item['berat_kg'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Form Section -->
                    <div class="form-section mb-5">
                        <h4 class="mb-3 fs-6 fw-bold">Informasi Pengiriman</h4>
                        <form method="POST" action="{{ route('checkout.store') }}">
                            @csrf
                            <div class="row mb-3 p-2">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="pemesan" class="form-label @error('nama') is-invalid @enderror">
                                        Pemesan</label>
                                    <input type="text" class="form-control"
                                           id="pemesan" name="nama"
                                           value="{{ old('nama') }}" required>
                                            @error('nama')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="telepon" class="form-label @error('telepon') is-invalid @enderror">
                                        Telepon (WhatsApp)</label>
                                    <input type="text" class="form-control"
                                           id="telepon" name="telepon"
                                           value="{{ old('telepon') }}" required>
                                            @error('nama')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                </div>
                            </div>

                            <div class="row mb-3 p-2">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label" for="kecamatan">Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan" class="form-select" required>
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach($locations->groupBy('kecamatan') as $kec => $group)
                                            <option value="{{ $kec }}">{{ $kec }}</option>
                                        @endforeach
                                    </select>
                                     @error('kecamatan')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                     @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="desa">Desa</label>
                                    <select name="desa" id="desa" class="form-select" required>
                                        <option value="">Pilih Desa</option>
{{--                                        @foreach($locations->groupBy('desa') as $des => $des)--}}
{{--                                                <option value="{{ $des }}">{{ $des }}</option>--}}
{{--                                        @endforeach--}}
                                    </select>
                                     @error('desa')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                     @enderror
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
                                          id="pesan" name="catatan" rows="3"
                                          placeholder="Contoh: Mohon peyek dikemas rapat"></textarea>
                            </div>

                            <div class="payment-section mb-4">
                                <h4 class="mb-3 fs-6 fw-bold">Metode Pembayaran</h4>
                                <div class="payment-methods">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="radio" name="payment_method" id="gopay" value="gopay"
                                               checked required>
                                        <label class="form-check-label" for="gopay">
                                            <img src="{{ asset('img_item_upload/gopay.png') }}" alt="GoPay" class="payment-logo">
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="radio" name="payment_method" id="qris" value="qris">
                                        <label class="form-check-label" for="qris">
                                            <img src="{{ asset('img_item_upload/qris.png') }}" alt="QRIS" class="payment-logo">
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="radio" name="payment_method" id="cod" value="cash">
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

@section('script')
<script>
    const locations = <?php echo json_encode($locations); ?>;

    console.log('locations  : ', locations);

    const kecamatanSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');

    // PERBAIKAN: Tambahkan quotes dan handle null value
    const oldKecamatan = '<?php echo old('kecamatan', '') ?>';
    const oldDesa = '<?php echo old('desa', '') ?>';

    console.log('Old Kecamatan:', oldKecamatan);
    console.log('Old Desa:', oldDesa);

    // Jika ada old value untuk kecamatan, set selected
    if(oldKecamatan){
        kecamatanSelect.value = oldKecamatan;

        // Trigger change event untuk memuat data
        kecamatanSelect.dispatchEvent(new Event('change'));

        // set old value untuk desa setelah dropdown terisi
        setTimeout(() =>{
            if(oldDesa) {
                desaSelect.value = oldDesa;
            }
        }, 100)
    }

    if (!kecamatanSelect || !desaSelect) {
        console.error('Dropdown elements not found!');
    }

    kecamatanSelect.addEventListener('change', function() {
        const selectedKecamatan = this.value;

        // Reset dropdown desa
        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
        desaSelect.disabled = true;

        if (!selectedKecamatan) {
            return;
        }

        // Filter dan tampilkan desa
        const filteredDesa = locations.filter(loc => {
            return loc.kecamatan === selectedKecamatan;
        });

        // Hilangkan duplikat desa (jika ada)
        const uniqueDesa = [...new Set(filteredDesa.map(location => location.desa))];

            // Populate dropdown desa
        uniqueDesa.forEach(desa => {
            const option = document.createElement('option');
            option.value = desa;
            option.textContent = desa;
            desaSelect.appendChild(option);
        });

        desaSelect.disabled = false;
    });
</script>
@endsection


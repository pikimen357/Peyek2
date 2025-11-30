@php use Illuminate\Support\Facades\Auth; @endphp
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
                                    <label for="pemesan" class="form-label">Pemesan</label>
                                    <input type="text" class="form-control {{ Auth::check() ? 'bg-light' : '' }}"
                                           id="pemesan" name="nama"
                                           value="{{ Auth::check() ? Auth::user()->nama : old('nama') }}"
                                           {{ Auth::check() ? 'readonly' : '' }}
                                           required>
                                    @error('nama')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="telepon" class="form-label">Telepon (WhatsApp)</label>
                                    <input type="text" class="form-control {{ Auth::check() ? 'bg-light' : '' }}"
                                           id="telepon" name="telepon"
                                           value="{{ Auth::check() ? Auth::user()->telepon : old('telepon') }}"
                                           {{ Auth::check() ? 'readonly' : '' }}
                                           required>
                                    @error('telepon')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3 p-2">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label" for="kecamatan">Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan"
                                            class="form-select"
                                            required>
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach($locations->groupBy('kecamatan') as $kec => $group)
                                            <option value="{{ $kec }}"
                                                {{ old('kecamatan') == $kec ? 'selected' : '' }}>
                                                {{ $kec }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('kecamatan')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="desa">Desa</label>
                                    <select name="desa" id="desa"
                                            class="form-select"
                                            required>
                                        <option value="">Pilih Desa</option>
                                    </select>

                                    @error('desa')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 p-2">
                                <label for="alamat" class="form-label">Detail Alamat</label>
                                <textarea class="form-control"
                                          id="alamat" name="alamat" rows="3"
                                          placeholder="Contoh: Jl. Merdeka No. 123, RT 02/RW 05"
                                          required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4 p-2">
                                <label for="pesan" class="form-label">Pesan Khusus</label>
                                <textarea class="form-control"
                                          id="pesan" name="catatan" rows="3"
                                          placeholder="Contoh: Mohon peyek dikemas rapat">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="payment-section mb-4">
                                <h4 class="mb-3 fs-6 fw-bold">Metode Pembayaran</h4>
                                <div class="payment-methods">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="radio" name="payment_method" id="gopay" value="gopay"
                                               {{ old('payment_method', 'gopay') == 'gopay' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="gopay">
                                            <img src="{{ asset('img_item_upload/gopay.png') }}" alt="GoPay" class="payment-logo">
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="radio" name="payment_method" id="qris" value="qris"
                                               {{ old('payment_method') == 'qris' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="qris">
                                            <img src="{{ asset('img_item_upload/qris.png') }}" alt="QRIS" class="payment-logo">
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input"
                                               type="radio" name="payment_method" id="cod" value="cash"
                                               {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cod">
                                            <img src="{{ asset('img_item_upload/cod.png') }}" alt="COD" class="payment-logo">
                                        </label>
                                    </div>
                                </div>
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

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
<script>
    const locations = @json($locations);
    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    const userLocation = @json($userLocation ?? null);
    const userAlamat = '{{ Auth::check() && $user ? $user->alamat : '' }}';

    const kecamatanSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');
    const alamatTextarea = document.getElementById('alamat');
    const useOtherAddressCheckbox = document.getElementById('useOtherAddress');

    const oldKecamatan = '{{ old('kecamatan', '') }}';
    const oldDesa = '{{ old('desa', '') }}';
    const oldUseOtherAddress = {{ old('use_other_address') ? 'true' : 'false' }};

    // Function untuk populate desa
    function populateDesa(selectedKecamatan, selectedDesa = null) {
        desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

        if (!selectedKecamatan) {
            desaSelect.disabled = true;
            return;
        }

        const filteredDesa = locations.filter(loc => loc.kecamatan === selectedKecamatan);
        const uniqueDesa = [...new Set(filteredDesa.map(location => location.desa))];

        uniqueDesa.forEach(desa => {
            const option = document.createElement('option');
            option.value = desa;
            option.textContent = desa;
            if(selectedDesa && desa === selectedDesa) {
                option.selected = true;
            }
            desaSelect.appendChild(option);
        });

        desaSelect.disabled = false;
    }

    // Function untuk toggle address fields
    function toggleAddressFields(useOtherAddress) {
        if (useOtherAddress) {
            // Reset ke kosong untuk alamat lain
            kecamatanSelect.value = '';
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';
            // desaSelect.disabled = true;
            // kecamatanSelect.disabled = true;

            alamatTextarea.value = '';

            kecamatanSelect.classList.remove('bg-light');
            desaSelect.classList.remove('bg-light');
            alamatTextarea.classList.remove('bg-light');
        } else {
            // Isi dengan data user
            if (isLoggedIn && userLocation) {
                kecamatanSelect.value = userLocation.kecamatan;
                populateDesa(userLocation.kecamatan, userLocation.desa);
                alamatTextarea.value = userAlamat;

                // kecamatanSelect.disabled = true;
                // desaSelect.disabled = true;
                // alamatTextarea.disabled = true;

                kecamatanSelect.classList.add('bg-light');
                desaSelect.classList.add('bg-light');
                alamatTextarea.classList.add('bg-light');
            }
        }
    }

    // Event listener untuk checkbox
    if (useOtherAddressCheckbox) {
        useOtherAddressCheckbox.addEventListener('change', function() {
            toggleAddressFields(this.checked);
        });

        // Initial state
        if (oldUseOtherAddress || oldKecamatan) {
            // Jika ada old value, berarti validation error
            useOtherAddressCheckbox.checked = true;
            toggleAddressFields(true);
            if (oldKecamatan) {
                kecamatanSelect.value = oldKecamatan;
                populateDesa(oldKecamatan, oldDesa);
            }
        } else if (isLoggedIn && userLocation) {
            // Default: gunakan alamat user
            toggleAddressFields(false);
        }
    } else {
        // User belum login
        if(oldKecamatan){
            kecamatanSelect.value = oldKecamatan;
            populateDesa(oldKecamatan, oldDesa);
        }
    }

    // Event listener untuk perubahan kecamatan
    kecamatanSelect.addEventListener('change', function() {
        populateDesa(this.value);
    });
</script>
@endsection

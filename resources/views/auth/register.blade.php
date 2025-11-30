@extends('customer.layout.master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/register/style.css') }}">
@endsection

@section('content')
  <div id="signup" class="container p-3 fw-bold fs-6" >

        <h4 class="mb-4 text-xl fw-bold text-center mt-3" id="buatAkun">Buat Akun</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="p-3" id="form-signup">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" name="nama" id="nama" required class="form-control" value="{{ old('nama') }}">
                    @error('nama')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input type="text" name="telepon" id="telepon" required class="form-control" value="{{ old('telepon') }}">
                    @error('telepon')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" name="password" id="password" required class="form-control">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password_confirmation">Konfirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label" for="kecamatan">Kecamatan</label>
                    <select name="kecamatan" id="kecamatan" onchange="filterDesa()" class="form-select" required>
                        <option value="">Pilih Kecamatan</option>
                        @php
                            $kecamatanList = $locations->pluck('kecamatan')->unique()->sort()->values();
                        @endphp
                        @foreach($kecamatanList as $kec)
                            <option value="{{ $kec }}" {{ old('kecamatan') == $kec ? 'selected' : '' }}>
                                {{ $kec }}
                            </option>
                        @endforeach
                    </select>
                    @error('kecamatan')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="desa">Desa</label>
                    <select name="desa" id="desa" class="form-select" required>
                        <option value="">Pilih Desa</option>
                    </select>
                    @error('desa')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Detail Alamat</label>
                <textarea name="alamat" id="alamat" required class="form-control" rows="3">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="d-flex justify-content-center mt-3">
                <div class="p-2">
                    <button type="submit" id="btn-daftar" class="btn text-white" style="width: 112px;">
                        Buat Akun
                    </button>
                </div>
                <div class="p-2">
                    <a href="{{ route('landing') }}" class="btn text-white" id="btn-batal" style="width: 112px;">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        // Data lokasi dari server
        const lokasi = @json($locations);

        function filterDesa() {
            const kecamatan = document.getElementById('kecamatan').value;
            const desaSelect = document.getElementById('desa');

            // Reset desa dropdown
            desaSelect.innerHTML = '<option value="">Pilih Desa</option>';

            // Filter desa berdasarkan kecamatan yang dipilih
            const filteredDesa = lokasi
                .filter(lok => lok.kecamatan === kecamatan)
                .map(lok => lok.desa)
                .sort();

            // Tambahkan option desa
            filteredDesa.forEach(desa => {
                const opt = document.createElement('option');
                opt.value = desa;
                opt.textContent = desa;
                desaSelect.appendChild(opt);
            });
        }

        // Jika ada old value untuk desa (ketika validation error)
        @if(old('kecamatan') && old('desa'))
            document.addEventListener('DOMContentLoaded', function() {
                filterDesa();
                document.getElementById('desa').value = "{{ old('desa') }}";
            });
        @endif
    </script>
@endsection

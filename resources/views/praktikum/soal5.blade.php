@extends('customer.layout.master')

@section('content')
<div class="container" style="margin-top: 200px;">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header text-white" style="background-color: #c29948;">
                    <h4 class="mb-0">Pencarian Desa</h4>
                </div>
                <div class="card-body">
                    {{-- Form pencarian --}}
                    <form method="GET" action="{{ route('location.search') }}">
                        <div class="input-group mb-3">
                            <input type="text" name="desa" class="form-control"
                                   placeholder="Masukkan nama desa..."
                                   value="{{ request('desa') }}">
                            <button class="btn" type="submit" style="background-color: #c29948;">
                                <p class="text-white">Cari</p>
                            </button>
                        </div>
                    </form>

                    {{-- Hasil pencarian --}}
                    @if(isset($desanya) && $desanya->count() > 0)
                        <div class="list-group">
                            @foreach($desanya as $index => $lokasi)
                                <div class="list-group-item list-group-item-action mb-2 shadow-sm rounded">
                                    <h5 class="mb-1">
                                        {{ $index + 1 }}. {{ $lokasi->desa }}
                                    </h5>
                                    <p class="mb-1">
                                        <strong>Kecamatan:</strong> {{ $lokasi->kecamatan }} <br>
                                        <strong>Jarak:</strong> {{ $lokasi->jarak }} km
                                    </p>
                                    <small class="text-muted">ID: {{ $lokasi->id }}</small>
                                </div>
                            @endforeach
                        </div>
                    @elseif(request()->has('desa'))
                        <div class="alert alert-warning">
                            Tidak ada desa ditemukan dengan kata kunci: <b>{{ request('desa') }}</b>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

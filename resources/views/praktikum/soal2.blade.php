@extends('customer.layout.master')

@section('content')
    <div class="locations-container p-5" style="margin-top: 130px;">
        <h2>Daftar Lokasi Terbaru</h2>

        <div class="row">
            @foreach($locations as $location)
                <div class="col-md-4 mb-4">
                    <div class="card location-card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $location->desa }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $location->kecamatan }}</h6>
                            <p class="card-text">
                                <strong>Jarak:</strong> {{ $location->jarak }} km<br>
                                <strong>Kode:</strong> {{ $location->id }}
                            </p>
                            <small class="text-muted">
                                Ditambahkan: {{ $location->created_at->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('styles')
<style>

    .location-card {
        transition: transform 0.3s ease;
        border: 1px solid #e0e0e0;
    }
    .location-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .card-title {
        color: #2c5282;
        font-weight: 600;
    }
</style>
@endsection

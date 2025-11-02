@extends('customer.layout.master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/history/style.css') }}">
@endsection

@section('content')
    <!-- Main Content -->
    <main class="d-flex justify-content-center container pt-5" style="margin-top: 90px;">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h2 class="mb-4 fw-bold">Daftar Pesanan</h2>

                <!-- Order Cards -->
                <div class="order-list mb-5">
                    @if($orders && count($orders) > 0)
                        @foreach($orders as $order)
                            <div class="order-card mb-4 rounded">
                                <div class="row align-items-center">
                                    <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
                                        <img src="{{ !empty($order->item->gambar) ? asset('img_item_upload/' . $order->item->gambar)
                                                : asset('img_item_upload/kacang.png') }}"

                                             alt="{{ $order->item->nama_peyek }}"
                                             class="img-fluid rounded w-25" >
                                    </div>
                                    <div class="col-md-7 col-sm-9">
                                        <h5 class="order-title">
                                            {{ $order->item->nama_peyek }}
                                            ({{ $order->jumlah_kg }} kg)
                                        </h5>
                                        <p class="order-date mb-1">
                                            Tanggal: {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}
                                        </p>
                                        <p class="order-price mb-1">
                                            Total: Rp{{ number_format($order->total_harga, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="col-md-3 mt-3 mt-md-0 text-md-end">
                                        <button type="button" class="btn {{ $order->order->status == 'selesai' ? 'btn-success' : ($order->order->status == 'belum bayar' ? 'btn-warning' : 'btn-secondary') }} btn-sm text-white fw-bold p-2 rounded" style="width: 110px;" onclick="lihatDetail({{ $order->id_order }})">
                                            {{ ucfirst($order->order->status) }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info" role="alert">
                            <h4 class="alert-heading">Tidak ada pesanan</h4>
                            <p>Anda belum memiliki pesanan apapun. Silakan mulai berbelanja untuk melihat riwayat pesanan Anda di sini.</p>
                            <hr>
                            <p class="mb-0">
                                <a href="{{ route('landing') }}" class="btn btn-primary">Mulai Belanja</a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
@endsection

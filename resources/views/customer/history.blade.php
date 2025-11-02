@extends('customer.layout.master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/history/style.css') }}">
@endsection

@section('content')
    <!-- Main Content -->
    <main class="d-flex justify-content-center container pt-4" style="margin-top: 90px;">
        <div class="row justify-content-center">
            <div class="col-md-10 p-4">
                <h1 class="mb-4 fw-bold">Daftar Pesanan</h1>

                {{-- Memilih Status--}}
                <div class="mt-3 mb-4 w-50">
                    <label for="statusSelect" class="form-labe">
                        <p class="fs-6 fst-italic mb-0">Status</p>
                    </label>
                    <select name="status" id="statusSelect" class="form-select">
                        <option value="belum bayar" selected>Belum Bayar</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <!-- Order Cards -->
                <div class="order-list mb-5">
                    @if($orders && count($orders) > 0)
                        @foreach($orders as $order)
                            <div class="order-card mb-3 rounded">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="{{ !empty($order->item->gambar) ? asset('img_item_upload/' . $order->item->gambar)
                                                : asset('img_item_upload/kacang.png') }}"
                                             alt="{{ $order->item->nama_peyek }}"
                                             class="order-image rounded">
                                    </div>
                                    <div class="col">
                                        <h5 class="order-title mb-1">{{ $order->item->nama_peyek }}</h5>
                                        <p class="order-date mb-1">Dipesan pada: {{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') }}</p>
                                        <p class="order-price mb-0">Total: Rp{{ number_format($order->total_harga, 0, ',', '.') }}</p>
                                        <p class="order-status mb-0">
                                            Status:
                                            <span class="p-1 badge @switch($order->order->status)
                                                    @case('selesai') badge-selesai @break
                                                    @case('diproses') badge-diproses @break
                                                    @case('belum bayar') badge-unpaid @break
                                                    @default badge-default
                                                @endswitch">
                                                {{ $order->order->status }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-auto">
                                        @if($order->order->status == 'belum bayar' )
                                            <button type="button" class="btn btn-bayar"
                                                    id="btnBayar"
                                                    onclick="alert('Bayar Rp:{{ number_format($order->total_harga, 0, ',', '.') }}?')">
    {{--                                                onclick="lihatDetail({{ $order->id_order }})">--}}
    {{--                                            {{ $order->order->status == 'belum bayar' ? 'Bayar' : 'Bayar' }}--}}
                                                    Bayar
                                            </button>
                                        @elseif($order->order->status == 'selesai')
                                           <button type="button" class="btn btn-review"
                                                    onclick="alert('Review {{ $order->item->nama_peyek }}')">
                                                    Review
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-secondary" role="alert">
                            <h4 class="alert-heading">Tidak ada pesanan</h4>
                            <p>Anda belum memiliki pesanan apapun. Silakan mulai berbelanja untuk melihat riwayat pesanan Anda di sini.</p>
                            <hr>
                            <p class="mb-0">
                                <a href="{{ route('products') }}" class="btn btn-primary">Mulai Belanja</a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default value ke "belum bayar"
    const statusSelect = document.getElementById('statusSelect');
    statusSelect.value = 'belum bayar';

    // Langsung filter order yang "belum bayar"
    filterOrders('belum bayar');

    // ... kode JavaScript filter yang sebelumnya
    statusSelect.addEventListener('change', function() {
        const selectedStatus = this.value;
        filterOrders(selectedStatus);
    });

    function filterOrders(status) {
        const orderCards = document.querySelectorAll('.order-card');
        let hasVisibleOrders = false;

        orderCards.forEach(card => {
            const statusElement = card.querySelector('.order-status');
            const statusText = statusElement.textContent.toLowerCase();

            if (status === '' || statusText.includes(status)) {
                card.style.display = 'flex';
                hasVisibleOrders = true;
            } else {
                card.style.display = 'none';
            }
        });

        if (!hasVisibleOrders) {
            showNoOrdersMessage(status);
        }
    }

    function showNoOrdersMessage(status) {
        // ... kode show message
    }
});
</script>
@endsection

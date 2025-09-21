@extends('customer.layout.master')

@section('content')
<div class="container" style="margin-top: 120px;">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Statistik Data Item</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Jumlah Item</th>
                            <td>{{ $count }}</td>
                        </tr>
                        <tr>
                            <th>Total Harga</th>
                            <td>Rp {{ number_format($total_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Harga Tertinggi</th>
                            <td>Rp {{ number_format($max_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Harga Terendah</th>
                            <td>Rp {{ number_format($min_price, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

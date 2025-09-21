@extends('customer.layout.master')

@section('content')
<div class="container-fluid" style="margin-top: 130px;">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Filter Berdasarkan Kecamatan</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('location.filter') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kecamatan">Pilih Kecamatan:</label>
                                    <select class="form-control" id="kecamatan" name="kecamatan" onchange="this.form.submit()">
                                        <option value="semua" {{ $selectedKecamatan == 'semua' ? 'selected' : '' }}>Semua Kecamatan</option>
                                        @foreach($allKecamatan as $kec)
                                            <option value="{{ $kec }}" {{ $selectedKecamatan == $kec ? 'selected' : '' }}>
                                                {{ $kec }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Desa</h4>
                    <p class="card-category">Menampilkan {{ $locations->count() }} desa</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="text-primary">
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Jarak (km)</th>
                                    <th>Tanggal Ditambahkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($locations as $index => $location)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $location->id }}</td>
                                        <td>{{ $location->desa }}</td>
                                        <td>{{ $location->kecamatan }}</td>
                                        <td>{{ $location->jarak }}</td>
                                        <td>{{ $location->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Optional: Auto-submit when selection changes
    document.getElementById('kecamatan').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endsection

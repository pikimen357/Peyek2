@extends('customer.layout.master')

@section('content')
    <main >
        <div class="container mb-2 p-4">
            <h1 class="fw-bold mt-4">Peyek Kriuk Pawon Eny</h1>
            <p class="transparent-text">Rempeyek renyah dipadukan dengan citarasa <br>khas bumbu daerah yang menggugah
                selera.</p>
            <a href="{{ route('products') }}" id="pesan1" class="pesan btn btn-dark">Pesan Sekarang</a>
        </div>

        <img src="{{ asset('img_item_upload/pkacang.png') }}" class="p-4" id="topImg" alt="">

        <div class="container mb-3 p-4">
            <h2 class="fw-bold mt-4 mb-3">Varian rasa nabati</h2>
            <div class="row" id="varian">
                <div class="col">
                    <img src="{{ asset('img_item_upload/kedelai.png') }}" class="" alt="">
                    <h3 class="mt-3">Peyek Kedelai</h3>
                    <p class="transparent-text">Varian toping paling banyak dipesan karena citarasa kedelai lokal yang
                        gurih dan renyah</p>
                </div>
                <div class="col">
                    <img src="{{ asset('img_item_upload/kacang.png') }}" class="" alt="">
                    <h3 class="mt-3">Peyek Kacang</h3>
                    <p class="transparent-text">Peyek dengan perpaduan khas antara bumbu dengan topping kacang yang
                        gurih membuat anda ketagihan</p>
                </div>
            </div>
            <a href="{{ route('products') }}" id="pesan2" class="btn btn-dark">Pesan</a>
        </div>

        <div class="container mb-5 p-4">
            <h2 class="fw-bold mt-4 mb-3">Varian rasa hewani</h2>
            <div class="row align-items-center">
                <!-- Kolom Kiri (teks) -->
                <div class="col-md-6">
                    <h3 class=" ">Toping Udang rebon</h3>
                    <p class="text-muted">
                        Citarasa asin gurih yang dihasilkan dari udang rebon akan membuat lidah terasa bergoyang dengan
                        rasanya
                    </p>

                    <h3 class=" mt-4">Toping Teri</h3>
                    <p class="text-muted">
                        Ikan Teri yang gurih merupakan kombinasi yang lezat ketika dipadukan dengan bumbu tradisional.
                    </p>

                </div>

                <!-- Kolom Kanan (gambar) -->
                <div class="col-md-6 text-begin" id="imgRebon">
                    <img src="{{ asset('img_item_upload/rebon.png') }}" alt="Peyek Udang & Teri"
                         class="img-fluid rounded">
                </div>

            </div>
            <a href="{{ route('products') }}" id="pesan3" class="btn btn-dark mt-3">Pesan</a>
        </div>
        <div class="container mb-5 p-4">
            <h4 class="fw-bold mb-4">Review Pemesan</h4>
            <div class="row g-3">

                <!-- Review 1 -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100 position-relative">
                        <!-- Bintang -->
                        <span class="position-absolute top-0 end-0 p-2">
                            ⭐⭐⭐
                        </span>
                        <p class="fw-semibold">“Enak Banget Bikin Nagih”</p>
                        <div class="d-flex align-items-center mt-3">
                            <img src="{{ asset('img_item_upload/profile.png') }}" alt="Foto Farhan"
                                 class="rounded-circle me-2" width="40"
                                 height="40">
                            <div>
                                <p class="mb-0 fw-bold">Farhan</p>
                                <small class="text-muted">Jakarta</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review 2 -->
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100 position-relative">
                        <!-- Bintang -->
                        <span class="position-absolute top-0 end-0 p-2">
                            ⭐⭐
                        </span>
                        <p class="fw-semibold">“Langsung dihabisin suamiku dong”</p>
                        <div class="d-flex align-items-center mt-3">
                            <img src="{{ asset('img_item_upload/profile.png') }}" alt="Foto Indah"
                                 class="rounded-circle me-2" width="40"
                                 height="40">
                            <div>
                                <p class="mb-0 fw-bold">Indah</p>
                                <small class="text-muted">Solo</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

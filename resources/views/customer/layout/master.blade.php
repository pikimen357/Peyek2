@include('customer.layout.__header')

<body class="d-flex flex-column min-vh-100">
@include('customer.layout.__navbar')

<nav class="sub-navbar fixed-top">
    <div class="container">
        <ul class="nav justify-content-evenly">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('products') }}">
                    Daftar Produk</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Pesanan Anda</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('cart2.show') }}" class="nav-link position-relative text-white bi bi-cart fs-4">
                    @php
                        $cart = session('cart');
                        $cartCount = is_array($cart) ? count($cart) : 0;
                    @endphp

                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle
                                    badge rounded-pill bg-danger w-5">
                        <span style="font-size: 11px;">{{ $cartCount }}</span>
                        <span class="visually-hidden">items in cart</span>
                        </span>
                    @endif
                </a>
            </li>
        </ul>
    </div>
</nav>

@yield('content')

@include('customer.layout.__footer')

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js"
        integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D"
        crossorigin="anonymous"></script>

@yield('script')
</body>

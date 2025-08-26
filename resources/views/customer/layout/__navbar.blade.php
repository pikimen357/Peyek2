    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="{{ route('landing') }}">Peyek Kriuk</a>
            <div class="ms-auto">
{{--                If user login show user name--}}

{{--                <?php if ($user) : ?>--}}
{{--                    <a class="nav-link d-inline text-white" href="#">Login</a>--}}
{{--                    <a class="nav-link d-inline text-white fw-bold" href="#">--}}
{{--                        <?= strtoupper(htmlspecialchars($user['nama'])) ?>--}}
{{--                    </a>--}}
{{--                <?php else : ?>--}}

                    <a class="nav-link d-inline text-white me-3" href="#">Daftar</a>
                    <a class="nav-link d-inline text-white" href="#">Login</a>
{{--                <?php endif; ?>--}}
            </div>
        </div>
    </nav>

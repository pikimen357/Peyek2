@extends('customer.layout.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/products/style.css') }}">
@endsection

@section('content')
    <main>
    <div class="d-flex justify-content-center align-items-center container" id="orderCont">
      <div class="box-wrapper row gap-1 mt-2" style="width: 380px;">
        <div class="col  mt-2" style="margin-right: 8px;">
          <img src="{{ asset('img_item_upload/pkacang.png') }}" class="mt-1" id="topImg" data-id="pkcg" alt="">
        </div>
        <div class="col  mt-2" id="col2">
          <h5 id="Pkacang">Peyek Kacang</h5>
          <p id="hargaDisplay" style="font-size: 10px;"><strong>Rp50.000/kg</strong></p>
          <p id="toping" class="transparent-text">Toping kacang tanah</p>
          <label for="jumlah" style="font-size: 10px;">Jumlah (kg):</label>
          <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
            <button type="button" class="btn btn-outline-dark" id="minus"
              style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .5rem;">−</button>
            <input type="text" class="form-control form-control-sm" id="jumlah" value="0.5" readonly>
            <button type="button" class="btn btn-outline-dark" id="plus"
              style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .5rem;">＋</button>
          </div>
          <p class="mt-2" id="harga" value="50000"></p>
        </div>

        <button class="btn btn-dark btn-sm mt-2 p-2 mb-2" id="checkout">Checkout</button>
      </div>
    </div>


    <div class="d-flex justify-content-center container p-4 mt-5" id="varCont">
      <div class="row w-100 mb-5" style="max-width: 600px;">
        <h2 id="varianLain" class="mb-4 fw-bold fs-3">Varian Lainnya</h2>
          @foreach($items as $item)
              <div class="col varian-item p-3 mb-4" style="line-height: 22px; cursor: pointer;"
                   data-id="{{ $item->id }}"
                   data-nama="{{ $item->nama_peyek }}"
                   data-harga="{{ $item->hrg_kiloan }}"
                   data-topping="{{ $item->topping }}"
                   data-gambar="{{ asset('img_item_upload/' . $item->gambar) }}">
                  <img src="{{ asset('img_item_upload/' . $item->gambar) }}" class="Vlimg" alt="">
                  <h3 class="mt-3" style="font-size: 13.5px;">{{ $item->nama_peyek }}</h3>
                  <p>
                      <span class="transparent-text">{{ ucfirst($item->topping) }}</span><br>
                      Rp{{ number_format($item->hrg_kiloan, 0, ',', '.') }}/kg
                  </p>
              </div>
        @endforeach
      </div>
    </div>
  </main>
@endsection

@section('script')
    <script>
            const jumlahInput = document.getElementById('jumlah');
            const plusBtn = document.getElementById('plus');
            const minusBtn = document.getElementById('minus');
            const harga = document.getElementById('harga');
            const checkout = document.getElementById('checkout');

            const topImg = document.getElementById('topImg');
            const topTitle = document.getElementById('Pkacang');
            const topHargaDisplay = document.getElementById('hargaDisplay');
            const topTopping = document.getElementById('toping');

            const varianItems = document.querySelectorAll('.varian-item');
            // const selectedId = document.querySelector('.varian-item[data-gambar="' + varianItems.src.split('/').pop() + '"]')?.getAttribute("data-id");

            let jumlah = 0.5;
            let hargaPerKg = 50000;

            function updateHarga() {
              const total = jumlah * hargaPerKg;
              harga.textContent = `Rp${total.toLocaleString('id-ID')}`;
            }

            plusBtn.addEventListener('click', () => {
              if (jumlah < 5) {
                jumlah = parseFloat((jumlah + 0.25).toFixed(2));
                jumlahInput.value = jumlah;
                updateHarga();
              }
            });

            minusBtn.addEventListener('click', () => {
              if (jumlah > 0.25) {
                jumlah = parseFloat((jumlah - 0.25).toFixed(2));
                jumlahInput.value = jumlah;
                updateHarga();
              }
            });

            updateHarga();

            // checkout.addEventListener('click', () => {
            //     const data = {
            //         id_peyek: document.getElementById("topImg").dataset.id,
            //         nama: document.getElementById("Pkacang").textContent,
            //         topping: document.getElementById("toping").textContent,
            //         jumlah: parseFloat(document.getElementById("jumlah").value),
            //         harga: parseInt(document.getElementById("harga").getAttribute("value")),
            //         gambar: document.getElementById("topImg").getAttribute("src"),
            //     };
            //
            //     fetch("set_checkout.php", {
            //         method: "POST",
            //         headers: { "Content-Type": "application/json" },
            //         body: JSON.stringify(data),
            //     }).then(() => {
            //         window.location.href = "../order/index.php";
            //     });
            // });

            // Fungsi ganti varian utama saat diklik
            varianItems.forEach(item => {
              item.addEventListener('click', () => {
                const nama = item.getAttribute('data-nama');
                const hargaBaru = parseInt(item.getAttribute('data-harga'));
                const topping = item.getAttribute('data-topping');
                const gambar = item.getAttribute('data-gambar');
                const idPeyek = item.getAttribute('data-id');

                topImg.src = gambar;
                topImg.dataset.id = idPeyek;
                topTitle.textContent = nama;
                topHargaDisplay.innerHTML = `<strong>Rp${hargaBaru.toLocaleString('id-ID')}/kg</strong>`;
                topTopping.textContent = `Toping ${topping}`;

                hargaPerKg = hargaBaru;
                harga.setAttribute("value", hargaBaru);
                updateHarga();
              });
            });
    </script>
@endsection

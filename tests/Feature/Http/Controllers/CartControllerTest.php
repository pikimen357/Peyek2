<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup data item untuk testing
    $this->item = Item::create([
        'id' => 'plrn',
        'nama_peyek' => 'Peyek Laron',
        'topping' => 'Laron',
        'hrg_kiloan' => 60000,
        'gambar' => 'peyek_kacang.jpg'
    ]);

    $this->item2 = Item::create([
        'id' => 'plkr',
        'nama_peyek' => 'Peyek Kriuk',
        'topping' => 'wewewewe',
        'hrg_kiloan' => 55000,
        'gambar' => 'peyek_kacang.jpg'
    ]);
});

test('berhasil menambahkan item ke cart dengan data valid', function () {
    $response = $this->postJson('/add-to-cart', [
        'item_id' => $this->item->id,
        'berat_kg' => 2.5
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Item berhasil ditambahkan ke keranjang',
            'cart_count' => 1
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'cart',
            'cart_count'
        ]);

    // Verifikasi session cart
    $cart = Session::get('cart');
    expect($cart)->toHaveKey($this->item->id);
    expect($cart[$this->item->id]['berat_kg'])->toBe(2.5);
    expect($cart[$this->item->id]['nama'])->toBe('Peyek Laron');
});

// berhasil menambah 2 item cart
test('beberapa item cart', function () {

    //item 1
    $response1 = $this->postJson('/add-to-cart', [
        'item_id' => 'plrn',
        'berat_kg' => 2.5
    ]);

    // item 2
    $response2 = $this->postJson('/add-to-cart', [
        'item_id' => 'plkr',
        'berat_kg' => 0.1
    ]);

    $response3 = $this->get(route('cart.items'));
    $response3->assertStatus(200)
    ->assertJson([
        'status' => 'success',
        'cart_count' => 2
    ]);

});

test('gagal menambahkan item tanpa berat_kg', function () {
    $response = $this->postJson('/add-to-cart', [
        'item_id' => $this->item->id
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'error',
            'message' => 'Berat tidak valid'
        ]);
});

test('gagal menambahkan item dengan berat_kg kosong', function () {
    $response = $this->postJson('/add-to-cart', [
        'item_id' => $this->item->id,
        'berat_kg' => ''
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'error',
            'message' => 'Berat tidak valid'
        ]);
});

test('gagal menambahkan item dengan berat_kg null', function () {
    $response = $this->postJson('/add-to-cart', [
        'item_id' => $this->item->id,
        'berat_kg' => null
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'error',
            'message' => 'Berat tidak valid'
        ]);
});

test('gagal menambahkan item tanpa item_id', function () {
    $response = $this->postJson('/add-to-cart', [
        'berat_kg' => 2.5
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'status' => 'error',
            'message' => 'Item ID tidak valid'
        ]);
});

test('gagal menambahkan item dengan item_id kosong', function () {
    $response = $this->postJson('/add-to-cart', [
        'item_id' => '',
        'berat_kg' => 2.5
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'status' => 'error',
            'message' => 'Item ID tidak valid'
        ]);
});

test('gagal menambahkan item dengan item_id null', function () {
    $response = $this->postJson('/add-to-cart', [
        'item_id' => null,
        'berat_kg' => 2.5
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'status' => 'error',
            'message' => 'Item ID tidak valid'
        ]);
});

test('gagal menambahkan item yang tidak ditemukan', function () {
    $response = $this->postJson('/add-to-cart', [
        'item_id' => 'ppld', // ID yang tidak ada
        'berat_kg' => 2.5
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'status' => 'error',
            'message' => 'Item tidak ditemukan'
        ]);
});

test('berhasil menambahkan berat ke item yang sudah ada di cart', function () {
    // Setup: tambahkan item pertama kali
    Session::put('cart', [
        $this->item->id => [
            'id' => $this->item->id,
            'nama' => $this->item->nama_peyek,
            'topping' => $this->item->topping,
            'harga' => $this->item->hrg_kiloan,
            'gambar' => asset('img_item_upload/' . $this->item->gambar),
            'berat_kg' => 1.5,
        ]
    ]);

    // Tambahkan berat lagi
    $response = $this->postJson('/add-to-cart', [
        'item_id' => $this->item->id,
        'berat_kg' => 2.5
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Item berhasil ditambahkan ke keranjang',
            'cart_count' => 1
        ]);

    // Verifikasi berat bertambah
    $cart = Session::get('cart');
    expect($cart[$this->item->id]['berat_kg'])->toBe(4.0); // 1.5 + 2.5
});

test('struktur data cart sesuai dengan ekspektasi', function () {
    $response = $this->postJson('/add-to-cart', [
        'item_id' => $this->item->id,
        'berat_kg' => 1.5
    ]);

    $response->assertStatus(200);

    $cart = Session::get('cart');
    $cartItem = $cart[$this->item->id];

    expect($cartItem)->toHaveKeys([
        'id', 'nama', 'topping', 'harga', 'gambar', 'berat_kg'
    ]);

    expect($cartItem['id'])->toBe($this->item->id);
    expect($cartItem['nama'])->toBe($this->item->nama_peyek);
    expect($cartItem['topping'])->toBe($this->item->topping);
    expect($cartItem['harga'])->toBe($this->item->hrg_kiloan);
    expect($cartItem['gambar'])->toContain('img_item_upload/' . $this->item->gambar);
    expect($cartItem['berat_kg'])->toBe(1.5);
});

test('cart count bertambah sesuai dengan jumlah item berbeda', function () {
    // Buat item kedua
    $item2 = Item::create([
        'id' => 'pudg',
        'nama_peyek' => 'Peyek Udang',
        'topping' => 'Udang',
        'hrg_kiloan' => 75000,
        'gambar' => 'peyek_udang.jpg'
    ]);

    // Tambahkan item pertama
    $this->postJson('/add-to-cart', [
        'item_id' => $this->item->id,
        'berat_kg' => 1.0
    ]);

    // Tambahkan item kedua
    $response = $this->postJson('/add-to-cart', [
        'item_id' => $item2->id,
        'berat_kg' => 2.0
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'cart_count' => 2
        ]);
});

test('session cart kosong jika belum ada item', function () {
    // Pastikan session kosong
    Session::forget('cart');

    $response = $this->postJson('/add-to-cart', [
        'item_id' => $this->item->id,
        'berat_kg' => 1.0
    ]);

    $response->assertStatus(200);

    $cart = Session::get('cart');
    expect($cart)->toHaveCount(1);
    expect($cart)->toHaveKey($this->item->id);
});

// test kosongkan keranjang
test('mengosongkan keranjang', function () {
    $response = $this->postJson('/add-to-cart', [
        'item_id' => $this->item->id,
        'berat_kg' => 1.0
    ]);

    Session::forget('cart');

    expect(Session::get('cart'))->toBeNull();

});

//test update invalid $item_id
test('update invalid item_id', function () {
    $response = $this->postJson(route('cart.update'), [
        'item_id' => 0,
        'berat_kg' => 1.0
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Data tidak valid',
        ]);
});

test('update invalid berat_kg', function () {
    $response = $this->postJson(route('cart.update'), [
        'item_id' => $this->item->id,
        'berat_kg' => -3
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Data tidak valid',
        ]);
});

// test Item tidak ditemukan
test('update invalid itemid', function () {
    $response = $this->postJson(route('cart.update'), [
        'item_id' => 'ppka',
        'berat_kg' => 3
    ]);

    $response->assertStatus(404)
    ->assertJson([
        'message' => 'Item tidak ditemukan di keranjang',
    ]);
});

//test update berat
test('update berat_kg ok', function () {
    $response1 = $this->postJson(route('cart.add'), [
        'item_id' => $this->item->id,
        'berat_kg' => 2
    ]);

    $response2 = $this->postJson(route('cart.update'), [
        'item_id' => $this->item->id,
        'berat_kg' => 3.5
    ]);

    // ambil data session cart
    $cart = Session::get('cart');
    $cartItem = $cart[$this->item->id];

    expect($cart[$this->item->id]['berat_kg'])->toBe(3.5);

    $response2->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Jumlah berhasil diupdate',
        ]);
});



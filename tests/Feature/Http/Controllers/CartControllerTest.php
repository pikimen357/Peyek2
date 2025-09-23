<?php

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

//test('addToCartTest', function () {
//    $response = $this->post('/add-to-cart', [
//        'item_id' => 'pkcg',
//        'berat_kg' => 0.5,
//    ]);
//
//    $response->assertStatus(200);
//});

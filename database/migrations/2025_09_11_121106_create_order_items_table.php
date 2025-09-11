<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // id (auto increment)
            $table->unsignedBigInteger('order_id'); // relasi ke orders
            $table->string('item_id');  // relasi ke items (id_peyek)
            $table->decimal('jumlah_kg', 4, 2);
            $table->integer('harga_per_kg');
            $table->unsignedBigInteger('total_harga');
            $table->timestamps();

            // foreign key (kalau tabel orders & items sudah dibuat)
             $table->foreign('order_id')->references('id')
                 ->on('orders')->onDelete('cascade');

             $table->foreign('item_id')->references('id')
                 ->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

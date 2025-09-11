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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('location_id', 100);
            $table->string('no_order')->unique();
            $table->enum('status', ['belum bayar', 'diproses', 'selesai'])
                ->default('belum bayar');
            $table->enum('payment_method', ['cash', 'qris']);
            $table->text('catatan')->nullable();
            $table->text("detail_alamat");
            $table->unsignedInteger('ongkir');
            $table->integer('subtotal');
            $table->timestamps();
            $table->dateTime('tanggal_selesai')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('location_id')->references('id')->on('locations');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

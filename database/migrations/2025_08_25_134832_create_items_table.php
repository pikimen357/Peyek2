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
        Schema::create('items', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('nama_peyek', 50);          // varchar(50) not null
            $table->string('topping', 50)->nullable(); // varchar(50) nullable
            $table->bigInteger('hrg_kiloan');             // int not null
            $table->string('gambar', 200)->nullable(); // varchar(200) nullable
            $table->text('deskripsi')->nullable();     // text nullable
            $table->boolean('is_available')->default(true);
            $table->softDeletes();

            //  timestamps (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes, HasFactory;

    // 1. Tentukan nama tabel jika berbeda dari plural model name
        protected $table = 'items'; // sesuaikan dengan nama tabel Anda

    // 2. PENTING: Tentukan primary key yang custom
        protected $primaryKey = 'id';

    // 3. SANGAT PENTING: Set ke false karena primary key bukan auto-increment integer
    public $incrementing = false;

    // 4. PENTING: Tentukan tipe data primary key
    protected $keyType = 'string';

    // 5. Jika Anda tidak menggunakan timestamps Laravel, set false
    public $timestamps = true;

    protected $fillable = [
        'id',
        'nama_peyek',
        'topping',
        'hrg_kiloan',
        'gambar',
        'deskripsi',
        'is_available',
        'created_at',
        'updated_at',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array{
        return [
            'is_available' => 'boolean',
            'hrg_kiloan' => 'integer',
        ];
    }

        /**
     * Relasi ke OrderItem
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'item_id');
    }
}

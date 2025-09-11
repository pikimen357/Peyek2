<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
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

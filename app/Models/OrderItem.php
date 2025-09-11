<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_id',
        'jumlah_kg',
        'harga_per_kg',
        'total_harga',
    ];

    /**
     * Relasi ke Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }
}

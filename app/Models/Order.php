<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'no_order',
        'status',
        'payment_method',
        'catatan',
        'detail_alamat',
        'ongkir',
        'subtotal',
        'tanggal_selesai',
    ];

    /**
     * Casting kolom agar lebih mudah digunakan
     */
    protected $casts = [
        'ongkir' => 'integer',
        'subtotal' => 'integer',
        'tanggal_selesai' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Location
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Relasi ke OrderItem
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}

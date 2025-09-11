<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'desa',
        'kecamatan',
        'jarak',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array{
        return [
            'jarak' => 'integer',
        ];
    }

    public function orders(){
        return $this->hasMany(Order::class, 'location_id');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->insert([
            [
                'id'         => 'pkcg',
                'nama_peyek'=> 'Peyek Kancang',
                'topping'   => 'kacang tanah',
                'hrg_kiloan'=> 50000,
                'gambar'    => null,
                'deskripsi' => 'Peyek dengan perpaduan khas antara bumbu dengan topping kacang yang gurih membuat anda ketagihan',
                'is_available' => true,
            ],
            [
                'id'         => 'pkdl',
                'nama_peyek'=> 'Peyek Kedelai',
                'topping'   => 'kacang kedelai',
                'hrg_kiloan'=> 52000,
                'gambar'    => null,
                'deskripsi' => 'Varian toping paling banyak dipesan karena citarasa kedelai lokal yang gurih dan renyah',
                'is_available' => true,
            ],
            [
                'id'         => 'pkhj',
                'nama_peyek'=> 'Peyek Kacang Hijau',
                'topping'   => 'kacang hijau',
                'hrg_kiloan'=> 52000,
                'gambar'    => null,
                'deskripsi' => 'Varian toping paling banyak dipesan karena citarasa kacang hijau berkualitas yang lezat',
                'is_available' => true,
            ],
            [
                'id'         => 'ptr',
                'nama_peyek'=> 'Peyek Teri',
                'topping'   => 'ikan teri',
                'hrg_kiloan'=> 56000,
                'gambar'    => null,
                'deskripsi' => 'Ikan Teri yang gurih merupakan kombinasi yang lezat ketika dipadukan dengan bumbu tradisional.',
                'is_available' => true,
            ],
            [
                'id'         => 'pur',
                'nama_peyek'=> 'Peyek Rebon',
                'topping'   => 'udang rebon',
                'hrg_kiloan'=> 60000,
                'gambar'    => null,
                'deskripsi' => 'Citarasa asin gurih yang dihasilkan dari udang rebon akan membuat lidah terasa bergoyang dengan rasanya',
                'is_available' => true,
            ],
        ]);
    }
}

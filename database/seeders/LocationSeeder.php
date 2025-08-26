<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'id' => 'bgk',
                'desa' => 'Bugelan',
                'kecamatan' => 'Kismantoro',
                'jarak' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'bit_pur',
                'desa' => 'Biting',
                'kecamatan' => 'Purwantoro',
                'jarak' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'bkl_pur',
                'desa' => 'Bakalan',
                'kecamatan' => 'Purwantoro',
                'jarak' => 7,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'bsr',
                'desa' => 'Bangsri',
                'kecamatan' => 'Purwantoro',
                'jarak' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'gbr_kis',
                'desa' => 'Gambiranom',
                'kecamatan' => 'Kismantoro',
                'jarak' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'gdk',
                'desa' => 'Gedawung',
                'kecamatan' => 'Kismantoro',
                'jarak' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'gdn_pur',
                'desa' => 'Gondang',
                'kecamatan' => 'Purwantoro',
                'jarak' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'gsg_kis',
                'desa' => 'Gesing',
                'kecamatan' => 'Kismantoro',
                'jarak' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'jho_pur',
                'desa' => 'Joho',
                'kecamatan' => 'Purwantoro',
                'jarak' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'knt_pur',
                'desa' => 'Kenteng',
                'kecamatan' => 'Purwantoro',
                'jarak' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('locations')->insert($locations);
    }
}

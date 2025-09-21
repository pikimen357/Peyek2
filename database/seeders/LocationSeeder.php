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

            // Data Kecamatan Kismantoro
            [
                'id' => 'bgk',
                'desa' => 'Bugelan',
                'kecamatan' => 'Kismantoro',
                'jarak' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'gbr_kis',
                'desa' => 'Gambiranom',
                'kecamatan' => 'Kismantoro',
                'jarak' => 3,
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
                'id' => 'gsg_kis',
                'desa' => 'Gesing',
                'kecamatan' => 'Kismantoro',
                'jarak' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'lmh_kis',
                'desa' => 'Lemahbang',
                'kecamatan' => 'Kismantoro',
                'jarak' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'mri_kis',
                'desa' => 'Miri',
                'kecamatan' => 'Kismantoro',
                'jarak' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'ngr_kis',
                'desa' => 'Ngroto',
                'kecamatan' => 'Kismantoro',
                'jarak' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'plr_kis',
                'desa' => 'Plosorejo',
                'kecamatan' => 'Kismantoro',
                'jarak' => 7,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'pcn_kis',
                'desa' => 'Pucung',
                'kecamatan' => 'Kismantoro',
                'jarak' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Data Kecamatan Purwantoro

            [
                'id' => 'kpy_pur',
                'desa' => 'Kepyar',
                'kecamatan' => 'Purwantoro',
                'jarak' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'mrc_pur',
                'desa' => 'Miricinde',
                'kecamatan' => 'Purwantoro',
                'jarak' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'pls_pur',
                'desa' => 'Ploso',
                'kecamatan' => 'Purwantoro',
                'jarak' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'sdd_pur',
                'desa' => 'Sendang',
                'kecamatan' => 'Purwantoro',
                'jarak' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'skm_pur',
                'desa' => 'Sukomangu',
                'kecamatan' => 'Purwantoro',
                'jarak' => 7,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'smr_pur',
                'desa' => 'Sumber',
                'kecamatan' => 'Purwantoro',
                'jarak' => 7,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'tls_pur',
                'desa' => 'Talesan',
                'kecamatan' => 'Purwantoro',
                'jarak' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],

            //Bulukerto

            [
                'id' => 'blr_bkt',
                'desa' => 'Bulurejo',
                'kecamatan' => 'Bulukerto',
                'jarak' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'cto_bkt',
                'desa' => 'Conto',
                'kecamatan' => 'Bulukerto',
                'jarak' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'dms_bkt',
                'desa' => 'Domas',
                'kecamatan' => 'Bulukerto',
                'jarak' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'gng_bkt',
                'desa' => 'Geneng',
                'kecamatan' => 'Bulukerto',
                'jarak' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'krd_bkt',
                'desa' => 'Krandegan',
                'kecamatan' => 'Bulukerto',
                'jarak' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'ndi_bkt',
                'desa' => 'Nadi',
                'kecamatan' => 'Bulukerto',
                'jarak' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'ngl_bkt',
                'desa' => 'Ngaglik',
                'kecamatan' => 'Bulukerto',
                'jarak' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'sgh_bkt',
                'desa' => 'Sugihan',
                'kecamatan' => 'Bulukerto',
                'jarak' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'tjg_bkt',
                'desa' => 'Tanjung',
                'kecamatan' => 'Bulukerto',
                'jarak' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ]

        ];

        DB::table('locations')->insert($locations);
    }
}

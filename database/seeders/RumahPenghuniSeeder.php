<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RumahPenghuni;

class RumahPenghuniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RumahPenghuni::create([
            'rumah_id' => 1,
            'penghuni_id' => 1,
            'tanggal_masuk' => '2022-01-01',
            'tanggal_keluar' => null,
        ]);
        RumahPenghuni::create([
            'rumah_id' => 2,
            'penghuni_id' => 2,
            'tanggal_masuk' => '2023-03-01',
            'tanggal_keluar' => '2023-12-31',
        ]);
    }
}

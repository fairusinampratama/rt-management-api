<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RumahPenghuni;
use App\Models\Rumah;
use App\Models\Penghuni;

class RumahPenghuniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        RumahPenghuni::query()->delete();

        // Assign 15 penghuni tetap to rumah 1-15
        for ($i = 1; $i <= 15; $i++) {
            $tanggalMasuk = date('Y-m-d', strtotime('-' . rand(365, 1825) . ' days'));
            RumahPenghuni::create([
                'rumah_id' => $i,
                'penghuni_id' => $i,
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_keluar' => null,
            ]);
        }
        // Assign 5 penghuni kontrak to rumah 16-20
        for ($i = 16; $i <= 20; $i++) {
            $tanggalMasuk = date('Y-m-d', strtotime('-' . rand(30, 365) . ' days'));
            $tanggalKeluar = date('Y-m-d', strtotime($tanggalMasuk . ' +' . rand(30, 180) . ' days'));
            RumahPenghuni::create([
                'rumah_id' => $i,
                'penghuni_id' => $i,
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_keluar' => $tanggalKeluar,
            ]);
        }
    }
}

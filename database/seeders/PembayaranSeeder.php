<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pembayaran;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pembayaran::create([
            'rumah_id' => 1,
            'penghuni_id' => 1,
            'bulan' => 1,
            'tahun' => 2024,
            'jenis_iuran' => 'satpam',
            'jumlah' => 100000,
            'status' => 1,
            'tanggal_bayar' => '2024-01-05',
        ]);
        Pembayaran::create([
            'rumah_id' => 1,
            'penghuni_id' => 1,
            'bulan' => 1,
            'tahun' => 2024,
            'jenis_iuran' => 'kebersihan',
            'jumlah' => 15000,
            'status' => 1,
            'tanggal_bayar' => '2024-01-05',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengeluaran;

class PengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengeluaran::create([
            'nama_pengeluaran' => 'Gaji Satpam',
            'jumlah' => 2000000,
            'bulan' => 1,
            'tahun' => 2024,
            'tanggal_pengeluaran' => '2024-01-31',
            'keterangan' => 'Gaji 2 satpam Januari',
        ]);
        Pengeluaran::create([
            'nama_pengeluaran' => 'Token Listrik Pos Satpam',
            'jumlah' => 300000,
            'bulan' => 1,
            'tahun' => 2024,
            'tanggal_pengeluaran' => '2024-01-20',
            'keterangan' => 'Listrik pos satpam Januari',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use App\Models\Iuran;

class PengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengeluaran::query()->delete();
        $year = 2025;

        // Pengeluaran bulanan tetap
        for ($month = 1; $month <= 12; $month++) {
            Pengeluaran::create([
                'nama_pengeluaran' => 'Gaji Satpam',
                'jumlah' => 1500000,
                'bulan' => $month,
                'tahun' => $year,
                'tanggal_pengeluaran' => "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01",
                'keterangan' => 'Pembayaran gaji satpam bulanan',
            ]);
            Pengeluaran::create([
                'nama_pengeluaran' => 'Listrik Pos Satpam',
                'jumlah' => 300000,
                'bulan' => $month,
                'tahun' => $year,
                'tanggal_pengeluaran' => "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-02",
                'keterangan' => 'Pembayaran listrik pos satpam',
            ]);
        }

        // Pengeluaran insidental (acak)
        $insidentals = [
            ['Perbaikan Jalan', 2000000, 'Perbaikan jalan utama'],
            ['Perbaikan Selokan', 1200000, 'Perbaikan saluran air'],
            ['Pembelian Alat Kebersihan', 500000, 'Pembelian alat kebersihan'],
        ];
        foreach ($insidentals as $item) {
            $bulan = rand(1, 12);
            Pengeluaran::create([
                'nama_pengeluaran' => $item[0],
                'jumlah' => $item[1],
                'bulan' => $bulan,
                'tahun' => $year,
                'tanggal_pengeluaran' => "$year-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-10",
                'keterangan' => $item[2],
            ]);
        }
    }

}

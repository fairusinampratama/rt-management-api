<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Penghuni;

class PenghuniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Penghuni::query()->delete();

        // 15 penghuni tetap
        for ($i = 1; $i <= 15; $i++) {
            Penghuni::create([
                'nama_lengkap' => 'Penghuni Tetap ' . $i,
                'foto_ktp' => 'ktp_' . $i . '.jpg',
                'status_penghuni' => 1,
                'nomor_telepon' => '08' . rand(100000000, 999999999),
                'status_menikah' => rand(0, 1),
            ]);
        }
        // 5 penghuni kontrak
        for ($i = 16; $i <= 20; $i++) {
            Penghuni::create([
                'nama_lengkap' => 'Penghuni Kontrak ' . ($i-15),
                'foto_ktp' => 'ktp_' . $i . '.jpg',
                'status_penghuni' => 0,
                'nomor_telepon' => '08' . rand(100000000, 999999999),
                'status_menikah' => rand(0, 1),
            ]);
        }
    }
}

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
        Penghuni::create([
            'nama_lengkap' => 'Budi Santoso',
            'foto_ktp' => 'budi.jpg',
            'status_penghuni' => 1,
            'nomor_telepon' => '081234567890',
            'status_menikah' => 1,
        ]);
        Penghuni::create([
            'nama_lengkap' => 'Siti Aminah',
            'foto_ktp' => 'siti.jpg',
            'status_penghuni' => 0,
            'nomor_telepon' => '081298765432',
            'status_menikah' => 0,
        ]);
    }
}

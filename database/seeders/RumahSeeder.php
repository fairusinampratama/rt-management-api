<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rumah;

class RumahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rumah::create(['nomor_rumah' => 'A1', 'status' => 1]);
        Rumah::create(['nomor_rumah' => 'A2', 'status' => 0]);
        Rumah::create(['nomor_rumah' => 'A3', 'status' => 2]);
        Rumah::create(['nomor_rumah' => 'A4', 'status' => 1]);
        Rumah::create(['nomor_rumah' => 'A5', 'status' => 0]);
    }
}
